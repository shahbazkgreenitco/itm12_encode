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
        Schema::create('tkt_ticket_trigger_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('trigger_id');
            $table->integer('updated_by');
            $table->integer('action_type');
            $table->string('new_name', 191)->nullable();
            $table->string('old_name', 191)->nullable();
            $table->text('new_description')->nullable();
            $table->text('old_description')->nullable();
            $table->integer('new_status')->nullable();
            $table->integer('old_status')->nullable();
            $table->string('old_match_type', 3)->nullable();
            $table->string('new_match_type', 3)->nullable();
            $table->integer('old_event_id')->nullable();
            $table->integer('new_event_id')->nullable();
            $table->integer('old_operator_id')->nullable();
            $table->integer('new_operator_id')->nullable();
            $table->text('old_condition_value')->nullable();
            $table->text('new_condition_value')->nullable();
            $table->integer('condition_row_number')->nullable();
            $table->integer('old_action_id')->nullable();
            $table->integer('new_action_id')->nullable();
            $table->text('old_action_value')->nullable();
            $table->text('new_action_value')->nullable();
            $table->timestamps();
            $table->text('other_messages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_ticket_trigger_history');
    }
};
