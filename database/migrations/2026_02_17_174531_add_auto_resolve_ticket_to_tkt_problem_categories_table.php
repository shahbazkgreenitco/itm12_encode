<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoResolveTicketToTktProblemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tkt_problem_categories', function (Blueprint $table) {
            $table->boolean('auto_resolve_ticket')->default(0)->after('privilege_access'); 
        });
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->boolean('auto_resolve_ticket')->default(0)->after('privilege_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tkt_problem_categories', function (Blueprint $table) {
            $table->dropColumn('auto_resolve_ticket');
        });
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->dropColumn('auto_resolve_ticket');
        });
    }
}
