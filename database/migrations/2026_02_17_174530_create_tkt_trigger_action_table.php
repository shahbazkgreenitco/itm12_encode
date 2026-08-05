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
        Schema::create('tkt_trigger_action', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trigger_id')->index();
            $table->integer('action_id')->index();
            $table->text('action_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_trigger_action');
    }
};
