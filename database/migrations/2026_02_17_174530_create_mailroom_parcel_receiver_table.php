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
        Schema::create('mailroom_parcel_receiver', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parcel_id');
            $table->string('receiver_name', 191);
            $table->string('receiver_phone', 191)->nullable();
            $table->string('receiver_email', 191)->nullable();
            $table->text('receiver_address')->nullable();
            $table->text('receiver_zipcode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailroom_parcel_receiver');
    }
};
