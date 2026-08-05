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
        Schema::create('tkt_auto_allocation_group_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id');
            $table->bigInteger('user_id');
            $table->bigInteger('max_ticket_per_hr')->nullable();
            $table->bigInteger('max_ticket_per_day')->nullable();
            $table->bigInteger('assign_in_flow')->nullable();
            $table->timestamps();
            $table->string('location_id')->nullable();
            $table->string('internal_place_id', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_auto_allocation_group_members');
    }
};
