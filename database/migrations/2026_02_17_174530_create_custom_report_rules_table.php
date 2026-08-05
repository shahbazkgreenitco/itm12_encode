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
        Schema::create('custom_report_rules', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('report_id')->nullable();
            $table->integer('field_id')->nullable();
            $table->integer('condition_code')->nullable();
            $table->text('compare_values')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_report_rules');
    }
};
