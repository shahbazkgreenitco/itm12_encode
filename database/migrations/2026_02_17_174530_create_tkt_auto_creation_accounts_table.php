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
        Schema::create('tkt_auto_creation_accounts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->tinyInteger('auto_create_from_email')->default(0)->comment('0 - No, 1 - Yes (Create Tickets by observe email account)');
            $table->string('ebts_host');
            $table->string('ebts_port', 20);
            $table->string('ebts_encryption', 20);
            $table->tinyInteger('validate_cert')->default(1)->comment('1 - True, 2 - False');
            $table->string('ebts_username');
            $table->string('ebts_password');
            $table->integer('default_department_id')->nullable()->comment('Default department for auto ticket from email');
            $table->integer('default_prob_cat_id')->nullable()->comment('default problem category for auto ticket from email');
            $table->integer('default_sub_cat_id')->nullable()->comment('default sub category for auto ticket from email');
            $table->tinyInteger('isReqDeptChgBfrResolve')->default(0)->comment('Is require department change before resolve a ticket');
            $table->tinyInteger('except_notify_status')->default(0)->comment('0 - Disable, 1 - Enable');
            $table->string('except_notify_tos')->nullable();
            $table->string('except_notify_ccs')->nullable();
            $table->tinyInteger('ticketing_restricted')->default(1)->comment('1 - Allow Specific Domains, 2 - Allow All Domains');
            $table->string('ticketing_allowed_domains')->nullable()->comment('allowed domains for ticketing');
            $table->string('ticketing_allowed_emails')->nullable()->comment('Allowed emails for ticketing');
            $table->string('ticketing_blocked_domains')->nullable()->comment('blocked domains for ticketing');
            $table->text('ticketing_blocked_accounts')->nullable()->comment('blocked emails for ticketing');
            $table->string('restricted_words')->nullable();
            $table->integer('user_id')->comment('Who last updated it');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->bigInteger('outgoing_mail_id')->nullable();
            $table->boolean('isReqDeptChgBfrAssigned');
            $table->boolean('isReqDeptChgBfrTransfer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_auto_creation_accounts');
    }
};
