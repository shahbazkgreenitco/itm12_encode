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
        Schema::create('cc_email_group_departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cc_group_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('problem_category_id');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cc_email_group_departments');
    }
};
