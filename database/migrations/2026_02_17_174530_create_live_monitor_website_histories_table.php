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
        Schema::create('live_monitor_website_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('website_id');
            $table->string('latency', 191)->default('0');
            $table->string('statuscode', 191)->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_monitor_website_histories');
    }
};
