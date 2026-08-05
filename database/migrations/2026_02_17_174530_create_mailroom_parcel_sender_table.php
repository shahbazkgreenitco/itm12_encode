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
        Schema::create('mailroom_parcel_sender', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parcel_id');
            $table->string('sender_name', 191);
            $table->string('sender_phone', 191)->nullable();
            $table->string('sender_email', 191)->nullable();
            $table->text('sender_address')->nullable();
            $table->text('sender_zipcode')->nullable();
            $table->timestamps();
            $table->integer('sender_city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailroom_parcel_sender');
    }
};
