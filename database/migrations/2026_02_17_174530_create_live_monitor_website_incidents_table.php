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
        Schema::create('live_monitor_website_incidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('website_id');
            $table->unsignedInteger('alert_type_id');
            $table->string('alert_type', 191)->comment('1- HTTP response code,2- Load Time');
            $table->string('comparison', 191);
            $table->string('limit_value', 191);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('repeats');
            $table->dateTime('last_notification');
            $table->string('comment', 191)->nullable();
            $table->integer('ignore')->default(0);
            $table->boolean('status')->default(true)->comment('1-OK,2-Warning');
            $table->timestamps();
            $table->integer('resolved_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_monitor_website_incidents');
    }
};
