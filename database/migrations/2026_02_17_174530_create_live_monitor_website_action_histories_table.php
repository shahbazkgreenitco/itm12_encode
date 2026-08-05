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
        Schema::create('live_monitor_website_action_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('action_type', 191)->nullable();
            $table->unsignedBigInteger('website_id')->nullable();
            $table->unsignedBigInteger('alert_id')->nullable();
            $table->unsignedBigInteger('incident_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('notes', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_monitor_website_action_histories');
    }
};
