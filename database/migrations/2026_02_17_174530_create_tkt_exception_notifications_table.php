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
        Schema::create('tkt_exception_notifications', function (Blueprint $table) {
            // $table->comment('To log the ticket exceptions');
            $table->increments('id');
            $table->unsignedInteger('ticket_id')->nullable();
            $table->unsignedInteger('following_id')->nullable();
            $table->string('ac_email_message_id', 120)->nullable();
            $table->string('ac_email')->nullable();
            $table->string('subject', 1000)->nullable();
            $table->string('mail_from', 1000)->nullable();
            $table->dateTime('mail_datetime')->nullable();
            $table->string('reason', 555)->nullable();
            $table->tinyInteger('is_notified')->nullable()->default(0)->comment('0 - Not notified, 1 - Notified');
            $table->dateTime('notified_at')->nullable();
            $table->timestamps();
            $table->bigInteger('auto_creation_account_id')->nullable();
        });
        DB::statement("ALTER TABLE `tkt_exception_notifications` COMMENT = 'To log the ticket exceptions'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_exception_notifications');
    }
};
