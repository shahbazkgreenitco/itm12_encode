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
        Schema::create('scheduled_device', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id');
            $table->integer('scheduled_id');
            $table->timestamps();
            $table->timestamp('device_schedule_start_date')->nullable();
            $table->timestamp('device_schedule_actual_start_date')->nullable();
            $table->timestamp('device_schedule_actual_end_date')->nullable();
            $table->integer('incharge_id')->nullable();
            $table->string('cost', 191)->nullable();
            $table->string('notes', 191)->nullable();
            $table->integer('amc_service_provider')->nullable();
            $table->tinyInteger('is_warranty')->nullable()->comment('1 - Yes');
            $table->tinyInteger('is_amc')->nullable()->comment('1 - Yes');
            $table->string('currency_format', 191)->nullable()->comment('add currency from Currency Table');
            $table->tinyInteger('status')->default(1)->comment('1 - Scheduled ,2 - In Process, 3 - On Hold , 4 - Canceled , 5 - Completed');
            $table->string('service_type', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_device');
    }
};
