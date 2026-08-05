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
        Schema::create('patch_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('auto_approval')->default(0)->comment('0-No, 1-Yes');
            $table->string('auto_approval_type', 191)->nullable()->default('1')->comment('1-Based On Severity, 2-Based On Category (OS, Hardware, Software)');
            $table->tinyInteger('recursion_plan')->nullable()->comment('1-OneTime, 2-Daily, 3-Weekly, 4-Monthly, 5-Yearly');
            $table->longText('recursion_plan_time')->nullable();
            $table->string('cron_expression', 191)->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('approved_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_settings');
    }
};
