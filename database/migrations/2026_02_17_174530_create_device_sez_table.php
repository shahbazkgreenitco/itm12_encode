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
        Schema::create('device_sez', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('device_id')->nullable();
            $table->timestamps();
            $table->text('_itm_sez_tool_tracking_no')->nullable();
            $table->text('_itm_economic_zone_type1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_sez');
    }
};
