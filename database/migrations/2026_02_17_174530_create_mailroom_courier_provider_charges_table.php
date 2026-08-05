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
        Schema::create('mailroom_courier_provider_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id');
            $table->decimal('base_charge', 10)->nullable();
            $table->decimal('rate_per_gram', 10, 4)->nullable();
            $table->integer('volumetric_divisor')->nullable();
            $table->boolean('enable_volumetric_charge')->default(false);
            $table->decimal('fragile_liquid_charge', 10)->nullable();
            $table->decimal('insurance_rate', 10)->nullable();
            $table->decimal('insurance_handling_charges', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailroom_courier_provider_charges');
    }
};
