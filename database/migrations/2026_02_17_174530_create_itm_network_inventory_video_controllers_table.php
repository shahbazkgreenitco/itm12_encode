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
        Schema::create('itm_network_inventory_video_controllers', function (Blueprint $table) {
            $table->integer('id', true)->index('id_itm_ni_video_idx');
            $table->integer('basic_id')->nullable()->index('basic_id_itm_ni_video_idx')->comment('Reference for basic table');
            $table->string('Caption')->nullable()->index('caption_itm_ni_video_idx');
            $table->string('DriverDate')->nullable()->index('driverdate_itm_ni_video_idx');
            $table->string('DriverVersion')->nullable()->index('driverversion_itm_ni_video_idx');
            $table->string('VideoProcessor')->nullable()->index('videoprocessor_itm_ni_video_idx');
            $table->timestamps();

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_video_controllers');
    }
};
