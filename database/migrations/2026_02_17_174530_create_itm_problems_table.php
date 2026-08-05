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
        Schema::create('itm_problems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->nullable();
            $table->text('content')->nullable();
            $table->bigInteger('priority_id')->nullable();
            $table->bigInteger('status_id')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->bigInteger('problem_category_id')->nullable();
            $table->bigInteger('sub_category_id')->nullable();
            $table->longText('problem_handler_id')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_problems');
    }
};
