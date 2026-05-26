<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Nanopayment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NanopaymentController extends Controller
{
    /**
     * POST /api/v1/nano/send
     */
    public function send(Request $request)
    {
        $request->validate([
            'sender_agent_id'   => 'required|integer|exists:agents,id',
            'receiver_agent_id' => 'required|integer|exists:agents,id|different:sender_agent_id',
            'amount'            => 'nullable|numeric|min:0.000001',
            'amount_micro'      => 'nullable|integer|min:1',
            'currency'          => 'nullable|string|max:10',
            'purpose'           => 'nullable|string|in:api_call,data_access,task_reward,compute,general,nano',
            'note'              => 'nullable|string|max:255',
            'stream'            => 'nullable|boolean',
        ]);

        if (!$request->filled('amount') && !$request->filled('amount_micro')) {
            return response()->json([
                'success' => false,
                'error'   => 'Provide either amount or amount_micro.',
                'code'    => 'VALIDATION_ERROR',
            ], 422);
        }

        $sender   = Agent::findOrFail($request->sender_agent_id);
        $receiver = Agent::findOrFail($request->receiver_agent_id);

        $amountMicro = $request->filled('amount_micro')
            ? (int) $request->amount_micro
            : (int) round((float) $request->amount * 1_000_000);

        $amount  = round($amountMicro / 1_000_000, 6);
        $purpose = $request->input('purpose', 'general');
        $stream  = $request->boolean('stream', false);

        try {
            // Simulated — Circle Gateway nanopayments API not yet public
            // On production, replace this block with the actual Circle API call
            $gatewayPaymentId = 'sim_' . Str::uuid()->toString();
            $status           = 'confirmed';

            // Persist
            $nano = Nanopayment::create([
                'sender_agent_id'    => $sender->id,
                'receiver_agent_id'  => $receiver->id,
                'amount'             => $amount,
                'amount_micro'       => $amountMicro,
                'currency'           => $request->input('currency', 'USDC'),
                'purpose'            => $purpose,
                'note'               => $request->input('note'),
                'status'             => $status,
                'stream'             => $stream,
                'gateway_payment_id' => $gatewayPaymentId,
                'verified_at'        => now(),
            ]);

            // Audit log
            AuditLog::create([
                'agent_id'     => $sender->id,
                'agent_name'   => $sender->name,
                'event_type'   => 'nanopayment.sent',
                'event_status' => $status,
                'amount'       => $amount,
                'source'       => 'nanopayment',
                'policy_rule'  => 'gas_free',
                'metadata'     => json_encode([
                    'receiver'   => $receiver->name,
                    'purpose'    => $purpose,
                    'stream'     => $stream,
                    'gateway_id' => $gatewayPaymentId,
                ]),
            ]);

            return response()->json([
                'success'     => true,
                'nanopayment' => [
                    'id'             => $nano->id,
                    'amount'         => number_format($amount, 6),
                    'amount_micro'   => $amountMicro,
                    'currency'       => $nano->currency,
                    'purpose'        => $purpose,
                    'stream'         => $stream,
                    'status'         => $status,
                    'gateway_id'     => $gatewayPaymentId,
                    'sender'         => $sender->name,
                    'receiver'       => $receiver->name,
                    'sender_agent'   => ['name' => $sender->name],
                    'receiver_agent' => ['name' => $receiver->name],
                    'note'           => $nano->note,
                    'verified_at'    => $nano->verified_at,
                    'created_at'     => $nano->created_at,
                ],
                'message' => 'Nanopayment confirmed — gas-free on Base-Sepolia',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Nanopayment failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error'   => 'Nanopayment failed: ' . $e->getMessage(),
                'code'    => 'NANOPAYMENT_ERROR',
            ], 500);
        }
    }

    /**
     * POST /api/v1/nano/batch
     */
    public function sendBatch(Request $request)
    {
        $request->validate([
            'sender_agent_id'              => 'required|integer|exists:agents,id',
            'payments'                     => 'required|array|min:1|max:100',
            'payments.*.receiver_agent_id' => 'required|integer|exists:agents,id',
            'payments.*.amount'            => 'nullable|numeric|min:0.000001',
            'payments.*.amount_micro'      => 'nullable|integer|min:1',
            'payments.*.purpose'           => 'nullable|string',
            'payments.*.note'              => 'nullable|string|max:255',
        ]);

        $results   = [];
        $succeeded = 0;
        $failed    = 0;

        foreach ($request->payments as $p) {
            $subRequest = new Request(array_merge($p, [
                'sender_agent_id' => $request->sender_agent_id,
            ]));

            $result = $this->send($subRequest);
            $data   = $result->getData(true);

            ($data['success'] ?? false) ? $succeeded++ : $failed++;
            $results[] = $data;
        }

        return response()->json([
            'success'   => $failed === 0,
            'total'     => count($request->payments),
            'succeeded' => $succeeded,
            'failed'    => $failed,
            'results'   => $results,
        ]);
    }

    /**
     * GET /api/v1/nano
     */
    public function index()
    {
        $payments = Nanopayment::with(['senderAgent', 'receiverAgent'])
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'sender_agent'   => ['name' => $p->senderAgent?->name],
                'receiver_agent' => ['name' => $p->receiverAgent?->name],
                'amount_micro'   => $p->amount_micro,
                'amount'         => $p->amount,
                'currency'       => $p->currency,
                'stream'         => (bool) ($p->stream ?? false),
                'status'         => $p->status,
                'note'           => $p->note,
                'created_at'     => $p->created_at,
            ]);

        return response()->json($payments);
    }

    /**
     * GET /api/v1/nano/stats
     */
    public function stats()
    {
        $total      = Nanopayment::count();
        $totalMicro = Nanopayment::sum('amount_micro');
        $pmAgents   = Agent::where('paymaster_enabled', true)->count();
        $volume     = Nanopayment::where('status', 'confirmed')->sum('amount');

        return response()->json([
            'total'              => $total,
            'total_micro'        => $totalMicro,
            'paymaster_agents'   => $pmAgents,
            'gas_saved_usdc'     => number_format($pmAgents * 0.002, 4),
            'total_nanopayments' => $total,
            'total_volume_usdc'  => number_format($volume, 6),
            'smallest_payment'   => Nanopayment::min('amount'),
            'largest_payment'    => Nanopayment::max('amount'),
        ]);
    }
}