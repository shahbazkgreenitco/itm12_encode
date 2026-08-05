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
        Schema::create('tkt_ticket_followings_archived', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('auto_response')->default(0);
            $table->unsignedInteger('ticket_id');
            $table->integer('updated_status')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->longText('remarks')->nullable();
            $table->tinyInteger('is_note')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();
            $table->integer('assigned_to')->nullable();
            $table->tinyInteger('action_type')->nullable();
            $table->integer('merge_primary')->nullable()->comment('Merge Ticket ID');
            $table->string('cc_emails', 555)->nullable()->comment('comma separated cc emails');
            $table->integer('creator_id')->nullable();
            $table->integer('ac_email_id')->nullable();
            $table->string('ac_email_uid')->nullable();
            $table->string('ac_email_message_id')->nullable();
            $table->integer('alias_acc_id')->nullable()->comment('tkt_alias_accounts id');
            $table->tinyInteger('is_service_request')->default(0);
            $table->unsignedBigInteger('ticket_status_form_id')->nullable();
            $table->boolean('is_workaround')->nullable();
            $table->boolean('is_valid_workaround')->nullable();
            $table->timestamp('revoke_access_at')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_ticket_followings_archived');
    }
};
