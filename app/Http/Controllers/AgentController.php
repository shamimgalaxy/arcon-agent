<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Services\CircleService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(protected CircleService $circle) {}

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email|unique:agents,email',
            'blockchain' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value && !CircleService::isValidBlockchain($value)) {
                        $supported = implode(', ', array_keys(CircleService::getSupportedBlockchains()));
                        $fail("Invalid blockchain. Supported: {$supported}");
                    }
                },
            ],
        ]);

        $blockchain = $request->blockchain ?? 'ARC-TESTNET';

        // Step 1: Create wallet set
        $walletSetResponse = $this->circle->createWalletSet($request->name);

        if (!isset($walletSetResponse['data']['walletSet']['id'])) {
            return response()->json([
                'error'    => 'Failed to create wallet set',
                'response' => $walletSetResponse,
            ], 500);
        }

        $walletSetId = $walletSetResponse['data']['walletSet']['id'];

        // Step 2: Create wallet on selected blockchain
        $walletResponse = $this->circle->createWallet($walletSetId, $blockchain);

        if (!isset($walletResponse['data']['wallets'][0])) {
            return response()->json([
                'error'    => 'Failed to create wallet',
                'response' => $walletResponse,
            ], 500);
        }

        $wallet = $walletResponse['data']['wallets'][0];

        // Step 3: Save agent
        $agent = Agent::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'circle_wallet_id'      => $wallet['id'],
            'circle_wallet_address' => $wallet['address'],
            'blockchain'            => $wallet['blockchain'],
            'status'                => 'active',
        ]);

        return response()->json([
            'message' => 'Agent registered successfully',
            'agent'   => $agent,
        ], 201);
    }

    public function blockchains()
    {
        return response()->json([
            'blockchains' => CircleService::getSupportedBlockchains(),
        ]);
    }

    public function show(Agent $agent)
    {
        return response()->json($agent);
    }

    public function index()
    {
        return response()->json(Agent::all());
    }
}