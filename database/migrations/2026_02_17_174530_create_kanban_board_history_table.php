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
        Schema::create('kanban_board_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('board_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->string('action_type', 100)->nullable();
            $table->text('old_name')->nullable();
            $table->text('new_name')->nullable();
            $table->longText('old_description')->nullable();
            $table->longText('new_description')->nullable();
            $table->string('old_dept_id', 191)->nullable();
            $table->string('new_dept_id', 191)->nullable();
            $table->string('old_problem_category_id', 191)->nullable();
            $table->string('new_problem_category_id', 191)->nullable();
            $table->string('old_sub_category_id', 191)->nullable();
            $table->string('new_sub_category_id', 191)->nullable();
            $table->integer('old_board_item_type')->nullable();
            $table->integer('new_board_item_type')->nullable();
            $table->boolean('old_auto_comment_on_card_change')->nullable();
            $table->boolean('new_auto_comment_on_card_change')->nullable();
            $table->boolean('old_access_to_all_dept_tech')->nullable();
            $table->boolean('new_access_to_all_dept_tech')->nullable();
            $table->string('old_item_id', 191)->nullable();
            $table->string('new_item_id', 191)->nullable();
            $table->string('old_custom_fieldset_id', 500)->nullable();
            $table->string('new_custom_fieldset_id', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_board_history');
    }
};
