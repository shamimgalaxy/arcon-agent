<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('agent')
            ->when($request->agent_id,    fn($q) => $q->where('agent_id', $request->agent_id))
            ->when($request->event_type,  fn($q) => $q->where('event_type', $request->event_type))
            ->when($request->event_status,fn($q) => $q->where('event_status', $request->event_status))
            ->latest()
            ->paginate($request->per_page ?? 50);

        return response()->json($logs);
    }
}