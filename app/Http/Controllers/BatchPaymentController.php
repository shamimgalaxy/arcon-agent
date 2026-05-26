<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Services\BatchPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BatchPaymentController extends Controller
{
    public function __construct(protected BatchPaymentService $batchService) {}

    // POST /api/payments/batch
    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sender_agent_id'        => 'required|integer|exists:agents,id',
            'payments'               => 'required|array|min:1|max:50',
            'payments.*.receiver_id' => 'required|integer|exists:agents,id',
            'payments.*.amount'      => 'required|numeric|min:0.000001',
            'payments.*.note'        => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sender = Agent::findOrFail($request->sender_agent_id);

        foreach ($request->payments as $p) {
            if ((int) $p['receiver_id'] === $sender->id) {
                return response()->json(['error' => 'Sender cannot pay themselves.'], 422);
            }
        }

        $result = $this->batchService->process($sender, $request->payments);

        $httpStatus = $result['failed'] > 0 || $result['blocked'] > 0 ? 207 : 200;

        return response()->json([
            'message' => 'Batch processed.',
            'data'    => $result,
        ], $httpStatus);
    }

    // GET /api/payments/batch/{batchId}
    public function show(string $batchId): JsonResponse
    {
        $payments = Payment::with(['senderAgent', 'receiverAgent'])
            ->forBatch($batchId)
            ->orderBy('batch_index')
            ->get();

        $auditLogs = AuditLog::forBatch($batchId)
            ->orderBy('created_at')
            ->get();

        if ($payments->isEmpty() && $auditLogs->isEmpty()) {
            return response()->json(['error' => 'Batch not found.'], 404);
        }

        return response()->json([
            'batch_id'   => $batchId,
            'payments'   => $payments,
            'audit_logs' => $auditLogs,
        ]);
    }
}