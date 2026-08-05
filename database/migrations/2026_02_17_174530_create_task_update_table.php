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
        Schema::create('task_update', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sch_device_id')->nullable()->comment('schedule Id from schedule maintenancetable');
            $table->integer('plan_id')->nullable()->comment('plan id of particular task');
            $table->integer('task_id')->nullable()->comment('task of particular devices');
            $table->integer('scheduled_id')->nullable()->comment('schedule id of the device');
            $table->integer('updated_by')->nullable()->comment('updated User');
            $table->tinyInteger('task_status')->default(1)->comment('1 = Not Completed, 2 = Started 3=Not Completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_update');
    }
};
