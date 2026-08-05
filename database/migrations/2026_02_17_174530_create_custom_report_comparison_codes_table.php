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
        Schema::create('custom_report_comparison_codes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('html_code', 30)->nullable();
            $table->string('text_code', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_report_comparison_codes');
    }
};
