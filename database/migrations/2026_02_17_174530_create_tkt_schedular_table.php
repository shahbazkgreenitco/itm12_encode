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
        Schema::create('tkt_schedular', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_id');
            $table->integer('status_id')->nullable()->default(1);
            $table->integer('department_id')->nullable();
            $table->text('content')->nullable();
            $table->integer('problem_category_id')->nullable();
            $table->integer('sub_category_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->integer('device_id')->nullable();
            $table->integer('tat')->nullable();
            $table->timestamp('tat_expire')->nullable();
            $table->string('subject', 191)->nullable();
            $table->integer('ac_email_id')->nullable();
            $table->string('action_data', 191)->nullable();
            $table->text('action_expression')->nullable();
            $table->string('cron_expression', 191)->nullable();
            $table->string('filename', 191)->nullable();
            $table->text('attachment_data')->nullable();
            $table->tinyInteger('is_temp')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('new_ticket_reference')->nullable();
            $table->string('seat_no', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_schedular');
    }
};
