<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalPlaceIdsToEscalationGroupUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('escalation_group_users', function (Blueprint $table) {
            $table->string('internal_place_ids')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('escalation_group_users', function (Blueprint $table) {
            $table->dropColumn('internal_place_ids');
        });
    }
}
