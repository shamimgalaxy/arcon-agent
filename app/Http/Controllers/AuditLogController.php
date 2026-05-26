<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // GET /api/audit-logs
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::with('agent')
            ->recent($request->integer('days', 30))
            ->orderByDesc('created_at');

        if ($request->filled('agent_id')) {
            $query->forAgent((int) $request->agent_id);
        }

        if ($request->filled('event_type')) {
            $query->byEvent($request->event_type);
        }

        if ($request->filled('event_status')) {
            $query->where('event_status', $request->event_status);
        }

        if ($request->filled('batch_id')) {
            $query->forBatch($request->batch_id);
        }

        $perPage = min((int) $request->get('per_page', 50), 200);
        $logs    = $query->paginate($perPage);

        return response()->json($logs);
    }

    // GET /api/audit-logs/{id}
    public function show(int $id): JsonResponse
    {
        $log = AuditLog::with(['agent', 'payment'])->findOrFail($id);

        return response()->json($log);
    }

    // GET /api/audit-logs/policy-violations
    public function policyViolations(Request $request): JsonResponse
    {
        $logs = AuditLog::policyViolations()
            ->with('agent')
            ->recent($request->integer('days', 30))
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json($logs);
    }

    // GET /api/audit-logs/agent/{agentId}
    public function forAgent(int $agentId, Request $request): JsonResponse
    {
        $logs = AuditLog::forAgent($agentId)
            ->recent($request->integer('days', 30))
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json($logs);
    }

    // GET /api/audit-logs/stats
    public function stats(Request $request): JsonResponse
    {
        $days = $request->integer('days', 7);
        $base = AuditLog::recent($days);

        return response()->json([
            'period_days'        => $days,
            'total_events'       => (clone $base)->count(),
            'payments_submitted' => (clone $base)->byEvent('payment_submitted')->count(),
            'payments_confirmed' => (clone $base)->byEvent('payment_confirmed')->count(),
            'payments_failed'    => (clone $base)->byEvent('payment_failed')->count(),
            'policy_violations'  => (clone $base)->policyViolations()->count(),
            'batch_payments'     => (clone $base)->byEvent('batch_payment')->where('event_status', 'success')->count(),
            'total_usdc_sent'    => (clone $base)->byEvent('payment_submitted')->sum('amount'),
        ]);
    }
}