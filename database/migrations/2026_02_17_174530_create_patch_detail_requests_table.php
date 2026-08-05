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
        Schema::create('patch_detail_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->tinyInteger('approved_status')->default(0)->comment('0-Waiting for Approval, 1-Approved, 2-Auto Approved, 3-Rejected');
            $table->bigInteger('patch_id')->nullable();
            $table->tinyInteger('operation_type')->nullable();
            $table->tinyInteger('patch_based_on')->default(1)->comment('Determines the patch based on: 1 for Name-based, 2 for Version-based');
            $table->tinyInteger('user_type')->default(1)->comment('1-Admin User, 2-User');
            $table->tinyInteger('request_reboot')->default(0)->comment('0-Not Required, 1-Required');
            $table->tinyInteger('deployment_type')->nullable()->comment('0-User Intervention, 1-Silent');
            $table->integer('policy_id')->nullable();
            $table->integer('deploy_to')->default(1)->comment('1-Device, 2-Group');
            $table->integer('retry')->nullable();
            $table->tinyInteger('enable_notification')->default(0);
            $table->longText('email')->nullable();
            $table->tinyInteger('scheduler')->nullable()->comment('1-Install After, 2-Install Before');
            $table->dateTime('schedule_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_detail_requests');
    }
};
