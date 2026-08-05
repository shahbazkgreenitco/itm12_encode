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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('color')->nullable()->default(null)->change();
            $table->string('sidebar_color')->nullable()->default(null)->change();
            $table->string('light_color')->nullable()->default(null)->change();
            $table->string('primary_color')->nullable()->default(null)->change();
            $table->string('secondary_color')->nullable()->default(null)->change();
            $table->string('history_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Reverse only if required
        });
    }
};