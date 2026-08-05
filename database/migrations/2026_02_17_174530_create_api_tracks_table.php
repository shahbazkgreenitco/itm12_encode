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
        Schema::create('api_tracks', function (Blueprint $table) {
            $table->integer('track_id', true);
            $table->integer('user_id')->nullable();
            $table->tinyInteger('response_type')->nullable()->default(0)->comment('1-Success,2-Fail');
            $table->string('url')->nullable();
            $table->string('device_id', 30)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->longText('request_data')->nullable();
            $table->longText('response_msg')->nullable();
            $table->dateTime('tracked_at')->nullable();
            $table->string('apptoken', 35)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tracks');
    }
};
