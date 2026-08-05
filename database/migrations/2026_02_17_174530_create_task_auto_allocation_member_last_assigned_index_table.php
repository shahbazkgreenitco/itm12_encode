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
        Schema::create('task_auto_allocation_member_last_assigned_index', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->nullable();
            $table->bigInteger('assigned_index')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_auto_allocation_member_last_assigned_index');
    }
};
