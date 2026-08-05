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
        Schema::create('consumables_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('consumable_id')->nullable();
            $table->string('assigned_for', 191)->default('1')->comment('1- user, 2- place, 3- device');
            $table->integer('assigned_to')->nullable();
            $table->timestamps();
            $table->bigInteger('ticket_id')->nullable();
            $table->integer('asset_logs_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables_users');
    }
};
