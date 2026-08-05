<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tkt_ai_email_accounts', function (Blueprint $table) {
            // $table->comment('For Ticket Auto Creation from desired email address');
            $table->integer('id', true);
            $table->string('email', 100)->nullable();
        });

        DB::statement("ALTER TABLE `tkt_ai_email_accounts` COMMENT = 'For Ticket Auto Creation from desired email address'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_ai_email_accounts');
    }
};
