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
        Schema::create('custom_report_available_fields', function (Blueprint $table) {
            $table->integer('id', true);
            $table->tinyInteger('report_module')->nullable();
            $table->string('field_name', 100)->nullable();
            $table->string('possible_checks', 40)->nullable();
            $table->string('value_box', 40)->nullable();
            $table->tinyInteger('value_type')->nullable()->default(1)->comment('1-Text, 2-Number, 3-Dropdown 4-Datepicker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_report_available_fields');
    }
};
