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
        Schema::create('live_monitor_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('device_id')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('cpu_usage')->nullable();
            $table->bigInteger('ram_usage')->nullable();
            $table->bigInteger('battery_percentage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_monitor_devices');
    }
};
