<?php

namespace App\Events;

use App\Models\AuditLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AuditLogCreated implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public AuditLog $log) {}

    public function broadcastOn(): array
    {
        return [new Channel('audit-logs')];
    }

    public function broadcastAs(): string
    {
        return 'AuditLogCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'id'           => $this->log->id,
            'agent_id'     => $this->log->agent_id,
            'agent_name'   => $this->log->agent_name,
            'event_type'   => $this->log->event_type,
            'event_status' => $this->log->event_status,
            'amount'       => $this->log->amount,
            'currency'     => $this->log->currency,
            'blockchain'   => $this->log->blockchain,
            'policy_rule'  => $this->log->policy_rule,
            'policy_detail'=> $this->log->policy_detail,
            'time'         => $this->log->created_at->toDateTimeString(),
        ];
    }
}