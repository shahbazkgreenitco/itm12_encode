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
        Schema::create('renewal_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('email_address', 191)->nullable();
            $table->bigInteger('ticket_id')->nullable();
            $table->string('procure_tag', 191)->nullable();
            $table->bigInteger('original_ticket_reference')->nullable();
            $table->integer('no_of_day')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewal_reminders');
    }
};
