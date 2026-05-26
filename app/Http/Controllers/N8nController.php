<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;

class N8nController extends Controller
{
    public function handlePayment(Request $request)
    {
        $request->validate([
            'sender_agent_id'   => 'required|exists:agents,id',
            'receiver_agent_id' => 'required|exists:agents,id',
            'amount'            => 'required|numeric|min:0.000001',
            'note'              => 'nullable|string',
        ]);

        return app(PaymentController::class)->send($request);
    }
}