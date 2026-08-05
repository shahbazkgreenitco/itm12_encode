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
        Schema::create('patch_request_device_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('patch_request_id')->nullable();
            $table->bigInteger('device_id')->nullable();
            $table->bigInteger('status_id')->nullable();
            $table->longText('message')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();
            $table->integer('error_type')->nullable()->comment('1-Low, 2-Medium, 3-Risk, 4-High');
            $table->longText('error_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_request_device_logs');
    }
};
