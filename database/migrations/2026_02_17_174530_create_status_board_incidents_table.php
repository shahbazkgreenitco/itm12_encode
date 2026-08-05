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
        Schema::create('status_board_incidents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->tinyInteger('status')->nullable()->default(0);
            $table->tinyInteger('is_enabled')->nullable()->default(1);
            $table->unsignedInteger('item_id')->nullable();
            $table->unsignedInteger('history_id')->nullable()->comment('To store last history id of incident');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_board_incidents');
    }
};
