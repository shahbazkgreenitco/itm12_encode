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
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('mail_enabled')->nullable()->default(0);
            $table->string('mail_driver', 191)->nullable();
            $table->string('mail_host', 191)->nullable();
            $table->string('mail_port', 191)->nullable();
            $table->string('mail_username', 191)->nullable();
            $table->string('mail_password', 191)->nullable();
            $table->string('mail_encryption', 191)->nullable();
            $table->string('mail_from_address', 191)->nullable();
            $table->string('mail_from_name', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
