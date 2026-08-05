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
        Schema::create('itm_connect', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('source_id');
            $table->integer('mode_id');
            $table->text('reference_id');
            $table->text('caller_name')->nullable();
            $table->text('message')->nullable();
            $table->text('recording')->nullable();
            $table->text('ticket_id')->nullable();
            $table->timestamps();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration')->nullable();
            $table->string('from_number', 191)->nullable();
            $table->string('to_number', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_connect');
    }
};
