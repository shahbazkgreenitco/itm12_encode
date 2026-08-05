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
        Schema::create('tasks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->tinyInteger('type_id')->nullable()->comment('1-Project, 2-Change, 3-Other');
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
            $table->timestamps();
            $table->text('description')->nullable();
            $table->bigInteger('ticket_id')->nullable();
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
        Schema::dropIfExists('tasks');
    }
};
