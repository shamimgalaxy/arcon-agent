<?php

namespace App\Http\Controllers;

use App\Events\AuditLogCreated;
use App\Jobs\PollCircleTransaction;
use App\Models\Agent;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Services\CircleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Circle minimum transfer on testnet
    const CIRCLE_MIN_AMOUNT = 0.01;

    public function __construct(protected CircleService $circle) {}

    public function send(Request $request, ?string $batchId = null)
    {
        $request->validate([
            'sender_agent_id'   => 'required|exists:agents,id',
            'receiver_agent_id' => 'required|exists:agents,id',
            'amount'            => 'required|numeric|min:' . self::CIRCLE_MIN_AMOUNT, // FIX #5
            'note'              => 'nullable|string',
        ]);

        $sender   = Agent::findOrFail($request->sender_agent_id);
        $receiver = Agent::findOrFail($request->receiver_agent_id);
        $amount   = (float) $request->amount;

        // ── Policy Enforcement ──────────────────────────────────────────
        if ($sender->spendingPolicy?->is_active) {
            $violation = $this->checkPolicy($sender, $amount, $batchId, $request, $receiver);
            if ($violation) return $violation;
        }
        // ── End Policy Enforcement ──────────────────────────────────────

        // FIX #2: Don't create payment record until Circle responds
        $response = $this->circle->transferUSDC(
            $sender->circle_wallet_id,
            $receiver->circle_wallet_address,
            (string) $amount,
            $sender->blockchain
        );

        \Log::info('Circle transfer response', ['response' => $response]);

        if (!isset($response['data']['id'])) {
            $this->writeAuditLog([
                'agent_id'         => $sender->id,
                'agent_name'       => $sender->name,
                'event_type'       => 'payment_failed',
                'event_status'     => 'failed',
                'amount'           => $amount,
                'currency'         => 'USDC',
                'blockchain'       => $sender->blockchain,
                'sender_address'   => $sender->circle_wallet_address,
                'receiver_address' => $receiver->circle_wallet_address,
                'batch_id'         => $batchId,
                'meta'             => ['circle_response' => $response],
                'ip_address'       => $request->ip(),
                'source'           => 'api',
            ]);

            return response()->json([
                'error'    => 'Payment failed',
                'response' => $response,
            ], 500);
        }

        // Circle accepted — now create the payment record
        $payment = Payment::create([
            'sender_agent_id'       => $sender->id,
            'receiver_agent_id'     => $receiver->id,
            'amount'                => $amount,
            'currency'              => 'USDC',
            'status'                => 'submitted',
            'blockchain'            => $sender->blockchain,
            'note'                  => $request->note,
            'circle_transaction_id' => $response['data']['id'],
        ]);

        $this->writeAuditLog([
            'agent_id'              => $sender->id,
            'agent_name'            => $sender->name,
            'event_type'            => 'payment_submitted',
            'event_status'          => 'allowed',
            'payment_id'            => $payment->id,
            'circle_transaction_id' => $response['data']['id'],
            'amount'                => $amount,
            'currency'              => 'USDC',
            'blockchain'            => $sender->blockchain,
            'sender_address'        => $sender->circle_wallet_address,
            'receiver_address'      => $receiver->circle_wallet_address,
            'batch_id'              => $batchId,
            'ip_address'            => $request->ip(),
            'source'                => 'api',
        ]);

        // FIX #1: Dispatch background job to poll until CONFIRMED/FAILED
        PollCircleTransaction::dispatch($payment->id)
            ->delay(now()->addSeconds(5));

        return response()->json([
            'message' => 'Payment submitted successfully',
            'payment' => $payment->fresh(),
        ], 201);
    }

    // ── FIX #3: sendBatch passes data cleanly, not fake Request ────────
    public function sendBatch(Request $request)
    {
        $request->validate([
            'sender_agent_id'              => 'required|exists:agents,id',
            'payments'                     => 'required|array|min:1',
            'payments.*.receiver_agent_id' => 'required|exists:agents,id',
            'payments.*.amount'            => 'required|numeric|min:' . self::CIRCLE_MIN_AMOUNT,
            'payments.*.note'              => 'nullable|string',
        ]);

        $batchId = (string) Str::uuid();
        $results = [];

        foreach ($request->payments as $item) {
            // FIX #3: Merge onto real request instead of new Request()
            $subRequest = $request->merge([
                'receiver_agent_id' => $item['receiver_agent_id'],
                'amount'            => $item['amount'],
                'note'              => $item['note'] ?? null,
            ]);

            $response  = $this->send($subRequest, $batchId);
            $results[] = [
                'receiver_agent_id' => $item['receiver_agent_id'],
                'amount'            => $item['amount'],
                'status'            => $response->getStatusCode(),
                'body'              => json_decode($response->getContent(), true),
            ];
        }

        return response()->json([
            'batch_id' => $batchId,
            'results'  => $results,
        ]);
    }

    public function index()
    {
        return response()->json(
            Payment::with(['senderAgent', 'receiverAgent'])->latest()->get()
        );
    }

    public function show(Payment $payment)
    {
        return response()->json(
            $payment->load(['senderAgent', 'receiverAgent'])
        );
    }

    public function auditLogs()
    {
        return response()->json(
            AuditLog::with('agent')->latest()->take(100)->get()
        );
    }

    // ── FIX #4: Single audit log helper ────────────────────────────────
    protected function writeAuditLog(array $data): void
    {
        $log = AuditLog::create($data);
        broadcast(new AuditLogCreated($log))->toOthers();
    }

    // ── Extracted policy check ──────────────────────────────────────────
    protected function checkPolicy(Agent $sender, float $amount, ?string $batchId, Request $request, Agent $receiver)
    {
        $policy = $sender->spendingPolicy;
        $base   = [
            'agent_id'         => $sender->id,
            'agent_name'       => $sender->name,
            'event_type'       => 'policy_violated',
            'event_status'     => 'blocked',
            'amount'           => $amount,
            'currency'         => 'USDC',
            'blockchain'       => $sender->blockchain,
            'sender_address'   => $sender->circle_wallet_address,
            'receiver_address' => $receiver->circle_wallet_address,
            'batch_id'         => $batchId,
            'ip_address'       => $request->ip(),
            'source'           => 'api',
        ];

        if ($policy->max_per_transaction !== null && $amount > $policy->max_per_transaction) {
            $this->writeAuditLog($base + [
                'policy_rule'   => 'max_per_transaction',
                'policy_detail' => "Amount {$amount} exceeds limit of {$policy->max_per_transaction} USDC.",
            ]);
            return response()->json([
                'error' => 'Policy violation: amount exceeds max_per_transaction.',
                'limit' => $policy->max_per_transaction, 'amount' => $amount,
            ], 422);
        }

        if ($policy->daily_limit !== null) {
            $todayTotal = Payment::where('sender_agent_id', $sender->id)
                ->whereIn('status', ['submitted', 'confirmed'])
                ->whereDate('created_at', today())->sum('amount');

            if (($todayTotal + $amount) > $policy->daily_limit) {
                $this->writeAuditLog($base + [
                    'policy_rule'   => 'daily_limit',
                    'policy_detail' => "Used {$todayTotal}, adding {$amount} exceeds daily limit of {$policy->daily_limit} USDC.",
                ]);
                return response()->json([
                    'error' => 'Policy violation: daily limit would be exceeded.',
                    'limit' => $policy->daily_limit, 'used_today' => $todayTotal, 'amount' => $amount,
                ], 422);
            }
        }

        if ($policy->monthly_limit !== null) {
            $monthTotal = Payment::where('sender_agent_id', $sender->id)
                ->whereIn('status', ['submitted', 'confirmed'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->sum('amount');

            if (($monthTotal + $amount) > $policy->monthly_limit) {
                $this->writeAuditLog($base + [
                    'policy_rule'   => 'monthly_limit',
                    'policy_detail' => "Used {$monthTotal}, adding {$amount} exceeds monthly limit of {$policy->monthly_limit} USDC.",
                ]);
                return response()->json([
                    'error' => 'Policy violation: monthly limit would be exceeded.',
                    'limit' => $policy->monthly_limit, 'used_month' => $monthTotal, 'amount' => $amount,
                ], 422);
            }
        }

        return null;
    }

    // GET /agents/{agent}/portfolio


}