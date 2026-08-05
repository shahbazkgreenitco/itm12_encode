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
        Schema::create('tkt_config', function (Blueprint $table) {
            $table->integer('id', true);
            $table->tinyInteger('eu_hide_priority')->nullable();
            $table->tinyInteger('eu_hide_assigned_to')->nullable();
            $table->tinyInteger('eu_hide_expire_at')->nullable();
            $table->tinyInteger('eu_hide_tat')->nullable();
            $table->timestamps();
            $table->tinyInteger('mail_all_status_changes')->nullable()->comment('1 - Mail to creator for all status changes, null - default (resolved time only)');
            $table->text('report_fields')->nullable()->comment('To store the visible fields on excel sheet');
            $table->tinyInteger('cc_to_settings_mail')->nullable()->default(0)->comment('1 - Mail CC to Settings Config Mail Address, 0 - No');
            $table->tinyInteger('auto_create_from_email')->nullable()->default(0)->comment('0 - No, 1 - Yes (Create Tickets by observe email account(s))');
            $table->tinyInteger('ticketing_restricted')->nullable()->default(1)->comment('1 - Allow Specific Domains, 2 - Allow All Domains');
            $table->string('ticketing_allowed_domains')->nullable()->comment('allowed domains for ticketing');
            $table->string('ticketing_allowed_emails')->nullable()->comment('Allowed emails for ticketing');
            $table->string('ticketing_blocked_domains')->nullable()->comment('blocked domains for ticketing');
            $table->text('ticketing_blocked_accounts')->nullable();
            $table->string('restricted_words')->nullable()->comment('restricted words to block ticket creation');
            $table->string('ebts_host')->nullable();
            $table->string('ebts_port', 20)->nullable();
            $table->string('ebts_encryption', 20)->nullable();
            $table->tinyInteger('validate_cert')->nullable()->default(1)->comment('1 - true, 2 - false');
            $table->string('ebts_username')->nullable();
            $table->string('ebts_password')->nullable();
            $table->integer('default_department_id')->nullable()->comment('Default department for auto ticket from email');
            $table->boolean('isReqDeptChgBfrResolve')->nullable()->default(false)->comment('Is require department change before resolve a ticket');
            $table->integer('default_prob_cat_id')->nullable()->comment('default problem category for auto ticket from email');
            $table->string('except_notify_tos')->nullable();
            $table->string('except_notify_ccs')->nullable();
            $table->tinyInteger('except_notify_status')->nullable()->default(0)->comment('0 - Disable, 1 - Enable');
            $table->tinyInteger('tat_by_work_hour')->default(0)->comment('1 - Work Hour Based, 0 - 24Hrs');
            $table->time('default_work_start')->nullable();
            $table->time('default_work_end')->nullable();
            $table->string('default_work_days')->nullable();
            $table->integer('close_request_in_x_days')->nullable();
            $table->tinyInteger('department_config')->default(0)->comment('0 = no access department, 1 = access all department');
            $table->integer('auto_archive_ticket_after_days')->nullable();
            $table->integer('sla_reminder')->nullable()->default(0)->comment('0 => No, 1 => Yes');
            $table->integer('sla_reminder_hr')->nullable()->default(1);
            $table->integer('sla_notification_type')->nullable()->default(1)->comment('1=> normal,2=>Buzzer');
            $table->tinyInteger('kd_auto_suggestion')->default(0)->comment('1 => Enable, 0=> Disable');
            $table->boolean('checked_cc_checkbox')->nullable()->default(false)->comment('0=>unchecked , 1=>checked');
            $table->integer('auto_archive_user_activity_after_days')->nullable();
            $table->string('ticket_initial', 191)->nullable()->comment('Comma separated initials like com,dep,cat');
            $table->string('ticket_initial_separator', 5)->nullable()->comment('Separator character like -, _, /');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_config');
    }
};
