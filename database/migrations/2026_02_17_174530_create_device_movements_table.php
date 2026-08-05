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
        Schema::create('device_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('device_id');
            $table->bigInteger('scanner_id');
            $table->string('antenna', 191)->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('place_id')->nullable();
            $table->integer('movement')->nullable();
            $table->timestamps();
            $table->bigInteger('user_id')->nullable();
            $table->text('rfid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_movements');
    }
};
