<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tkt_problem_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(1);
        });

        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(1);
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
            $table->dropColumn('company_id');
        });
        Schema::table('tkt_problem_categories_history', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
