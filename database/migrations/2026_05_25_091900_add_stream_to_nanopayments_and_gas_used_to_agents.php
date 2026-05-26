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
    Schema::table('nanopayments', function (Blueprint $table) {
        $table->boolean('stream')->default(false)->after('note');
    });

    Schema::table('agents', function (Blueprint $table) {
        $table->decimal('gas_used_usdc', 10, 4)->default(0)->after('gas_usdc_limit');
    });
}

public function down(): void
{
    Schema::table('nanopayments', function (Blueprint $table) {
        $table->dropColumn('stream');
    });

    Schema::table('agents', function (Blueprint $table) {
        $table->dropColumn('gas_used_usdc');
    });
}
};
