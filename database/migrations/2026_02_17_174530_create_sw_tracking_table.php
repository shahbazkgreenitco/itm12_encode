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
        Schema::create('sw_tracking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sw_name', 191)->nullable();
            $table->string('sw_version', 191)->nullable();
            $table->string('sw_publisher', 191)->nullable();
            $table->string('serial', 191)->comment('device serial number');
            $table->integer('license_id');
            $table->date('date')->nullable();
            $table->integer('sw_usage_time');
            $table->string('name', 191)->nullable()->comment('username if not in users table');
            $table->integer('user_id')->nullable()->comment('user_id is from user table');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sw_tracking');
    }
};
