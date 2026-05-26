<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'agent_id', 'agent_name', 'event_type', 'event_status',
        'payment_id', 'circle_transaction_id', 'batch_id',
        'amount', 'currency', 'blockchain',
        'sender_address', 'receiver_address',
        'policy_rule', 'policy_detail', 'meta',
        'ip_address', 'source',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}