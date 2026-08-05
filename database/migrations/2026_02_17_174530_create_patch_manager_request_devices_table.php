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
        Schema::create('patch_manager_request_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('patch_manager_request_id')->nullable();
            $table->bigInteger('device_id')->nullable();
            $table->tinyInteger('status_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_manager_request_devices');
    }
};
