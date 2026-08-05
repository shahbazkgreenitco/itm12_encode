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
        Schema::create('itm_network_inventory_monitors', function (Blueprint $table) {
            $table->increments('id')->index('id_itm_ni_monitors_idx');
            $table->integer('basic_id')->nullable()->index('basic_id_itm_ni_monitors_idx');
            $table->unsignedInteger('component_id')->nullable()->index('component_id_itm_ni_monitors_idx');
            $table->string('monitor_name')->nullable()->index('monitor_name_itm_ni_monitors_idx');
            $table->string('serial')->nullable()->index('serial_itm_ni_monitors_idx');
            $table->mediumText('others')->nullable();
            $table->timestamps();

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_monitors');
    }
};
