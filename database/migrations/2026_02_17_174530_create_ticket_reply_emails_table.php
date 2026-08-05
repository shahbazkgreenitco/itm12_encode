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
        Schema::create('ticket_reply_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mail_service_enabled');
            $table->string('mail_driver', 191);
            $table->string('mail_host', 191);
            $table->string('mail_port', 191);
            $table->string('mail_username', 191);
            $table->string('mail_password', 191);
            $table->string('mail_encryption', 191);
            $table->string('mail_from_address', 191);
            $table->string('mail_from_name', 191);
            $table->string('mailgun_domain', 191);
            $table->string('mailgun_secret', 191);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_reply_emails');
    }
};
