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
        Schema::create('billing_unit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('rate_hr', 9)->nullable();
            $table->decimal('rate_day', 9)->nullable();
            $table->decimal('rate_week', 9)->nullable();
            $table->decimal('rate_month', 9)->nullable();
            $table->decimal('rate_quarterly', 9)->nullable();
            $table->decimal('rate_half_yearly', 9)->nullable();
            $table->decimal('rate_yearly', 9)->nullable();
            $table->integer('asset_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_unit');
    }
};
