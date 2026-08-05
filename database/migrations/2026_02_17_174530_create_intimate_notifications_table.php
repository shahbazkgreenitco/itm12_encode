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
        Schema::create('intimate_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('device_id');
            $table->bigInteger('intimate_for')->default(1);
            $table->boolean('intimate_status')->default(false);
            $table->longText('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intimate_notifications');
    }
};
