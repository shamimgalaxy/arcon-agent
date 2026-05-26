<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymasterController extends Controller
{
    // Base Sepolia Paymaster contract
    const PAYMASTER_ADDRESS = '0x31BE08D380A21fc740883c0BC434FcFc88740b58';
    const USDC_ADDRESS      = '0x036CbD53842c5426634e7929541eC2318f3dCF7e';
    const BUNDLER_RPC       = 'https://api.developer.coinbase.com/rpc/v1/base-sepolia/';

    /**
     * POST /api/v1/paymaster/enable
     * Enable USDC gas payments for an agent
     */
    public function enable(Request $request)
    {
        $request->validate([
            'agent_id'      => 'required|integer|exists:agents,id',
            'gas_usdc_limit'=> 'required|numeric|min:0.01|max:10.00',
        ]);

        $agent = Agent::findOrFail($request->agent_id);

        $agent->update([
            'paymaster_enabled' => true,
            'gas_usdc_limit'    => $request->gas_usdc_limit,
        ]);

        AuditLog::create([
            'agent_id'     => $agent->id,
            'agent_name'   => $agent->name,
            'event_type'   => 'paymaster.enabled',
            'event_status' => 'allowed',
            'source'       => 'paymaster',
            'metadata'     => json_encode([
                'gas_usdc_limit' => $request->gas_usdc_limit,
            ]),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Paymaster enabled for {$agent->name} — gas capped at \${$request->gas_usdc_limit} USDC",
            'agent'   => [
                'id'              => $agent->id,
                'name'            => $agent->name,
                'paymaster'       => true,
                'gas_usdc_limit'  => $request->gas_usdc_limit,
            ],
        ]);
    }

    /**
     * POST /api/v1/paymaster/disable
     */
    public function disable(Request $request)
    {
        $request->validate(['agent_id' => 'required|integer|exists:agents,id']);

        $agent = Agent::findOrFail($request->agent_id);
        $agent->update(['paymaster_enabled' => false]);

        return response()->json([
            'success' => true,
            'message' => "Paymaster disabled for {$agent->name}",
        ]);
    }

    /**
     * GET /api/v1/agents/{agent}/paymaster
     */
    public function status(Agent $agent)
    {
        return response()->json([
            'agent_id'        => $agent->id,
            'agent_name'      => $agent->name,
            'paymaster'       => [
                'enabled'         => (bool) $agent->paymaster_enabled,
                'gas_usdc_limit'  => $agent->gas_usdc_limit ?? 1.00,
                'address'         => self::PAYMASTER_ADDRESS,
                'usdc_contract'   => self::USDC_ADDRESS,
                'network'         => 'Base Sepolia',
            ],
        ]);
    }

    /**
     * POST /api/v1/paymaster/userop
     * Build + submit a UserOperation with USDC gas via ERC-4337
     * The frontend has already collected the EIP-2612 permit signature
     */
    public function buildAndSubmit(Request $request)
    {
        $request->validate([
            'agent_id'        => 'required|integer|exists:agents,id',
            'permit_signature'=> 'required|string',   // EIP-2612 signed permit from frontend
            'permit_value'    => 'required|numeric',  // max USDC for gas
            'call_to'         => 'required|string',   // target contract address
            'call_data'       => 'required|string',   // encoded calldata
        ]);

        $agent = Agent::findOrFail($request->agent_id);

        if (!$agent->paymaster_enabled) {
            return response()->json([
                'success' => false,
                'error'   => 'Paymaster not enabled for this agent',
                'code'    => 'PAYMASTER_DISABLED',
            ], 422);
        }

        try {
            // Submit UserOp to bundler
            $bundlerResponse = Http::post(
                self::BUNDLER_RPC . config('services.circle.bundler_key'),
                [
                    'jsonrpc' => '2.0',
                    'id'      => 1,
                    'method'  => 'eth_sendUserOperation',
                    'params'  => [
                        [
                            'sender'               => $agent->circle_wallet_address,
                            'paymaster'            => self::PAYMASTER_ADDRESS,
                            'paymasterData'        => $this->encodePaymasterData(
                                $request->permit_value,
                                $request->permit_signature
                            ),
                            'callData'             => $request->call_data,
                        ],
                        '0x0000000000000000000000000000000000000000000000000000000000000006', // EntryPoint v0.7
                    ],
                ]
            );

            $result = $bundlerResponse->json();

            AuditLog::create([
                'agent_id'     => $agent->id,
                'agent_name'   => $agent->name,
                'event_type'   => 'paymaster.userop_submitted',
                'event_status' => isset($result['result']) ? 'allowed' : 'failed',
                'source'       => 'paymaster',
                'metadata'     => json_encode([
                    'userop_hash'    => $result['result'] ?? null,
                    'permit_value'   => $request->permit_value,
                    'error'          => $result['error'] ?? null,
                ]),
            ]);

            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'error'   => $result['error']['message'] ?? 'Bundler rejected UserOp',
                    'code'    => 'BUNDLER_ERROR',
                ], 422);
            }

            return response()->json([
                'success'      => true,
                'userop_hash'  => $result['result'],
                'message'      => 'UserOp submitted — gas paid in USDC via Circle Paymaster',
                'paymaster'    => self::PAYMASTER_ADDRESS,
            ]);

        } catch (\Exception $e) {
            Log::error('Paymaster UserOp failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'code'    => 'PAYMASTER_ERROR',
            ], 500);
        }
    }

    private function encodePaymasterData(float $permitValue, string $permitSignature): string
    {
        // ERC-4337 paymasterData: [uint8 mode, address token, uint256 maxCost, bytes permit]
        // mode 0 = use permit, token = USDC
        $maxCostWei = dechex((int) round($permitValue * 1_000_000));
        return '0x00'
            . ltrim(self::USDC_ADDRESS, '0x')
            . str_pad($maxCostWei, 64, '0', STR_PAD_LEFT)
            . ltrim($permitSignature, '0x');
    }

    // Add to PaymasterController
public function toggle(Request $request)
{
    $validated = $request->validate([
        'agent_id'       => 'required|exists:agents,id',
        'enabled'        => 'required|boolean',
        'gas_usdc_limit' => 'nullable|numeric|min:0',
    ]);

    $agent = Agent::findOrFail($validated['agent_id']);
    $agent->update([
        'paymaster_enabled' => $validated['enabled'],
        'gas_usdc_limit'    => $validated['gas_usdc_limit'] ?? $agent->gas_usdc_limit,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Paymaster ' . ($validated['enabled'] ? 'enabled' : 'disabled'),
        'agent'   => $agent->fresh(),
    ]);
}
}