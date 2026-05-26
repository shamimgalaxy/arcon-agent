 <?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BatchPaymentService
{
    public function __construct(
        protected CircleService $circle,
        protected PolicyService $policy,
    ) {}

    public function process(Agent $sender, array $payments): array
    {
        $batchId     = (string) Str::uuid();
        $totalAmount = collect($payments)->sum('amount');
        $results     = [];
        $succeeded   = 0;
        $failed      = 0;
        $blocked     = 0;
        $totalSent   = 0.0;

        // Log batch start
        AuditService::batchStarted($sender, $batchId, count($payments), $totalAmount);

        foreach ($payments as $index => $item) {
            $receiver = Agent::find($item['receiver_id']);

            // Unknown receiver
            if (! $receiver) {
                $results[] = $this->resultRow($index, $item, null, 'failed', 'Receiver agent not found');
                $failed++;
                continue;
            }

            $amount = (float) $item['amount'];
            $note   = $item['note'] ?? null;

            // Policy check
            $policyCheck = $this->policy->check($sender, $receiver, $amount);

            if (! $policyCheck['allowed']) {
                AuditService::policyViolation(
                    $sender, $receiver, $amount,
                    $policyCheck['rule'],
                    $policyCheck['detail'],
                    $batchId
                );
                $results[] = $this->resultRow($index, $item, $receiver, 'blocked', $policyCheck['detail']);
                $blocked++;
                continue;
            }

            // Attempt payment
            AuditService::paymentAttempt($sender, $receiver, $amount, $note, $batchId);

            try {
                DB::beginTransaction();

                $txResponse = $this->circle->transferUSDC(
                    $sender->circle_wallet_id,
                    $receiver->circle_wallet_address,
                    $amount
                );

                $payment = Payment::create([
                    'sender_agent_id'       => $sender->id,
                    'receiver_agent_id'     => $receiver->id,
                    'amount'                => $amount,
                    'currency'              => 'USDC',
                    'circle_transaction_id' => $txResponse['id'] ?? null,
                    'status'                => 'submitted',
                    'blockchain'            => $sender->blockchain ?? 'BASE-SEPOLIA',
                    'note'                  => $note,
                    'batch_id'              => $batchId,
                    'batch_index'           => $index,
                ]);

                AuditService::paymentSubmitted($payment);

                DB::commit();

                $totalSent += $amount;
                $succeeded++;
                $results[] = $this->resultRow($index, $item, $receiver, 'submitted', 'OK', $payment);

            } catch (\Throwable $e) {
                DB::rollBack();
                AuditService::paymentFailed($sender, $receiver, $amount, $e->getMessage(), $batchId);
                $results[] = $this->resultRow($index, $item, $receiver, 'failed', $e->getMessage());
                $failed++;
            }
        }

        // Log batch completion
        AuditService::batchCompleted($sender, $batchId, $succeeded, $failed, $blocked, $totalSent);

        return [
            'batch_id'        => $batchId,
            'sender_agent_id' => $sender->id,
            'sender_name'     => $sender->name,
            'total_payments'  => count($payments),
            'succeeded'       => $succeeded,
            'failed'          => $failed,
            'blocked'         => $blocked,
            'total_sent_usdc' => $totalSent,
            'results'         => $results,
        ];
    }

    private function resultRow(int $index, array $item, ?Agent $receiver, string $status, string $message, ?Payment $payment = null): array
    {
        return [
            'index'             => $index,
            'receiver_agent_id' => $item['receiver_id'],
            'receiver_name'     => $receiver?->name,
            'amount'            => $item['amount'],
            'status'            => $status,
            'message'           => $message,
            'payment_id'        => $payment?->id,
            'circle_tx_id'      => $payment?->circle_transaction_id,
        ];
    }
}
