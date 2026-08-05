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
        Schema::create('itm_network_mapped_locations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('ip_from')->nullable();
            $table->string('ip_to')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('place_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_mapped_locations');
    }
};
