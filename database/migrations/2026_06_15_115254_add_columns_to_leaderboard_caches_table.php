<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaderboard_caches', function (Blueprint $table) {
            $table->double('not_responded_tickets', 8, 2)->default(0.00);
            $table->text('date_range')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leaderboard_caches', function (Blueprint $table) {
            $table->dropColumn(['not_responded_tickets','date_range',]);
        });
    }
};