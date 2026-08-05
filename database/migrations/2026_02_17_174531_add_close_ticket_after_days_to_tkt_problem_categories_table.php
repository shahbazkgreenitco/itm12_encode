<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloseTicketAfterDaysToTktProblemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tkt_problem_categories', function (Blueprint $table) {
            $table->integer('close_ticket_after_days')
                  ->nullable()
                  ->after('remarks'); 
        });
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->integer('close_ticket_after_days')
                  ->nullable()
                  ->after('remarks'); 
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
            $table->dropColumn('close_ticket_after_days');
        });
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->dropColumn('close_ticket_after_days');
        });

    }
}
