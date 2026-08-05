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
        Schema::create('kanban_board_member_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('board_id');
            $table->bigInteger('old_user_id')->nullable();
            $table->bigInteger('new_user_id')->nullable();
            $table->integer('old_access_type')->nullable();
            $table->integer('new_access_type')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('action_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_board_member_history');
    }
};
