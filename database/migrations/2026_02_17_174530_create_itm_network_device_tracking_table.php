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
        Schema::create('itm_network_device_tracking', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('device_id')->nullable()->comment('id from asset tbl');
            $table->integer('map_loc_id')->nullable();
            $table->timestamps();
            $table->string('ip', 120)->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('place_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_device_tracking');
    }
};
