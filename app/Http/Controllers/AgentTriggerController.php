<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AgentTrigger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AgentTriggerController extends Controller
{
    /**
     * List all triggers for a given agent.
     */
    public function index(Request $request, Agent $agent): JsonResponse
    {
        $triggers = $agent->triggers()
            ->when($request->query('type'), fn($q, $type) => $q->ofType($type))
            ->when($request->boolean('active_only'), fn($q) => $q->active())
            ->latest()
            ->get();

        return response()->json($triggers);
    }

    /**
     * Create a new trigger for the agent.
     */
    public function store(Request $request, Agent $agent): JsonResponse
    {
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'trigger_type'         => ['required', Rule::in(['balance_threshold', 'scheduled', 'task_event'])],
            'is_active'            => ['boolean'],

            // Balance threshold
            'threshold_amount'     => ['required_if:trigger_type,balance_threshold', 'nullable', 'numeric', 'min:0'],
            'threshold_direction'  => ['required_if:trigger_type,balance_threshold', 'nullable', Rule::in(['above', 'below'])],

            // Scheduled
            'interval_hours'       => ['required_if:trigger_type,scheduled', 'nullable', 'integer', 'min:1'],

            // Task event
            'event_name'           => ['required_if:trigger_type,task_event', 'nullable', 'string', 'max:255'],

            // Payment details
            'receiver_agent_id'    => ['nullable', 'exists:agents,id'],
            'receiver_address'     => ['nullable', 'string', 'max:255'],
            'amount'               => ['required', 'numeric', 'min:0'],
            'currency'             => ['string', 'max:10'],
            'note'                 => ['nullable', 'string'],
        ]);

        $trigger = $agent->triggers()->create($data);

        return response()->json($trigger, 201);
    }

    /**
     * Show a single trigger.
     */
    public function show(Agent $agent, AgentTrigger $trigger): JsonResponse
    {
        $this->authorizeTriggerBelongsToAgent($agent, $trigger);

        return response()->json($trigger);
    }

    /**
     * Update an existing trigger.
     */
    public function update(Request $request, Agent $agent, AgentTrigger $trigger): JsonResponse
    {
        $this->authorizeTriggerBelongsToAgent($agent, $trigger);

        $data = $request->validate([
            'name'                 => ['sometimes', 'string', 'max:255'],
            'trigger_type'         => ['sometimes', Rule::in(['balance_threshold', 'scheduled', 'task_event'])],
            'is_active'            => ['sometimes', 'boolean'],

            // Balance threshold
            'threshold_amount'     => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'threshold_direction'  => ['sometimes', 'nullable', Rule::in(['above', 'below'])],

            // Scheduled
            'interval_hours'       => ['sometimes', 'nullable', 'integer', 'min:1'],

            // Task event
            'event_name'           => ['sometimes', 'nullable', 'string', 'max:255'],

            // Payment details
            'receiver_agent_id'    => ['sometimes', 'nullable', 'exists:agents,id'],
            'receiver_address'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'amount'               => ['sometimes', 'numeric', 'min:0'],
            'currency'             => ['sometimes', 'string', 'max:10'],
            'note'                 => ['sometimes', 'nullable', 'string'],
        ]);

        $trigger->update($data);

        return response()->json($trigger);
    }

    /**
     * Delete a trigger.
     */
    public function destroy(Agent $agent, AgentTrigger $trigger): JsonResponse
    {
        $this->authorizeTriggerBelongsToAgent($agent, $trigger);

        $trigger->delete();

        return response()->json(['message' => 'Trigger deleted successfully.']);
    }

    /**
     * Toggle is_active on a trigger.
     */
    public function toggleActive(Agent $agent, AgentTrigger $trigger): JsonResponse
    {
        $this->authorizeTriggerBelongsToAgent($agent, $trigger);

        $trigger->update(['is_active' => !$trigger->is_active]);

        return response()->json([
            'message'   => 'Trigger ' . ($trigger->is_active ? 'activated' : 'deactivated') . '.',
            'is_active' => $trigger->is_active,
        ]);
    }

    /**
     * Manually fire a trigger (useful for testing).
     */
    public function fire(Agent $agent, AgentTrigger $trigger): JsonResponse
    {
        $this->authorizeTriggerBelongsToAgent($agent, $trigger);

        $receiver = $trigger->resolveReceiverAddress();

        if (!$receiver) {
            return response()->json(['message' => 'No receiver address could be resolved.'], 422);
        }

        $trigger->markFired();

        return response()->json([
            'message'          => 'Trigger fired.',
            'receiver_address' => $receiver,
            'amount'           => $trigger->amount,
            'currency'         => $trigger->currency,
            'fired_count'      => $trigger->fired_count,
            'last_fired_at'    => $trigger->last_fired_at,
        ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function authorizeTriggerBelongsToAgent(Agent $agent, AgentTrigger $trigger): void
    {
        abort_if($trigger->agent_id !== $agent->id, 403, 'Trigger does not belong to this agent.');
    }
}