<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CctpService;
use App\Models\CrossChainTransfer;
use Illuminate\Http\Request;

class CrossChainController extends Controller
{
    public function __construct(protected CctpService $cctp) {}

    // POST /api/v1/crosschain/initiate
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'agent_id'            => 'required|exists:agents,id',
            'destination_chain'   => 'required|in:ethereum-sepolia,avalanche-fuji,op-sepolia,arbitrum-sepolia,polygon-amoy',
            'destination_address' => 'required|string',
            'amount'              => 'required|numeric|min:0.000001',
        ]);

        $result = $this->cctp->initiateCrossChain($data);

        return response()->json($result, 201);
    }

    // POST /api/v1/crosschain/{id}/burn-hash
    public function setBurnHash(Request $request, int $id)
    {
        $request->validate(['tx_hash' => 'required|string']);

        $transfer = CrossChainTransfer::findOrFail($id);
        $transfer->update([
            'burn_tx_hash' => $request->tx_hash,
            'status'       => 'burned',
        ]);

        return response()->json(['success' => true, 'status' => 'burned']);
    }

    // GET /api/v1/crosschain/{id}/status
    public function status(int $id)
    {
        $transfer = CrossChainTransfer::findOrFail($id);
        $result   = $this->cctp->checkAndUpdate($id);

        return response()->json([
            'transfer' => $transfer->fresh(),
            'attestation' => $result,
        ]);
    }

    // GET /api/v1/crosschain
    public function index()
    {
        return response()->json(
            CrossChainTransfer::with('agent')->latest()->get()
        );
    }
}