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
        Schema::create('outgoing_email_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('mail_service_enabled')->comment('0 = Enabled, 2 = Disabled');
            $table->string('mail_driver', 191)->comment('smtp = SMTP');
            $table->string('mail_host', 191);
            $table->string('mail_port', 191);
            $table->string('mail_encryption', 191)->comment('ssl = SSL, tls = TLS, false = False');
            $table->string('mail_username', 191);
            $table->string('mail_password', 191);
            $table->string('mail_from_address', 191);
            $table->string('mail_from_name', 191);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_email_settings');
    }
};
