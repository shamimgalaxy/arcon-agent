<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('payments'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'payment.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id'                     => $this->payment->id,
            'sender_agent_id'        => $this->payment->sender_agent_id,
            'receiver_agent_id'      => $this->payment->receiver_agent_id,
            'amount'                 => $this->payment->amount,
            'currency'               => $this->payment->currency,
            'status'                 => $this->payment->status,
            'circle_transaction_id'  => $this->payment->circle_transaction_id,
            'blockchain'             => $this->payment->blockchain,
            'note'                   => $this->payment->note,
            'created_at'             => $this->payment->created_at,
        ];
    }
}