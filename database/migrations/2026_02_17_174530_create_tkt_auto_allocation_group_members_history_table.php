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
        Schema::create('tkt_auto_allocation_group_members_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('group_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('max_ticket_per_hr')->nullable();
            $table->bigInteger('max_ticket_per_day')->nullable();
            $table->integer('assign_in_flow')->nullable();
            $table->string('remark', 191)->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->string('location_id')->nullable();
            $table->string('internal_place_id', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_auto_allocation_group_members_history');
    }
};
