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
        Schema::create('status_board_incident_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('iid');
            $table->text('note')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
            $table->tinyInteger('item_status')->nullable()->default(0);
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_board_incident_histories');
    }
};
