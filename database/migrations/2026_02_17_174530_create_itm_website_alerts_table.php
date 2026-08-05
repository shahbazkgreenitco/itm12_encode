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
        Schema::create('itm_website_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('website_id');
            $table->string('alert_type', 191)->comment('1- HTTP response code,2- Load Time');
            $table->string('comparison', 191);
            $table->string('limit_value', 191);
            $table->integer('occurrence');
            $table->string('notify_to', 191);
            $table->integer('repeat');
            $table->boolean('status')->default(false)->comment('0-Inactive,1-Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_website_alerts');
    }
};
