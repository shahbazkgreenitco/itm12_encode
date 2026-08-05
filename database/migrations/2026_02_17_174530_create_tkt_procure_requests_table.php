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
        Schema::create('tkt_procure_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('procure_tag', 30)->nullable();
            $table->tinyInteger('status_id')->default(2)->comment('1 - Created, 2 - Waiting for Approval, 3 - Approved');
            $table->unsignedBigInteger('ticket_id')->index('tkt_procure_requests_ticket_id_foreign');
            $table->string('subject', 191);
            $table->longText('content');
            $table->integer('department_id');
            $table->integer('problem_category_id');
            $table->integer('sub_category_id')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->integer('priority_id');
            $table->integer('device_id')->nullable();
            $table->integer('tat');
            $table->integer('ac_email_id')->nullable();
            $table->integer('creator_id');
            $table->integer('updator_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->string('pab_id', 500)->nullable();
            $table->integer('approved_day')->nullable();
            $table->text('cc_emails')->nullable();
            $table->bigInteger('old_ticket_ref')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('form_type')->nullable();
            $table->bigInteger('new_ticket_reference')->nullable();
            $table->bigInteger('original_ticket_reference')->nullable();

            $table->fullText(['subject', 'content', 'procure_tag'], 'idx_procure_requests');
            $table->index(['status_id', 'ticket_id', 'department_id', 'problem_category_id', 'sub_category_id', 'assigned_to', 'priority_id', 'device_id', 'tat', 'ac_email_id', 'creator_id', 'updator_id', 'location_id', 'procure_tag', 'pab_id'], 'idx_tkt_procure_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_procure_requests');
    }
};
