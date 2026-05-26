<?php

use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use App\Models\Agent;
Route::get('/dashboard', function () {
   $payments = Payment::with(['senderAgent', 'receiverAgent'])->latest()->take(20)->get();
    $agents   = Agent::all();
    return view('dashboard', compact('payments', 'agents'));

});

Route::get('/', function () {
    return view('app');
});

Route::get('/m3', fn() => view('m3'));