<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CctpService
{
    // CCTP V2 Iris Attestation API (testnet)
    protected string $irisApi = 'https://iris-api-sandbox.circle.com/v2/messages';

    // Base Sepolia USDC contract
    protected string $usdcContract = '0x036CbD53842c5426634e7929541eC2318f3dCF7e';

    // Base Sepolia TokenMessenger (CCTP V1 testnet)
    protected string $tokenMessenger = '0x9f3B8679c73C2Fef8b59B4f3444d4e156fb70AA5';

    // Supported destination domains
    protected array $domains = [
        'ethereum-sepolia' => 0,
        'avalanche-fuji'   => 1,
        'op-sepolia'       => 2,
        'arbitrum-sepolia' => 3,
        'base-sepolia'     => 6,
        'polygon-amoy'     => 7,
    ];

    /**
     * Get attestation for a burn tx hash
     */
    public function getAttestation(string $txHash, string $sourceDomain = 'base-sepolia'): array
    {
        $domainId = $this->domains[$sourceDomain] ?? 6;

        $response = Http::get("{$this->irisApi}/{$domainId}/{$txHash}");

        if ($response->successful()) {
            return [
                'success'     => true,
                'attestation' => $response->json('messages.0.attestation'),
                'message'     => $response->json('messages.0.message'),
                'status'      => $response->json('messages.0.attestationStatus'),
            ];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    /**
     * Initiate a cross-chain transfer (record + return burn instructions)
     * Actual on-chain burn must be done via frontend/web3
     */
    public function initiateCrossChain(array $data): array
    {
        // Record the intent in DB
        $transfer = \App\Models\CrossChainTransfer::create([
            'agent_id'         => $data['agent_id'],
            'source_chain'     => $data['source_chain'] ?? 'base-sepolia',
            'destination_chain'=> $data['destination_chain'],
            'destination_address' => $data['destination_address'],
            'amount_usdc'      => $data['amount'],
            'status'           => 'pending',
            'usdc_contract'    => $this->usdcContract,
            'token_messenger'  => $this->tokenMessenger,
            'destination_domain' => $this->domains[$data['destination_chain']] ?? null,
        ]);

        return [
            'success'           => true,
            'transfer_id'       => $transfer->id,
            'instructions'      => [
                'step1' => 'Approve USDC spending on TokenMessenger contract',
                'step2' => 'Call depositForBurn() on TokenMessenger',
                'usdc_contract'     => $this->usdcContract,
                'token_messenger'   => $this->tokenMessenger,
                'destination_domain'=> $this->domains[$data['destination_chain']] ?? null,
                'amount_wei'        => bcmul($data['amount'], '1000000'), // 6 decimals
            ],
        ];
    }

    /**
     * Check & update attestation status for a transfer
     */
    public function checkAndUpdate(int $transferId): array
    {
        $transfer = \App\Models\CrossChainTransfer::findOrFail($transferId);

        if (!$transfer->burn_tx_hash) {
            return ['success' => false, 'error' => 'No burn tx hash yet'];
        }

        $result = $this->getAttestation($transfer->burn_tx_hash);

        if ($result['success'] && $result['status'] === 'complete') {
            $transfer->update([
                'status'      => 'attested',
                'attestation' => $result['attestation'],
                'message_hex' => $result['message'],
            ]);
        }

        return $result;
    }
}