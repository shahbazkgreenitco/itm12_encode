<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToKanbanBoardsTable extends Migration
{
    public function up()
    {
        Schema::table('kanban_boards', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')
                  ->default(1)
                  ->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('kanban_boards', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}