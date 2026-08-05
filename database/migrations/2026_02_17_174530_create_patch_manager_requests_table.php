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
        Schema::create('patch_manager_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('operation_type')->nullable();
            $table->bigInteger('patch_id')->nullable();
            $table->tinyInteger('patch_based_on')->default(1)->comment('Determines the patch based on: 1 for Name-based, 2 for Version-based');
            $table->integer('user_type')->default(1)->comment('1-System User, 2-User');
            $table->tinyInteger('reboot')->default(0);
            $table->integer('policy_id')->nullable();
            $table->integer('deploy_to')->comment('1-Device, 2-Group');
            $table->integer('retry')->nullable();
            $table->tinyInteger('enable_notification')->default(0);
            $table->longText('email')->nullable();
            $table->integer('scheduler')->nullable()->comment('1- Install After, 2- Install Before');
            $table->dateTime('schedule_time')->nullable();
            $table->timestamps();
            $table->tinyInteger('system_update_type')->default(0)->comment('0 - Not System Software, 1 - System Software');
            $table->tinyInteger('deployment_type')->nullable()->comment('0 - Silent, 1 - User Intervention');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_manager_requests');
    }
};
