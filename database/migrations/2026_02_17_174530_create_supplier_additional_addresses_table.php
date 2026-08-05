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
        Schema::create('supplier_additional_addresses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('supplier_id');
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 32)->nullable();
            $table->string('country', 3)->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('zip', 10)->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->bigInteger('country_id')->nullable();
            $table->unsignedMediumInteger('state_id')->nullable()->index('state_id');
            $table->bigInteger('city_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_additional_addresses');
    }
};
