<?php
// app/Jobs/PollCircleTransaction.php

namespace App\Jobs;

use App\Events\AuditLogCreated;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Services\CircleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PollCircleTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 12;   // max polling attempts
    public int $backoff = 5;    // seconds between retries

    public function __construct(public int $paymentId) {}

    public function handle(CircleService $circle): void
    {
        $payment = Payment::with(['senderAgent', 'receiverAgent'])->findOrFail($this->paymentId);

        // Already terminal — nothing to do
        if (in_array($payment->status, ['confirmed', 'failed', 'cancelled'])) {
            return;
        }

        $response = $circle->getTransaction($payment->circle_transaction_id);
        $state    = $response['data']['transaction']['state'] ?? null;

        // Not terminal yet — re-queue and try again
        if (!in_array($state, ['CONFIRMED', 'COMPLETE', 'FAILED', 'CANCELLED'])) {
            self::dispatch($this->paymentId)->delay(now()->addSeconds(5));
            return;
        }

        // Map Circle state → your status
        $status = match ($state) {
            'CONFIRMED', 'COMPLETE' => 'confirmed',
            'FAILED'                => 'failed',
            'CANCELLED'             => 'cancelled',
            default                 => 'failed',
        };

        $payment->update(['status' => $status]);

        // Broadcast the audit log so dashboard updates in real-time
        $log = AuditLog::create([
            'agent_id'              => $payment->senderAgent->id,
            'agent_name'            => $payment->senderAgent->name,
            'event_type'            => "payment_{$status}",
            'event_status'          => $status,
            'payment_id'            => $payment->id,
            'circle_transaction_id' => $payment->circle_transaction_id,
            'amount'                => $payment->amount,
            'currency'              => 'USDC',
            'blockchain'            => $payment->blockchain,
            'sender_address'        => $payment->senderAgent->circle_wallet_address,
            'receiver_address'      => $payment->receiverAgent->circle_wallet_address,
            'source'                => 'system',
        ]);

        broadcast(new AuditLogCreated($log))->toOthers();
    }
}