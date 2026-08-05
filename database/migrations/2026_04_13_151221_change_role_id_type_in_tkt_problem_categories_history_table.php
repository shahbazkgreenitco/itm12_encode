<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->string('role_id', 256)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->bigInteger('role_id')->nullable()->change();
        });
    }
};
