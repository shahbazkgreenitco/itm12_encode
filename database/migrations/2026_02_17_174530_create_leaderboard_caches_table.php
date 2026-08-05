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
        Schema::create('leaderboard_caches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('technician_id')->unique();
            $table->string('username', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('avatar', 191)->nullable();
            $table->string('full_name', 191)->nullable();
            $table->integer('total_tickets')->default(0);
            $table->string('total_feedback', 191)->default('0');
            $table->string('feedback_count', 191)->default('0');
            $table->string('sla_breached', 191)->default('0');
            $table->double('feedback_score')->default(0);
            $table->double('escalation_score')->default(0);
            $table->double('sla_score')->default(0);
            $table->double('response_score')->default(0);
            $table->double('total_score')->default(0);
            $table->double('avg_feedback')->default(0);
            $table->integer('escalation_count')->default(0);
            $table->double('avg_response_time')->default(0);
            $table->integer('rank')->default(0);
            $table->text('ai_summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard_caches');
    }
};
