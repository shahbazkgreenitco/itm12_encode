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
            $table->unsignedBigInteger('problem_category_id')
                  ->default(20)
                  ->after('id'); // adjust position if needed
        });
    }

    public function down(): void
    {
        Schema::table('problem_category_tasks_defination', function (Blueprint $table) {
            $table->dropColumn('problem_category_id');
        });
    }
};
