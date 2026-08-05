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
        Schema::create('itm_network_inventory_usb_ports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('DeviceName', 191)->nullable();
            $table->longText('Description')->nullable();
            $table->longText('DeviceID')->nullable();
            $table->timestamps();
            $table->integer('basic_id')->nullable()->index('itm_network_inventory_usb_ports_basic_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_usb_ports');
    }
};
