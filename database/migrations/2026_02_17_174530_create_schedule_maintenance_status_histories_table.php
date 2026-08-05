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
        Schema::create('schedule_maintenance_status_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('action_type');
            $table->integer('allocation_id');
            $table->integer('status_id');
            $table->text('remark');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_maintenance_status_histories');
    }
};
