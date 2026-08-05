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
        Schema::table('problem_category_tasks_defination', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('problem_category_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problem_category_tasks_defination', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable(false)->default(null)->change();
            $table->unsignedBigInteger('problem_category_id')->nullable(false)->default(null)->change();
        });
    }
};