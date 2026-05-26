<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AgentCoordinationService;
use Illuminate\Http\Request;

class CoordinationController extends Controller
{
    public function __construct(protected AgentCoordinationService $coordination) {}

    /**
     * POST /api/v1/coordination/execute
     *
     * Body example:
     * {
     *   "orchestrator_agent_id": 4,
     *   "tasks": [
     *     { "receiver_agent_id": 5, "amount": "0.10", "note": "Task A payment" },
     *     { "receiver_agent_id": 5, "amount": "0.05", "note": "Task B payment", "depends_on": 0 }
     *   ]
     * }
     */
    public function execute(Request $request)
    {
        $data = $request->validate([
            'orchestrator_agent_id'       => 'required|exists:agents,id',
            'tasks'                       => 'required|array|min:1|max:10',
            'tasks.*.receiver_agent_id'   => 'required|exists:agents,id',
            'tasks.*.amount'              => 'required|numeric|min:0.000001',
            'tasks.*.note'                => 'nullable|string|max:255',
            'tasks.*.depends_on'          => 'nullable|integer',
        ]);

        $result = $this->coordination->coordinate($data);

        $status = $result['failed'] === 0 ? 200 : 207; // 207 = partial success

        return response()->json($result, $status);
    }
}