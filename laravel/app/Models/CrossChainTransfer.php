<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrossChainTransfer extends Model
{
    protected $fillable = [
        'agent_id', 'source_chain', 'destination_chain',
        'destination_address', 'amount_usdc', 'status',
        'burn_tx_hash', 'attestation', 'message_hex',
        'mint_tx_hash', 'usdc_contract', 'token_messenger',
        'destination_domain',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}