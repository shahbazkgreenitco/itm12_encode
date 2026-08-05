<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tkt_auto_allocation_groups', function (Blueprint $table) {

            $table->unsignedBigInteger('company_id')
                  ->default(1)
                  ->after('id');

        });

        Schema::table('tkt_auto_allocation_groups_history', function (Blueprint $table) {

            $table->unsignedBigInteger('company_id')
                  ->default(1)
                  ->after('id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_auto_allocation_groups', function (Blueprint $table) {

            $table->dropColumn('company_id');

        });

        Schema::table('tkt_auto_allocation_groups_history', function (Blueprint $table) {

            $table->dropColumn('company_id');

        });
    }
};