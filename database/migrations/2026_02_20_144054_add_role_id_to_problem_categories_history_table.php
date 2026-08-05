<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleIdToProblemCategoriesHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            //
            $table->bigInteger('role_id')->nullable()->after('privilege_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            //
            $table->dropColumn('role_id');
        });
    }
}
