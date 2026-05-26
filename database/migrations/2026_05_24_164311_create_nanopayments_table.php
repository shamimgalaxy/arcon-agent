<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nanopayments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sender_agent_id')->constrained('agents');
    $table->foreignId('receiver_agent_id')->constrained('agents');
    $table->decimal('amount', 18, 6);
    $table->bigInteger('amount_micro');
    $table->string('currency')->default('USDC');
    $table->string('purpose')->default('general');
    $table->string('note')->nullable();
    $table->string('status')->default('pending');
    $table->string('gateway_payment_id')->nullable();
    $table->timestamp('verified_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nanopayments');
    }
};
