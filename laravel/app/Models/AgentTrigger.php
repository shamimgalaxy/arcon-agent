<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentTrigger extends Model
{
    protected $fillable = [
        'agent_id',
        'name',
        'trigger_type',
        'is_active',

        // Balance threshold
        'threshold_amount',
        'threshold_direction',

        // Scheduled
        'interval_hours',

        // Task event
        'event_name',

        // Payment details
        'receiver_agent_id',
        'receiver_address',
        'amount',
        'currency',
        'note',

        // Tracking
        'fired_count',
        'last_fired_at',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'threshold_amount'   => 'decimal:6',
        'amount'             => 'decimal:6',
        'fired_count'        => 'integer',
        'last_fired_at'      => 'datetime',
    ];

    // ── Relationships ───────────────────────────────────────────────────────

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function receiverAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'receiver_agent_id');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('trigger_type', $type);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    // Check if scheduled trigger is due to fire
    public function isDue(): bool
    {
        if ($this->trigger_type !== 'scheduled') {
            return false;
        }

        if (!$this->last_fired_at) {
            return true; // never fired — fire immediately
        }

        $nextFireAt = $this->last_fired_at->addHours($this->interval_hours);

        return now()->greaterThanOrEqualTo($nextFireAt);
    }

    // Mark trigger as fired
    public function markFired(): void
    {
        $this->increment('fired_count');
        $this->update(['last_fired_at' => now()]);
    }

    // Resolve final receiver address
    public function resolveReceiverAddress(): ?string
    {
        if ($this->receiver_address) {
            return $this->receiver_address;
        }

        return $this->receiverAgent?->circle_wallet_address;
    }
}