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
        Schema::create('kanban_board_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('board_id')->nullable()->index('idx_board_id');
            $table->text('item_name')->nullable();
            $table->integer('item_type')->nullable()->comment('1 - status | 2 - custom');
            $table->timestamp('created_at')->nullable()->index('idx_created_at');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->integer('position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_board_items');
    }
};
