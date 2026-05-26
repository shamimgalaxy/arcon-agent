<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Circle Webhook Received', $payload);

        $eventType = $payload['notificationType'] ?? null;
        $transaction = $payload['transaction'] ?? null;

        if (!$transaction) {
            return response()->json(['message' => 'No transaction data'], 200);
        }

        $transactionId = $transaction['id'] ?? null;
        $state         = $transaction['state'] ?? null;

        if (!$transactionId) {
            return response()->json(['message' => 'No transaction ID'], 200);
        }

        // Find matching payment and update status
        $payment = Payment::where('circle_transaction_id', $transactionId)->first();

        if ($payment) {
            $status = match($state) {
                'CONFIRMED'  => 'confirmed',
                'COMPLETE'   => 'confirmed',
                'FAILED'     => 'failed',
                'CANCELLED'  => 'failed',
                default      => 'submitted',
            };

            $payment->update(['status' => $status]);
            // Fire broadcast event
\App\Events\PaymentUpdated::dispatch($payment->fresh());

            Log::info("Payment {$payment->id} updated to {$status}");
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }
}