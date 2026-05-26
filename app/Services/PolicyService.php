<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;

class PolicyService
{
    public function enforce(Agent $agent, float $amount, string $toWalletId): void
    {
        $policy = $agent->spendingPolicy;

        if (!$policy || !$policy->is_active) {
            return; // no policy = no restriction
        }

        // Max per transaction
        if ($policy->max_per_transaction && $amount > $policy->max_per_transaction) {
            throw new Exception("Exceeds max per transaction limit of {$policy->max_per_transaction} USDC.");
        }

        // Daily limit
        if ($policy->daily_limit) {
            $todayTotal = Payment::where('agent_id', $agent->id)
                ->whereDate('created_at', Carbon::today())
                ->where('status', '!=', 'failed')
                ->sum('amount');

            if (($todayTotal + $amount) > $policy->daily_limit) {
                throw new Exception("Exceeds daily limit of {$policy->daily_limit} USDC.");
            }
        }

        // Monthly limit
        if ($policy->monthly_limit) {
            $monthTotal = Payment::where('agent_id', $agent->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', '!=', 'failed')
                ->sum('amount');

            if (($monthTotal + $amount) > $policy->monthly_limit) {
                throw new Exception("Exceeds monthly limit of {$policy->monthly_limit} USDC.");
            }
        }

        // Whitelist check
        if ($policy->allowed_wallet_ids) {
            $allowed = json_decode($policy->allowed_wallet_ids, true);
            if (!empty($allowed) && !in_array($toWalletId, $allowed)) {
                throw new Exception("Recipient wallet not in allowed list.");
            }
        }
    }
}