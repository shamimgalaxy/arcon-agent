<?php
// app/Http/Controllers/PolicyController.php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\SpendingPolicy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    // GET /api/agents/{agent}/policy
    public function show(Agent $agent)
    {
        $policy = $agent->spendingPolicy;

        if (!$policy) {
            return response()->json(['message' => 'No policy found for this agent.'], 404);
        }

        return response()->json($policy);
    }

    // POST /api/agents/{agent}/policy
    public function store(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'max_per_transaction' => 'nullable|numeric|min:0',
            'daily_limit'         => 'nullable|numeric|min:0',
            'monthly_limit'       => 'nullable|numeric|min:0',
            'allowed_receivers'   => 'nullable|array',
            'allowed_receivers.*' => 'string',
            'blocked_receivers'   => 'nullable|array',
            'blocked_receivers.*' => 'string',
            'is_active'           => 'boolean',
        ]);

        if ($agent->spendingPolicy) {
            return response()->json([
                'message' => 'Policy already exists. Use PUT to update.',
            ], 409);
        }

        $policy = $agent->spendingPolicy()->create($validated);

        return response()->json([
            'message' => 'Policy created successfully.',
            'policy'  => $policy,
        ], 201);
    }

    // PUT /api/agents/{agent}/policy
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'max_per_transaction' => 'nullable|numeric|min:0',
            'daily_limit'         => 'nullable|numeric|min:0',
            'monthly_limit'       => 'nullable|numeric|min:0',
            'allowed_receivers'   => 'nullable|array',
            'allowed_receivers.*' => 'string',
            'blocked_receivers'   => 'nullable|array',
            'blocked_receivers.*' => 'string',
            'is_active'           => 'boolean',
        ]);

        $policy = $agent->spendingPolicy;

        if (!$policy) {
            // Auto-create if not exists
            $policy = $agent->spendingPolicy()->create($validated);
            return response()->json([
                'message' => 'Policy created (none existed).',
                'policy'  => $policy,
            ], 201);
        }

        $policy->update($validated);

        return response()->json([
            'message' => 'Policy updated successfully.',
            'policy'  => $policy,
        ]);
    }

    // DELETE /api/agents/{agent}/policy
    public function destroy(Agent $agent)
    {
        $policy = $agent->spendingPolicy;

        if (!$policy) {
            return response()->json(['message' => 'No policy to delete.'], 404);
        }

        $policy->delete();

        return response()->json(['message' => 'Policy deleted successfully.']);
    }
}