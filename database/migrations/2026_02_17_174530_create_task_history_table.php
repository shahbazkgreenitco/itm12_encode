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
        Schema::create('task_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->tinyInteger('type_id')->nullable()->comment('1-Change, 2-Project, 3-Other, 4-Ticket');
            $table->integer('status_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->integer('team_size')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->decimal('cost', 11)->nullable();
            $table->integer('assigned_to')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('change_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->longText('remarks')->nullable();
            $table->integer('action_id')->nullable();
            $table->integer('change_by')->nullable();
            $table->integer('change_by_module')->nullable()->comment('1-task,2-ticket');
            $table->timestamps();
            $table->boolean('is_note')->nullable();
            $table->softDeletes();
            $table->boolean('is_visible_user')->default(false);
            $table->unsignedBigInteger('pc_task_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_history');
    }
};
