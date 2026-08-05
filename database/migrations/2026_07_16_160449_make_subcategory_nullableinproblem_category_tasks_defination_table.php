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
        //
        Schema::table('problem_category_tasks_defination', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('problem_category_tasks_defination', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable(false)->change();
        });
    }
};
