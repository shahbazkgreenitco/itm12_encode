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
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('updated_by')->nullable()->comment('who did it');
            $table->text('content')->nullable();
            $table->string('notify_people', 555)->nullable();
            $table->string('view_pending', 555)->nullable();
            $table->tinyInteger('info_type')->nullable();
            $table->timestamps();
            $table->boolean('new_ticket_created')->nullable();
            $table->boolean('ticket_reopened')->nullable();
            $table->boolean('ticket_commented')->nullable();
            $table->boolean('sla_breached')->nullable();
            $table->boolean('device_checkin')->nullable();
            $table->boolean('device_checkout')->nullable();
            $table->boolean('new_user_added')->nullable();
            $table->boolean('user_deleted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
