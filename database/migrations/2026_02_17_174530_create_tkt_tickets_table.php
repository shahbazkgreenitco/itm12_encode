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
        Schema::create('tkt_tickets', function (Blueprint $table) {
            $table->bigIncrements('id')->index('id');
            $table->string('ticket_tag', 191)->nullable();
            $table->integer('status_id')->nullable()->index('idx_status_id');
            $table->integer('priority_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->integer('problem_category_id')->nullable();
            $table->integer('sub_category_id')->nullable()->comment('Id from Problem category table');
            $table->string('subject', 1000)->nullable();
            $table->longText('content')->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->string('starred')->nullable();
            $table->longText('note')->nullable();
            $table->integer('visible_group_id')->nullable();
            $table->tinyInteger('created_via')->nullable()->comment('"1"=> "Portal","2" => "Chat", "3" => "Email", "4" => "Mobile", "5" => "Call", "6" => "BOT"');
            $table->unsignedInteger('creator_id')->nullable()->default(1);
            $table->unsignedInteger('updated_by')->nullable()->default(1);
            $table->timestamp('created_at')->nullable()->index('idx_created_at');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->integer('deleted_by')->nullable()->comment('who delete this ticket');
            $table->tinyInteger('is_temp')->nullable()->default(1);
            $table->integer('tat')->nullable();
            $table->timestamp('tat_expire')->nullable();
            $table->integer('created_by')->nullable()->comment('Who actually created ticket behelf of another user');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('reopened_at')->nullable()->comment('Reopen Date and Time');
            $table->integer('reopened_by')->nullable()->comment('who reopen the ticket');
            $table->integer('assigned_to')->nullable()->comment('Person who is responsible to resolve the ticket');
            $table->tinyInteger('feedback')->nullable();
            $table->boolean('spam')->nullable();
            $table->integer('tat_remaining_mins')->nullable()->default(0)->comment('to store halt mins');
            $table->integer('device_id')->nullable();
            $table->integer('ac_email_id')->nullable();
            $table->string('ac_email_uid')->nullable();
            $table->string('ac_email_message_id')->nullable();
            $table->string('merged_ids', 555)->nullable()->comment('Ticket IDs of which are involved merge');
            $table->tinyInteger('is_merge_primary')->nullable()->comment('1 - Yes. It is primary on merged tickets, NULL - Noting');
            $table->integer('merge_primary')->nullable()->comment('Merge Ticket ID');
            $table->integer('merged_by')->nullable()->comment('Who merged the tickets');
            $table->timestamp('merged_at')->nullable()->comment('When merge happend');
            $table->string('merged_tkt_creators')->nullable()->comment('Merged Different Creators Ids');
            $table->text('cc_emails')->nullable();
            $table->tinyInteger('is_overdued')->nullable()->comment('To identify overdued atleast once');
            $table->integer('alias_acc_id')->nullable()->comment('tkt_alias_accounts id');
            $table->text('tags')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->bigInteger('ticket_type')->default(0);
            $table->text('ticket_type_custom_fields')->nullable();
            $table->text('custom_fields')->nullable();
            $table->bigInteger('new_ticket_reference')->nullable();
            $table->bigInteger('original_ticket_reference')->nullable();
            $table->integer('is_renewal')->nullable();
            $table->bigInteger('convert_to_kd_by')->nullable();
            $table->timestamp('response_sla')->nullable();
            $table->timestamp('workaround_sla')->nullable();
            $table->bigInteger('workaround_sla_hrs')->nullable();
            $table->bigInteger('response_sla_hrs')->nullable();
            $table->bigInteger('workaround_sla_remaining_mins')->nullable()->default(0);
            $table->bigInteger('response_sla_remaining_mins')->nullable()->default(0);
            $table->unsignedBigInteger('project_id')->nullable();
            $table->timestamp('revoke_access_at')->nullable();
            $table->string('seat_no')->nullable();
            $table->bigInteger('old_ticket_ref')->nullable();
            $table->bigInteger('schedule_maintenance_id')->nullable();
            $table->text('_itm_tset_field1')->nullable();
            $table->text('_itm_test_field2')->nullable();
            $table->text('_itm_logtest')->nullable();
            $table->text('_itm_test')->nullable();
            $table->text('_itm_ticket_severity')->nullable();
            $table->text('_itm_entry')->nullable();
            $table->text('_itm_viptickets')->nullable();
            $table->text('_itm_level')->nullable();
            $table->text('_itm_matrix')->nullable();
            $table->text('_itm_dropdown')->nullable();
            $table->text('_itm_drpdowntrial')->nullable();
            $table->text('_itm_dd_mj1')->nullable();
            $table->text('_itm_dd_mj2')->nullable();
            $table->text('_itm_testing_tag')->nullable();
            $table->string('_itm_testing_numeric')->nullable();
            $table->text('_itm_new_field')->nullable();
            $table->text('_itm_aadhar_no')->nullable();
            $table->text('_itm_shabby_name')->nullable();
            $table->string('_itm_shabby_dob')->nullable();
            $table->string('_itm_shbby_gender')->nullable();
            $table->string('_itm_testavdasvhfvasf')->nullable();

            $table->index(['status_id', 'priority_id', 'department_id', 'problem_category_id', 'sub_category_id', 'created_via', 'creator_id', 'updated_by', 'deleted_by', 'created_by', 'location_id', 'assigned_to', 'reopened_by', 'ac_email_id', 'device_id', 'rating'], 'idx_tkt_tickets');
            $table->index(['visible_group_id', 'is_temp', 'tat', 'feedback', 'spam', 'tat_remaining_mins', 'is_merge_primary', 'merge_primary', 'merged_by', 'is_overdued', 'alias_acc_id', 'starred', 'created_at', 'updated_at', 'deleted_at', 'tat_expire'], 'idx_tkt_tickets1');
            $table->index(['resolved_at', 'closed_at', 'reopened_at', 'ac_email_uid', 'ac_email_message_id', 'ticket_type'], 'idx_tkt_tickets2');
            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_tickets');
    }
};
