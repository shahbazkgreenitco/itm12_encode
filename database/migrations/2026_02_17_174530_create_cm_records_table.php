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
        Schema::create('cm_records', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('change_type_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->string('change_implementer', 191)->nullable();
            $table->integer('change_manager')->nullable();
            $table->integer('change_requester')->nullable();
            $table->text('change_reviewer')->nullable();
            $table->integer('change_approver')->nullable();
            $table->integer('cab_id')->nullable();
            $table->integer('impact_id')->nullable();
            $table->integer('urgency')->nullable();
            $table->integer('priority_id')->nullable();
            $table->integer('risk_id')->nullable();
            $table->bigInteger('cost')->nullable();
            $table->string('record_tag')->nullable();
            $table->text('subject')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('scheduled_start_date')->nullable();
            $table->dateTime('scheduled_end_date')->nullable();
            $table->string('impacted_services')->nullable();
            $table->string('impacted_devices')->nullable();
            $table->string('status_comments')->nullable();
            $table->longText('change_description')->nullable();
            $table->longText('reason_description')->nullable();
            $table->longText('risk_description')->nullable();
            $table->longText('impact_description')->nullable();
            $table->longText('rollout_plan')->nullable();
            $table->longText('fallback_plan')->nullable();
            $table->integer('close_state')->nullable();
            $table->dateTime('closed_date')->nullable();
            $table->string('currency', 191)->nullable();
            $table->integer('downtime')->nullable()->comment('1-Yes, 2-No');
            $table->dateTime('start_down_time')->nullable();
            $table->dateTime('end_down_time')->nullable();
            $table->tinyInteger('enable_communication')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->text('po_number')->nullable();
            $table->dateTime('po_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_records');
    }
};
