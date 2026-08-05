<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDuplicateTicketDetectionFlow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_auto_update_settings', function (Blueprint $table) {
            $table->boolean('enable_duplicate_issues_check')->nullable();
            $table->boolean('auto_merge_duplicate_issues')->nullable();
            $table->boolean('allow_user_to_continue_ticket_creation_on_duplicate')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_auto_update_settings', function (Blueprint $table) {
            $table->dropColumn('enable_duplicate_issues_check');
            $table->dropColumn('auto_merge_duplicate_issues');
            $table->dropColumn('allow_user_to_continue_ticket_creation_on_duplicate');
        });
    }
}
