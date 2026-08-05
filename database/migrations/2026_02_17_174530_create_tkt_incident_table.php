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
        Schema::create('tkt_incident', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_id');
            $table->integer('status_id')->nullable()->default(1);
            $table->integer('department_id')->nullable();
            $table->text('content')->nullable();
            $table->integer('problem_category_id')->nullable();
            $table->integer('sub_category_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->string('ticket_id', 191)->nullable();
            $table->integer('man_hour_loss')->nullable();
            $table->timestamp('incident_start_date')->nullable();
            $table->timestamp('incident_end_date')->nullable();
            $table->string('subject', 191)->nullable();
            $table->integer('financial_loss_amount')->nullable();
            $table->string('service_impacted', 191)->nullable();
            $table->integer('sla_breaches')->nullable();
            $table->integer('status')->nullable()->default(1);
            $table->text('filename')->nullable();
            $table->text('attachment_data')->nullable();
            $table->tinyInteger('is_temp')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_incident');
    }
};
