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
        Schema::create('custom_reports', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('report_name', 100)->nullable()->unique('report_name');
            $table->tinyInteger('tot_fields')->nullable();
            $table->text('fields')->nullable();
            $table->timestamps();
            $table->text('dictated_rules')->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->integer('report_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_reports');
    }
};
