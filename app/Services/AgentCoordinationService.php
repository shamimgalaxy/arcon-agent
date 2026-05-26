<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AgentCoordinationService
{
    public function __construct(protected CircleService $circle) {}

    /**
     * Orchestrator agent pays multiple receiver agents in sequence
     * with dependency support (pay B only after A succeeds)
     */
    public function coordinate(array $data): array
    {
        $orchestrator = Agent::findOrFail($data['orchestrator_agent_id']);
        $results      = [];
        $failed       = 0;

        foreach ($data['tasks'] as $index => $task) {

            // Check dependency — skip if a required prior task failed
            if (isset($task['depends_on'])) {
                $dep = $results[$task['depends_on']] ?? null;
                if (!$dep || !$dep['success']) {
                    $results[$index] = [
                        'success'  => false,
                        'skipped'  => true,
                        'reason'   => "Dependency task {$task['depends_on']} failed or missing",
                        'receiver' => $task['receiver_agent_id'],
                    ];
                    $failed++;
                    continue;
                }
            }

            $receiver = Agent::find($task['receiver_agent_id']);

            if (!$receiver || !$receiver->wallet_address) {
                $results[$index] = [
                    'success'  => false,
                    'reason'   => 'Receiver agent not found or no wallet',
                    'receiver' => $task['receiver_agent_id'],
                ];
                $failed++;
                continue;
            }

            // Execute payment via Circle
            try {
                $payment = $this->circle->sendPayment([
                    'source_wallet_id'      => $orchestrator->circle_wallet_id,
                    'destination_address'   => $receiver->wallet_address,
                    'amount'                => $task['amount'],
                    'currency'              => 'USD',
                    'blockchain'            => 'BASE-SEPOLIA',
                ]);

                // Record in payments table
                $paymentRecord = Payment::create([
                    'sender_agent_id'   => $orchestrator->id,
                    'receiver_agent_id' => $receiver->id,
                    'amount'            => $task['amount'],
                    'currency'          => 'USD',
                    'circle_payment_id' => $payment['id'] ?? null,
                    'status'            => $payment['status'] ?? 'pending',
                    'note'              => $task['note'] ?? 'Multi-agent coordination',
                ]);

                // Audit log
                AuditLog::create([
                    'agent_id'   => $orchestrator->id,
                    'action'     => 'coordination_payment',
                    'amount'     => $task['amount'],
                    'currency'   => 'USD',
                    'status'     => 'success',
                    'meta'       => json_encode([
                        'receiver_agent_id' => $receiver->id,
                        'task_index'        => $index,
                        'circle_payment_id' => $payment['id'] ?? null,
                    ]),
                ]);

                $results[$index] = [
                    'success'           => true,
                    'receiver'          => $receiver->name,
                    'amount'            => $task['amount'],
                    'circle_payment_id' => $payment['id'] ?? null,
                    'payment_id'        => $paymentRecord->id,
                ];

            } catch (\Exception $e) {
                Log::error('Coordination payment failed', [
                    'orchestrator' => $orchestrator->id,
                    'receiver'     => $task['receiver_agent_id'],
                    'error'        => $e->getMessage(),
                ]);

                $results[$index] = [
                    'success'  => false,
                    'receiver' => $task['receiver_agent_id'],
                    'reason'   => $e->getMessage(),
                ];
                $failed++;
            }
        }

        return [
            'orchestrator'  => $orchestrator->name,
            'total_tasks'   => count($data['tasks']),
            'succeeded'     => count($data['tasks']) - $failed,
            'failed'        => $failed,
            'results'       => $results,
        ];
    }
}