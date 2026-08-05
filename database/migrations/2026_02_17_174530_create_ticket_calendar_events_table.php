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
        Schema::create('ticket_calendar_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->string('subject', 191);
            $table->text('description')->nullable();
            $table->string('cc_users', 191)->nullable();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('technician_id');
            $table->unsignedBigInteger('creator_id');
            $table->softDeletes();
            $table->timestamps();
            $table->longText('comment')->nullable();
            $table->enum('status', ['open', 'complete'])->default('open');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_calendar_events');
    }
};
