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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('session_id', 191)->nullable();
            $table->timestamp('last_active')->nullable();
            $table->timestamp('logged_out')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('headers')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 2 = Logged Out, 3 = Session Expired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
