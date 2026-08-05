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
         Schema::table('tkt_user_privileges', function (Blueprint $table) {
            $table->bigInteger('company_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_user_privileges', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
    }
};
