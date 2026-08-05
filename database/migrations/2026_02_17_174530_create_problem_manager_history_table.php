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
        Schema::create('problem_manager_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pm_id');
            $table->bigInteger('updated_by');
            $table->bigInteger('action_type');
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_manager_history');
    }
};
