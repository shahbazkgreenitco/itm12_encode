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
        Schema::create('itm_network_inventory_sound_devices', function (Blueprint $table) {
            $table->integer('id', true)->index('id_itm_network_inventory_sound_devices_idx');
            $table->integer('basic_id')->nullable()->index('basic_id_itm_network_inventory_sound_devices_idx')->comment('Reference for basic table');
            $table->string('Name')->nullable()->index('name_itm_network_inventory_sound_devices_idx');
            $table->string('Manufacturer')->nullable()->index('manufacturer_itm_network_inventory_sound_devices_idx');
            $table->timestamps();

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_sound_devices');
    }
};
