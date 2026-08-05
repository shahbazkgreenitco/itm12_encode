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
        Schema::create('tkt_followings', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('auto_response')->default(0);
            $table->unsignedBigInteger('ticket_id')->index();
            $table->integer('updated_status')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->index();
            $table->longText('remarks')->nullable()->fulltext('idx_followings_remarks');
            $table->tinyInteger('is_note')->nullable()->index();
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();
            $table->integer('assigned_to')->nullable()->index();
            $table->tinyInteger('action_type')->nullable()->index();
            $table->integer('merge_primary')->nullable()->comment('Merge Ticket ID');
            $table->string('cc_emails', 1000)->nullable()->comment('comma separated cc emails');
            $table->integer('creator_id')->nullable()->index();
            $table->integer('ac_email_id')->nullable();
            $table->string('ac_email_uid')->nullable();
            $table->string('ac_email_message_id')->nullable();
            $table->integer('alias_acc_id')->nullable()->comment('tkt_alias_accounts id');
            $table->boolean('is_service_request')->default(false);
            $table->unsignedBigInteger('ticket_status_form_id')->nullable();
            $table->boolean('is_workaround')->nullable();
            $table->boolean('is_valid_workaround')->nullable();
            $table->timestamp('revoke_access_at')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->tinyInteger('updated_via')->nullable()->comment('1-Portal, 2-Chat, 3-Email, 4-Mobile, 5-Call');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_followings');
    }
};
