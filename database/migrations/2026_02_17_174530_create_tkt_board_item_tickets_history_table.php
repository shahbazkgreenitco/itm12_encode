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
        Schema::create('tkt_board_item_tickets_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('action_id')->comment('1: create, 2: update, 3: comment');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('old_title', 500)->nullable();
            $table->string('new_title', 500)->nullable();
            $table->longText('old_description')->nullable();
            $table->longText('new_description')->nullable();
            $table->integer('old_status')->nullable();
            $table->integer('new_status')->nullable();
            $table->integer('old_priority')->nullable();
            $table->integer('new_priority')->nullable();
            $table->dateTime('old_expected_date')->nullable();
            $table->dateTime('new_expected_date')->nullable();
            $table->unsignedBigInteger('old_ticket_reference')->nullable();
            $table->unsignedBigInteger('new_ticket_reference')->nullable();
            $table->string('old_assigned_to', 500)->nullable();
            $table->string('new_assigned_to', 500)->nullable();
            $table->integer('old_item_id')->nullable();
            $table->integer('new_item_id')->nullable();
            $table->integer('old_department_id')->nullable();
            $table->integer('new_department_id')->nullable();
            $table->integer('old_problem_category_id')->nullable();
            $table->integer('new_problem_category_id')->nullable();
            $table->integer('old_sub_category_id')->nullable();
            $table->integer('new_sub_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_board_item_tickets_history');
    }
};
