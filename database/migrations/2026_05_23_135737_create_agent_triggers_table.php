<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('trigger_type', ['balance_threshold', 'scheduled', 'task_event']);
            $table->boolean('is_active')->default(true);

            // ── Balance Threshold fields ────────────────────────────────────
            $table->decimal('threshold_amount', 20, 6)->nullable();
            $table->enum('threshold_direction', ['above', 'below'])->nullable();

            // ── Scheduled fields ────────────────────────────────────────────
            $table->unsignedInteger('interval_hours')->nullable();

            // ── Task Event fields ───────────────────────────────────────────
            $table->string('event_name')->nullable();

            // ── Payment details (shared by all trigger types) ───────────────
            $table->foreignId('receiver_agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->string('receiver_address')->nullable();
            $table->decimal('amount', 20, 6);
            $table->string('currency')->default('USDC');
            $table->text('note')->nullable();

            // ── Execution tracking ──────────────────────────────────────────
            $table->unsignedInteger('fired_count')->default(0);
            $table->timestamp('last_fired_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_triggers');
    }
};