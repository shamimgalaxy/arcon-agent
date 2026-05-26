<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('audit_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agent_id')->constrained()->onDelete('cascade');
        $table->string('event');           // e.g. payment_submitted, policy_violated, payment_failed
        $table->string('status');          // allowed, blocked, failed
        $table->decimal('amount', 20, 6)->nullable();
        $table->string('currency')->default('USDC');
        $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
        $table->json('meta')->nullable();  // extra context (limit, used, reason etc.)
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};