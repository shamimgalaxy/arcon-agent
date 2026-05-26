<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController as BasePaymentController;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected BasePaymentController $base) {}

    public function send(Request $request)
    {
        return $this->base->send($request);
    }

    public function sendBatch(Request $request)
    {
        return $this->base->sendBatch($request);
    }

    public function index()
    {
        return $this->base->index();
    }

    public function show(\App\Models\Payment $payment)
    {
        return $this->base->show($payment);
    }
}