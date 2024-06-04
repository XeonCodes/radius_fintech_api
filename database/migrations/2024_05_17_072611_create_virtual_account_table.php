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
        Schema::create('virtual_account', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('account_id');
            $table->string('account_reference');
            $table->string('account_number');
            $table->string('account_name');
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
        Schema::dropIfExists('virtual_account');
    }
};
