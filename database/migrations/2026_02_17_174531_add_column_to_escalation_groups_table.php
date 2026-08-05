<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToEscalationGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('escalation_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });
        DB::table('escalation_groups')->whereNull('company_id')->update(['company_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('escalation_groups', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
