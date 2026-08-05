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
        Schema::create('app_platforms', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->string('osVersion')->nullable();
            $table->string('uuid')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial')->nullable();
            $table->string('imei')->nullable();
            $table->string('meid')->nullable();
            $table->string('esn')->nullable();
            $table->string('imsi')->nullable();
            $table->string('android_id')->nullable();
            $table->string('device_platform')->nullable();
            $table->timestamps();
            $table->float('lat', 11)->nullable()->default(0);
            $table->float('lng', 11)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_platforms');
    }
};
