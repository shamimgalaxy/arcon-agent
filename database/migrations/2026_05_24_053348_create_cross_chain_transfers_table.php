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
        Schema::create('cross_chain_transfers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('agent_id')->constrained()->onDelete('cascade');
    $table->string('source_chain')->default('base-sepolia');
    $table->string('destination_chain');
    $table->string('destination_address');
    $table->decimal('amount_usdc', 20, 6);
    $table->string('status')->default('pending');
    // pending → burned → attested → minted → failed
    $table->string('burn_tx_hash')->nullable();
    $table->text('attestation')->nullable();
    $table->text('message_hex')->nullable();
    $table->string('mint_tx_hash')->nullable();
    $table->string('usdc_contract')->nullable();
    $table->string('token_messenger')->nullable();
    $table->integer('destination_domain')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cross_chain_transfers');
    }
};
