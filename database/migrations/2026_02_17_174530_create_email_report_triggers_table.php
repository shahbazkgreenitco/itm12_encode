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
        Schema::create('email_report_triggers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('creator_id');
            $table->text('trigger_name');
            $table->text('trigger_description')->nullable();
            $table->text('trigger_cc_to')->nullable();
            $table->text('trigger_report_elements')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->text('report_departments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_report_triggers');
    }
};
