<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CircleService
{
    // ─── All supported blockchains ─────────────────────────────────────────
    const SUPPORTED_BLOCKCHAINS = [
        // Testnets
        'ARC-TESTNET'   => ['label' => 'Arc Testnet',      'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'BASE-SEPOLIA'  => ['label' => 'Base Sepolia',      'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'ETH-SEPOLIA'   => ['label' => 'Ethereum Sepolia',  'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'ARB-SEPOLIA'   => ['label' => 'Arbitrum Sepolia',  'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'AVAX-FUJI'     => ['label' => 'Avalanche Fuji',    'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'MATIC-AMOY'    => ['label' => 'Polygon Amoy',      'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'OP-SEPOLIA'    => ['label' => 'Optimism Sepolia',  'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'UNI-SEPOLIA'   => ['label' => 'Unichain Sepolia',  'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'MONAD-TESTNET' => ['label' => 'Monad Testnet',     'type' => 'testnet', 'accountTypes' => ['EOA', 'SCA']],
        'SOL-DEVNET'    => ['label' => 'Solana Devnet',     'type' => 'testnet', 'accountTypes' => ['EOA']],
        // Mainnets
        'BASE'          => ['label' => 'Base',              'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'ETH'           => ['label' => 'Ethereum',          'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'ARB'           => ['label' => 'Arbitrum',          'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'AVAX'          => ['label' => 'Avalanche',         'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'MATIC'         => ['label' => 'Polygon',           'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'OP'            => ['label' => 'Optimism',          'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'UNI'           => ['label' => 'Unichain',          'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'MONAD'         => ['label' => 'Monad',             'type' => 'mainnet', 'accountTypes' => ['EOA', 'SCA']],
        'SOL'           => ['label' => 'Solana',            'type' => 'mainnet', 'accountTypes' => ['EOA']],
    ];

    const EOA_ONLY_BLOCKCHAINS = ['SOL', 'SOL-DEVNET'];

    protected string $baseUrl;
    protected string $apiKey;
    protected string $defaultBlockchain;

    // ─── Single clean constructor ───────────────────────────────────────────
    public function __construct()
    {
        $this->baseUrl           = config('services.circle.base_url');
        $this->apiKey            = config('services.circle.api_key');
        $this->defaultBlockchain = config('services.circle.default_blockchain', 'ARC-TESTNET');
    }

    public function client()
    {
        $http = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ]);

        if (app()->environment(['local', 'testing'])) {
            $http = $http->withoutVerifying();
        }

        return $http;
    }

    public static function getSupportedBlockchains(): array
    {
        return self::SUPPORTED_BLOCKCHAINS;
    }

    public static function isValidBlockchain(string $blockchain): bool
    {
        return array_key_exists($blockchain, self::SUPPORTED_BLOCKCHAINS);
    }

    public static function getAccountType(string $blockchain): string
    {
        return in_array($blockchain, self::EOA_ONLY_BLOCKCHAINS) ? 'EOA' : 'SCA';
    }

    public function getEntitySecretCiphertext(): string
    {
        return Cache::remember('circle_entity_ciphertext', now()->addMinutes(10), function () {
            $entitySecret = config('services.circle.entity_secret');

            $response  = $this->client()->get("{$this->baseUrl}/v1/w3s/config/entity/publicKey");
            $publicKey = $response->json('data.publicKey');

            $rsa = \phpseclib3\Crypt\RSA::loadPublicKey($publicKey)
                ->withPadding(\phpseclib3\Crypt\RSA::ENCRYPTION_OAEP)
                ->withHash('sha256')
                ->withMGFHash('sha256');

            return base64_encode($rsa->encrypt(hex2bin($entitySecret)));
        });
    }

    public function ping(): array
    {
        return $this->client()->get("{$this->baseUrl}/ping")->json();
    }

    public function createWalletSet(string $name): array
    {
        return $this->client()->post("{$this->baseUrl}/v1/w3s/developer/walletSets", [
            'name'                   => $name,
            'idempotencyKey'         => Str::uuid()->toString(),
            'entitySecretCiphertext' => $this->getEntitySecretCiphertext(),
        ])->json();
    }

    public function createWallet(string $walletSetId, string $blockchain = null): array
    {
        $blockchain  = $blockchain ?? $this->defaultBlockchain;
        $accountType = self::getAccountType($blockchain);

        return $this->client()->post("{$this->baseUrl}/v1/w3s/developer/wallets", [
            'idempotencyKey'         => Str::uuid()->toString(),
            'blockchains'            => [$blockchain],
            'walletSetId'            => $walletSetId,
            'count'                  => 1,
            'accountType'            => $accountType,
            'entitySecretCiphertext' => $this->getEntitySecretCiphertext(),
        ])->json();
    }

    public function transferUSDC(
        string $sourceWalletId,
        string $destinationAddress,
        string $amount,
        string $blockchain = null
    ): array {
        $blockchain = $blockchain ?? $this->defaultBlockchain;
        $tokenId    = $this->getUSDCTokenId($sourceWalletId);

        if (!$tokenId) {
            return ['error' => 'USDC token not found in wallet balances'];
        }

        return $this->client()->post("{$this->baseUrl}/v1/w3s/developer/transactions/transfer", [
            'idempotencyKey'         => Str::uuid()->toString(),
            'walletId'               => $sourceWalletId,
            'entitySecretCiphertext' => $this->getEntitySecretCiphertext(),
            'amounts'                => [$amount],
            'destinationAddress'     => $destinationAddress,
            'feeLevel'               => 'MEDIUM',
            'tokenId'                => $tokenId,
            'blockchain'             => $blockchain,
        ])->json();
    }

    public function pollTransactionStatus(string $transactionId, int $maxAttempts = 10): string
    {
        $terminalStates = ['CONFIRMED', 'COMPLETE', 'FAILED', 'CANCELLED'];

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->client()
                ->get("{$this->baseUrl}/v1/w3s/transactions/{$transactionId}")
                ->json();

            $state = $response['data']['transaction']['state'] ?? 'UNKNOWN';

            if (in_array($state, $terminalStates)) {
                return $state;
            }

            sleep(3);
        }

        return 'TIMEOUT';
    }

    public function getTransaction(string $transactionId): array
    {
        return $this->client()
            ->get("{$this->baseUrl}/v1/w3s/transactions/{$transactionId}")
            ->json();
    }

    protected function getUSDCTokenId(string $walletId): ?string
    {
        $response = $this->client()
            ->get("{$this->baseUrl}/v1/w3s/wallets/{$walletId}/balances")
            ->json();

        foreach ($response['data']['tokenBalances'] ?? [] as $balance) {
            $symbol = $balance['token']['symbol'] ?? $balance['symbol'] ?? null;
            $id     = $balance['token']['id']     ?? $balance['id']     ?? null;

            if ($symbol === 'USDC' && $id) {
                return $id;
            }
        }

        return null;
    }
}