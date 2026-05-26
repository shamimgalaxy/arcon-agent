<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\SpendingPolicy;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'circle_wallet_id',
        'circle_wallet_address',
        'blockchain',
        'status',
    ];

   public function spendingPolicy()
{
    return $this->hasOne(SpendingPolicy::class);
}

public function auditLogs()
{
    return $this->hasMany(AuditLog::class);
}
// app/Models/Agent.php — add this method
public function triggers()
{
    return $this->hasMany(AgentTrigger::class);
}
}