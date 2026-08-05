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
        Schema::create('bp_tech_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sender_id', 191);
            $table->string('receiver_id', 191);
            $table->text('message');
            $table->tinyInteger('seen')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bp_tech_messages');
    }
};
