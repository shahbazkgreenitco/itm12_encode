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
        Schema::create('notification_config', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('new_ticket_created')->nullable()->default(0);
            $table->integer('ticket_reopened')->nullable()->default(0);
            $table->integer('ticket_commented')->nullable()->default(0);
            $table->integer('sla_breached')->nullable()->default(0);
            $table->integer('device_checkin')->nullable()->default(0);
            $table->integer('device_checkout')->nullable()->default(0);
            $table->integer('new_user_added')->nullable()->default(0);
            $table->integer('user_deleted')->nullable()->default(0);
            $table->timestamps();
            $table->boolean('whatsapp_notification_enabled')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_config');
    }
};
