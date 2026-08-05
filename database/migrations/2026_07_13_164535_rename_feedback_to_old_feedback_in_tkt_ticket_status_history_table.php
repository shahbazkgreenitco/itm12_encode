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
        Schema::table('tkt_ticket_status_history', function (Blueprint $table) {
            $table->tinyInteger('feedback')->nullable();
            $table->tinyInteger('old_feedback')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_ticket_status_history', function (Blueprint $table) {
            $table->dropColumn(['feedback', 'old_feedback']);
        });
    }
};