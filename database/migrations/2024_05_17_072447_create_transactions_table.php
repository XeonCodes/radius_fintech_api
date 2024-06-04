<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('type');
            $table->string('txf')->unique();
            $table->string('x_ref');
            $table->string('session_id');
            $table->decimal('amount', 60, 2);
            $table->decimal('fee', 60, 2);
            $table->decimal('balance_before', 60, 2);
            $table->decimal('balance_after', 60, 2);
            $table->string('trans_type');
            $table->string('account_type');
            $table->string('beneficiary');
            $table->string('status');
            $table->string('narration');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('bank_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
