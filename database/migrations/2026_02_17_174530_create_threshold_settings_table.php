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
        Schema::create('threshold_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('threshold_enabled')->nullable()->default(1)->comment('1 for enabled; 0 for disabled');
            $table->tinyInteger('alerts_enabled')->nullable()->default(1)->comment('1 for enabled; 0 for disabled');
            $table->tinyInteger('send_alerts')->nullable()->default(0)->comment('0 for as per general settings alerts configuration; 1 for following email address');
            $table->string('email')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threshold_settings');
    }
};
