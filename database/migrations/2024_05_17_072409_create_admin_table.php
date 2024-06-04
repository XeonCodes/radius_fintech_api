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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->boolean("data_status")->default(true);
            $table->boolean("airtime_status")->default(true);
            $table->boolean("electricity_status")->default(true);
            $table->boolean("cable_status")->default(true);
            $table->boolean("education_status")->default(true);
            $table->boolean("transport_status")->default(true);
            $table->boolean("internet_status")->default(true);
            $table->boolean("link_status")->default(true);
            $table->decimal("daily_bonus", 60, 2)->default(0);
            $table->boolean("all_services_status")->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
