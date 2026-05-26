<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpendingPolicy extends Model
{
    protected $fillable = [
        'agent_id', 'max_per_transaction', 'daily_limit',
        'monthly_limit', 'allowed_wallet_ids', 'is_active',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}