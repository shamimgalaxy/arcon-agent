<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Services\CircleService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(protected CircleService $circle) {}

    public function index()
    {
        return response()->json(Agent::latest()->get());
    }

    public function store(Request $request)
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

        $walletSetResponse = $this->circle->createWalletSet($request->name);

        if (!isset($walletSetResponse['data']['walletSet']['id'])) {
            return response()->json([
                'error'    => 'Failed to create wallet set',
                'response' => $walletSetResponse,
            ], 500);
        }

        $walletSetId = $walletSetResponse['data']['walletSet']['id'];

        $walletResponse = $this->circle->createWallet($walletSetId, $blockchain);

        if (!isset($walletResponse['data']['wallets'][0])) {
            return response()->json([
                'error'    => 'Failed to create wallet',
                'response' => $walletResponse,
            ], 500);
        }

        $wallet = $walletResponse['data']['wallets'][0];

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

    public function show(Agent $agent)
    {
        return response()->json($agent);
    }

    // ─── Uses CircleService instead of raw Http call ────────────────────────
    public function balance(Agent $agent)
    {
        $response = $this->circle->client()
            ->get(config('services.circle.base_url') . "/v1/w3s/wallets/{$agent->circle_wallet_id}/balances")
            ->json();

        $balances = $response['data']['tokenBalances'] ?? [];

        return response()->json([
            'agent_id'       => $agent->id,
            'agent_name'     => $agent->name,
            'wallet_address' => $agent->circle_wallet_address,
            'blockchain'     => $agent->blockchain,
            'balances'       => $balances,
        ]);
    }
public function blockchains()
{
    $blockchains = collect(CircleService::getSupportedBlockchains())
        ->mapWithKeys(fn($data, $key) => [$key => $data['label']])
        ->toArray();

    return response()->json([
        'blockchains' => $blockchains,
    ]);
}
}