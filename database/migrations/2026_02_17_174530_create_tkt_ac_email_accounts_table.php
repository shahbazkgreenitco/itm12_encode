<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * FIX: $table->comment() was removed in Laravel 9+.
     * Use DB::statement() to add table comment after creation.
     */
    public function up(): void
    {
        Schema::create('tkt_ac_email_accounts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('email', 100)->nullable();
        });

        DB::statement("ALTER TABLE `tkt_ac_email_accounts` COMMENT = 'For Ticket Auto Creation from desired Email Accounts'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_ac_email_accounts');
    }
};
