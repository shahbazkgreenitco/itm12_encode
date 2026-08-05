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
        Schema::create('tkt_esclation_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id')->nullable();
            $table->unsignedInteger('escl_id')->nullable();
            $table->timestamps();
            $table->integer('status_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->integer('tat')->nullable();
            $table->timestamp('tat_expire')->nullable();
            $table->integer('escalate_for')->nullable()->comment('Escalate for User/Group');
            $table->string('escalate_to', 191)->nullable();
            $table->tinyInteger('technician_mark_cc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_esclation_logs');
    }
};
