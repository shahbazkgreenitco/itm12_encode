<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeptCategoryColsToProblemCategoryTaskDefinationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('problem_category_task_defination_history', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('problem_category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problem_category_task_defination_history', function (Blueprint $table) {
            $table->dropColumn(['department_id','problem_category_id','sub_category_id']);
        });
    }
}
