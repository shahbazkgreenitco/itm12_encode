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
        Schema::create('problem_category_tasks_defination', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order')->nullable();
            $table->bigInteger('pc_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('assign_to')->nullable()->comment('1 = group, 2 = user');
            $table->bigInteger('assigned_to_id')->nullable();
            $table->string('status_id', 191)->nullable();
            $table->integer('tat')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_visible_user')->default(false);
            $table->bigInteger('task_flow_type')->default(2)->comment('1 = sequential, 2 = bulk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_category_tasks_defination');
    }
};
