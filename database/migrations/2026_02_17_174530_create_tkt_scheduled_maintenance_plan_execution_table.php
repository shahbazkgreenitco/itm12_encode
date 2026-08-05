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
        Schema::create('tkt_scheduled_maintenance_plan_execution', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_allow_id');
            $table->integer('task_id');
            $table->integer('status')->comment('1-In Progress,2-Hold,3-Completed,4-Delay,5-Not started');
            $table->text('lattitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('proof_details')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_scheduled_maintenance_plan_execution');
    }
};
