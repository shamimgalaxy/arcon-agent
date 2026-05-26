<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_agent_id')->constrained('agents');
            $table->foreignId('receiver_agent_id')->constrained('agents');
            $table->decimal('amount', 20, 6);
            $table->string('currency')->default('USDC');
            $table->string('circle_transaction_id')->nullable();
            $table->string('status')->default('pending');
            $table->string('blockchain')->default('BASE-SEPOLIA');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};