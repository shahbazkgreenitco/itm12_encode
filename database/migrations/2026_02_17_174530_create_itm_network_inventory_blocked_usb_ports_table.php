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
        Schema::create('itm_network_inventory_blocked_usb_ports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('itm_ni_usb_port_id');
            $table->boolean('is_blocked')->default(true)->comment('0-unblocked,1-blocked');
            $table->timestamps();
            $table->boolean('status')->default(false)->comment('0-pending, 1-complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_blocked_usb_ports');
    }
};
