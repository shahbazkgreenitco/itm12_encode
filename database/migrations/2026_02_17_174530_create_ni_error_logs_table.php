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
        Schema::create('ni_error_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial', 191)->nullable();
            $table->string('ip', 191)->nullable();
            $table->string('mac', 191)->nullable();
            $table->string('version', 191)->nullable();
            $table->text('data')->nullable();
            $table->string('response_code', 191);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ni_error_logs');
    }
};
