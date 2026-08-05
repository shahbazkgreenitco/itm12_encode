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
        Schema::create('tkt_ticket_status_history', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('auto_response')->default(0);
            $table->unsignedBigInteger('ticket_id')->index();
            $table->integer('status')->nullable()->index();
            $table->integer('updated_by');
            $table->integer('assigned_to')->nullable()->index();
            $table->boolean('is_note')->nullable();
            $table->integer('priority_id')->nullable();
            $table->integer('merge_ticket_id')->nullable();
            $table->bigInteger('tat_changed')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('pbm_cat_id')->nullable();
            $table->integer('sub_cat_id')->nullable();
            $table->boolean('action_type')->index();
            $table->boolean('is_deleted')->nullable();
            $table->timestamps();
            $table->bigInteger('tat')->nullable()->default(0);
            $table->text('ticket_type_custom_fields')->nullable();
            $table->integer('change_creator_id')->nullable();
            $table->integer('old_status')->nullable();
            $table->integer('old_assigned_to')->nullable();
            $table->integer('old_priority_id')->nullable();
            $table->integer('old_department_id')->nullable();
            $table->integer('old_pbm_cat_id')->nullable();
            $table->integer('old_sub_cat_id')->nullable();
            $table->integer('old_change_creator_id')->nullable();
            $table->text('custom_fields')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->integer('device_id')->nullable();
            $table->integer('old_device_id')->nullable();
            $table->longText('new_custom_field_values')->nullable();
            $table->longText('old_custom_field_values')->nullable();
            $table->string('remarks', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_ticket_status_history');
    }
};
