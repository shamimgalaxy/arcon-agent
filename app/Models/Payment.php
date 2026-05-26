<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'sender_agent_id',
        'receiver_agent_id',
        'amount',
        'currency',
        'circle_transaction_id',
        'status',
        'blockchain',
        'note',
        'batch_id',
        'batch_index',
    ];

    protected $casts = [
        'amount' => 'decimal:6',
    ];

    // Relationships
    public function senderAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'sender_agent_id');
    }

    public function receiverAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'receiver_agent_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // Scopes
    public function scopeForBatch($query, string $batchId)
    {
        return $query->where('batch_id', $batchId);
    }
}