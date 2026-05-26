<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nanopayment extends Model
{
   protected $fillable = [
    'sender_agent_id', 'receiver_agent_id', 'amount',
    'amount_micro', 'currency', 'purpose', 'note',
    'status', 'gateway_payment_id', 'verified_at', 'stream','paymaster_enabled', 'gas_usdc_limit', 'gas_used_usdc',
];

protected $casts = [
    'verified_at' => 'datetime',
    'amount'      => 'float',
    'stream'      => 'boolean',
];

    public function senderAgent()
    {
        return $this->belongsTo(Agent::class, 'sender_agent_id');
    }

    public function receiverAgent()
    {
        return $this->belongsTo(Agent::class, 'receiver_agent_id');
    }
}