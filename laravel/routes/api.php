<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AgentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\BatchPaymentController;
use App\Http\Controllers\AuditLogController;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AgentController as V1AgentController;
use App\Http\Controllers\Api\V1\PaymentController as V1PaymentController;
use App\Http\Controllers\Api\V1\AuditLogController as V1AuditLogController;
use App\Http\Controllers\Api\V1\CrossChainController;
use App\Http\Controllers\Api\V1\CoordinationController;
use App\Http\Controllers\AgentTriggerController;
use App\Http\Controllers\Api\V1\NanopaymentController;
use App\Http\Controllers\Api\V1\PaymasterController;

// ════════════════════════════════════════════════════════════════════════════
// V1 API
// ════════════════════════════════════════════════════════════════════════════

Route::prefix('v1')->group(function () {

    // ── Auth (public) ────────────────────────────────────────────────────────
    Route::post('auth/token', [AuthController::class, 'issue']);

    // ── Nano + Paymaster (public — no Sanctum token needed) ──────────────────
    Route::get('nano/stats',  [NanopaymentController::class, 'stats']);
    Route::get('nano',        [NanopaymentController::class, 'index']);
    Route::post('nano/send',  [NanopaymentController::class, 'send']);
    Route::post('nano/batch', [NanopaymentController::class, 'sendBatch']);

    Route::post('paymaster/toggle',          [PaymasterController::class, 'toggle']);
    Route::get('paymaster/status/{agentId}', [PaymasterController::class, 'status']);

    // Also keep nanopayments prefix working (no auth)
    Route::get('nanopayments/stats',  [NanopaymentController::class, 'stats']);
    Route::get('nanopayments',        [NanopaymentController::class, 'index']);
    Route::post('nanopayments/send',  [NanopaymentController::class, 'send']);
    Route::post('nanopayments/batch', [NanopaymentController::class, 'sendBatch']);

    // ── Protected ────────────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::get('auth/me',       [AuthController::class, 'me']);
        Route::delete('auth/token', [AuthController::class, 'revokeToken']);

        // Blockchains
        Route::get('blockchains', [V1AgentController::class, 'blockchains']);

        // Agents
        Route::get('agents',                 [V1AgentController::class, 'index']);
        Route::post('agents',                [V1AgentController::class, 'store']);
        Route::get('agents/{agent}',         [V1AgentController::class, 'show']);
        Route::get('agents/{agent}/balance', [V1AgentController::class, 'balance']);

        // Payments
        Route::post('payments/send',  [V1PaymentController::class, 'send']);
        Route::post('payments/batch', [V1PaymentController::class, 'sendBatch']);
        Route::get('payments',        [V1PaymentController::class, 'index']);
        Route::get('payments/{id}',   [V1PaymentController::class, 'show']);

        // Audit Logs
        Route::get('audit-logs', [V1AuditLogController::class, 'index']);

        // Cross-chain (CCTP)
        Route::get('crosschain',                 [CrossChainController::class, 'index']);
        Route::post('crosschain/initiate',       [CrossChainController::class, 'initiate']);
        Route::post('crosschain/{id}/burn-hash', [CrossChainController::class, 'setBurnHash']);
        Route::get('crosschain/{id}/status',     [CrossChainController::class, 'status']);

        // Multi-agent coordination
        Route::post('coordination/execute', [CoordinationController::class, 'execute']);

        // Paymaster (protected versions)
        Route::post('paymaster/enable',        [PaymasterController::class, 'enable']);
        Route::post('paymaster/disable',       [PaymasterController::class, 'disable']);
        Route::get('agents/{agent}/paymaster', [PaymasterController::class, 'status']);
        Route::post('paymaster/userop',        [PaymasterController::class, 'buildAndSubmit']);
    });
});

// ════════════════════════════════════════════════════════════════════════════
// Webhooks (public)
// ════════════════════════════════════════════════════════════════════════════

Route::post('/webhook/circle', [WebhookController::class, 'handle']);

// ════════════════════════════════════════════════════════════════════════════
// Legacy / Internal API (public — used by frontend JS directly)
// ════════════════════════════════════════════════════════════════════════════

Route::get('/blockchains',      [AgentController::class, 'blockchains']);

Route::post('/agents/register', [AgentController::class, 'register']);
Route::get('/agents',           [AgentController::class, 'index']);
Route::get('/agents/{agent}',   [AgentController::class, 'show']);

Route::get('/agents/{agent}/policy',    [PolicyController::class, 'show']);
Route::post('/agents/{agent}/policy',   [PolicyController::class, 'store']);
Route::put('/agents/{agent}/policy',    [PolicyController::class, 'update']);
Route::delete('/agents/{agent}/policy', [PolicyController::class, 'destroy']);

Route::post('/payments/send',           [PaymentController::class, 'send']);
Route::post('/payments/batch',          [BatchPaymentController::class, 'send']);
Route::get('/payments/batch/{batchId}', [BatchPaymentController::class, 'show']);
Route::get('/payments',                 [PaymentController::class, 'index']);
Route::get('/payments/{payment}',       [PaymentController::class, 'show']);

Route::get('audit-logs/stats',             [AuditLogController::class, 'stats']);
Route::get('audit-logs/policy-violations', [AuditLogController::class, 'policyViolations']);
Route::get('audit-logs/agent/{agentId}',   [AuditLogController::class, 'forAgent']);
Route::get('audit-logs',                   [AuditLogController::class, 'index']);
Route::get('audit-logs/{id}',              [AuditLogController::class, 'show']);

Route::prefix('agents/{agent}/triggers')->group(function () {
    Route::get('/',                          [AgentTriggerController::class, 'index']);
    Route::post('/',                         [AgentTriggerController::class, 'store']);
    Route::get('/{trigger}',                 [AgentTriggerController::class, 'show']);
    Route::patch('/{trigger}',               [AgentTriggerController::class, 'update']);
    Route::delete('/{trigger}',              [AgentTriggerController::class, 'destroy']);
    Route::post('/{trigger}/fire',           [AgentTriggerController::class, 'fire']);
    Route::patch('/{trigger}/toggle-active', [AgentTriggerController::class, 'toggleActive']);
});

Route::post('n8n/payment', [App\Http\Controllers\N8nController::class, 'handlePayment']);
Route::get('/m4', fn() => view('m4'));