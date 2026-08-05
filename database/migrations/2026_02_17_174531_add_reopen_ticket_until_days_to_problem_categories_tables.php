<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReopenTicketUntilDaysToProblemCategoriesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tkt_problem_categories', function (Blueprint $table) {
            $table->integer('reopen_ticket_until_days')
                ->nullable()
                ->after('close_ticket_after_days');
        });

        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->integer('reopen_ticket_until_days')
                ->nullable()
                ->after('close_ticket_after_days');
        });
    }

    public function down()
    {
        Schema::table('tkt_problem_categories', function (Blueprint $table) {
            $table->dropColumn('reopen_ticket_until_days');
        });

        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->dropColumn('reopen_ticket_until_days');
        });
    }
}
