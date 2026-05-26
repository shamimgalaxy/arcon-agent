<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('spending_policies', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agent_id')->constrained()->onDelete('cascade');
        $table->decimal('max_per_transaction', 20, 6)->nullable(); // USDC
        $table->decimal('daily_limit', 20, 6)->nullable();         // USDC
        $table->decimal('monthly_limit', 20, 6)->nullable();       // USDC
        $table->json('allowed_wallet_ids')->nullable();            // whitelist
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
};