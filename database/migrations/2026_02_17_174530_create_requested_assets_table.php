<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * FIX: MySQL 8+ does not allow '0000-00-00 00:00:00' as a default
     * value for timestamp columns — causes Error 1067: Invalid default value.
     *
     * Use $table->timestamps() which sets proper CURRENT_TIMESTAMP defaults,
     * or use ->nullable() if the column can be null.
     */
    public function up(): void
    {
        Schema::create('requested_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_id');
            $table->integer('user_id');
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('denied_at')->nullable();
            $table->string('notes');
            $table->timestamps(); // ✅ created_at & updated_at with proper defaults
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requested_assets');
    }
};
