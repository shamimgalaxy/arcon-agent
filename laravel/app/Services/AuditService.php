 <?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AuditLog;
use App\Models\Payment;

class AuditService
{
    // Generic logger
    public static function log(array $data): AuditLog
    {
        return AuditLog::create(array_merge([
            'currency'   => 'USDC',
            'blockchain' => 'BASE-SEPOLIA',
            'source'     => 'api',
            'ip_address' => request()?->ip(),
        ], $data));
    }

    // Payment attempt (before Circle API call)
    public static function paymentAttempt(Agent $sender, Agent $receiver, float $amount, ?string $note = null, ?string $batchId = null): AuditLog
    {
        return self::log([
            'agent_id'         => $sender->id,
            'agent_name'       => $sender->name,
            'event_type'       => 'payment_attempt',
            'event_status'     => 'pending',
            'amount'           => $amount,
            'sender_address'   => $sender->circle_wallet_address,
            'receiver_address' => $receiver->circle_wallet_address,
            'batch_id'         => $batchId,
            'meta'             => [
                'receiver_agent_id'   => $receiver->id,
                'receiver_agent_name' => $receiver->name,
                'note'                => $note,
            ],
        ]);
    }

    // Payment submitted to Circle successfully
    public static function paymentSubmitted(Payment $payment): AuditLog
    {
        return self::log([
            'agent_id'              => $payment->sender_agent_id,
            'agent_name'            => $payment->senderAgent->name ?? null,
            'event_type'            => 'payment_submitted',
            'event_status'          => 'success',
            'payment_id'            => $payment->id,
            'circle_transaction_id' => $payment->circle_transaction_id,
            'batch_id'              => $payment->batch_id,
            'amount'                => $payment->amount,
            'sender_address'        => $payment->senderAgent->circle_wallet_address ?? null,
            'receiver_address'      => $payment->receiverAgent->circle_wallet_address ?? null,
        ]);
    }

    // Payment failed
    public static function paymentFailed(Agent $sender, Agent $receiver, float $amount, string $reason, ?string $batchId = null): AuditLog
    {
        return self::log([
            'agent_id'         => $sender->id,
            'agent_name'       => $sender->name,
            'event_type'       => 'payment_failed',
            'event_status'     => 'failed',
            'amount'           => $amount,
            'sender_address'   => $sender->circle_wallet_address,
            'receiver_address' => $receiver->circle_wallet_address,
            'batch_id'         => $batchId,
            'meta'             => [
                'reason'              => $reason,
                'receiver_agent_id'   => $receiver->id,
                'receiver_agent_name' => $receiver->name,
            ],
        ]);
    }

    // Payment confirmed via webhook
    public static function paymentConfirmed(Payment $payment): AuditLog
    {
        return self::log([
            'agent_id'              => $payment->sender_agent_id,
            'agent_name'            => $payment->senderAgent->name ?? null,
            'event_type'            => 'payment_confirmed',
            'event_status'          => 'success',
            'payment_id'            => $payment->id,
            'circle_transaction_id' => $payment->circle_transaction_id,
            'batch_id'              => $payment->batch_id,
            'amount'                => $payment->amount,
            'source'                => 'webhook',
        ]);
    }

    // Policy violation
    public static function policyViolation(Agent $sender, Agent $receiver, float $amount, string $rule, string $detail, ?string $batchId = null): AuditLog
    {
        return self::log([
            'agent_id'         => $sender->id,
            'agent_name'       => $sender->name,
            'event_type'       => 'policy_violation',
            'event_status'     => 'blocked',
            'amount'           => $amount,
            'sender_address'   => $sender->circle_wallet_address,
            'receiver_address' => $receiver->circle_wallet_address,
            'batch_id'         => $batchId,
            'policy_rule'      => $rule,
            'policy_detail'    => $detail,
            'meta'             => [
                'receiver_agent_id'   => $receiver->id,
                'receiver_agent_name' => $receiver->name,
            ],
        ]);
    }

    // Batch started
    public static function batchStarted(Agent $sender, string $batchId, int $totalPayments, float $totalAmount): AuditLog
    {
        return self::log([
            'agent_id'     => $sender->id,
            'agent_name'   => $sender->name,
            'event_type'   => 'batch_payment',
            'event_status' => 'pending',
            'batch_id'     => $batchId,
            'amount'       => $totalAmount,
            'meta'         => [
                'total_payments' => $totalPayments,
                'total_amount'   => $totalAmount,
            ],
        ]);
    }

    // Batch completed
    public static function batchCompleted(Agent $sender, string $batchId, int $succeeded, int $failed, int $blocked, float $totalSent): AuditLog
    {
        return self::log([
            'agent_id'     => $sender->id,
            'agent_name'   => $sender->name,
            'event_type'   => 'batch_payment',
            'event_status' => $failed + $blocked > 0 ? 'partial' : 'success',
            'batch_id'     => $batchId,
            'amount'       => $totalSent,
            'meta'         => [
                'succeeded'  => $succeeded,
                'failed'     => $failed,
                'blocked'    => $blocked,
                'total_sent' => $totalSent,
            ],
        ]);
    }

    // Agent registered
    public static function agentRegistered(Agent $agent): AuditLog
    {
        return self::log([
            'agent_id'     => $agent->id,
            'agent_name'   => $agent->name,
            'event_type'   => 'agent_registered',
            'event_status' => 'success',
            'meta'         => [
                'wallet_address' => $agent->circle_wallet_address,
                'blockchain'     => $agent->blockchain,
            ],
        ]);
    }

    // Webhook received
    public static function webhookReceived(string $eventType, array $payload, ?Payment $payment = null): AuditLog
    {
        return self::log([
            'event_type'            => 'webhook_received',
            'event_status'          => 'success',
            'payment_id'            => $payment?->id,
            'circle_transaction_id' => $payload['transactionId'] ?? null,
            'source'                => 'webhook',
            'meta'                  => [
                'circle_event' => $eventType,
                'payload'      => $payload,
            ],
        ]);
    }
}
