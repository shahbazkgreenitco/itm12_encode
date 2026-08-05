<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCompanyIdToTktStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tkt_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
        });
        DB::table('tkt_statuses')->whereNull('company_id')->update(['company_id' => 1]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tkt_statuses', function (Blueprint $table) {
           $table->dropColumn('company_id');
        });
    }
}
