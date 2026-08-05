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
        Schema::create('ticket_auto_update_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('min_before_escalation_to_handler')->nullable();
            $table->bigInteger('min_before_escalation_to_user')->nullable();
            $table->bigInteger('min_before_breached_to_technician')->nullable();
            $table->bigInteger('min_before_close_ticket_to_handler')->nullable();
            $table->timestamps();
            $table->boolean('auto_response_for_ticket')->nullable();
            $table->boolean('auto_resolve_ticket')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_auto_update_settings');
    }
};
