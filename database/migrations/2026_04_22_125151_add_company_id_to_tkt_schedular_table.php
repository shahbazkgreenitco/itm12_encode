<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToTktSchedularTable extends Migration
{
    public function up()
    {
        Schema::table('tkt_schedular', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(1)->after('id');
        });
    }

    public function down()
    {
        Schema::table('tkt_schedular', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
