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
        Schema::create('iana_reg_pvt_enterprises', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('DecimalCode')->nullable();
            $table->string('Organization', 500)->nullable();
            $table->string('Contact')->nullable();
            $table->string('Email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iana_reg_pvt_enterprises');
    }
};
