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
        Schema::create('patch_request_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('patch_request_id')->nullable();
            $table->bigInteger('device_id')->nullable();
            $table->timestamps();
            $table->integer('status_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_request_devices');
    }
};
