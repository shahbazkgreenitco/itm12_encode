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
        Schema::create('tkt_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name', 191);
            $table->string('name', 191)->index();
            $table->boolean('aborts_cycle')->default(false);
            $table->boolean('updates_ticket')->default(true);
            $table->text('input_config')->nullable();
            $table->integer('status')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_actions');
    }
};
