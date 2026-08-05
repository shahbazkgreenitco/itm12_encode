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
        Schema::create('asset_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('action_type');
            $table->unsignedInteger('asset_id')->nullable()->index('asset_logs_asset_id_foreign');
            $table->integer('checkedout_to')->nullable();
            $table->integer('location_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('asset_type', 100)->nullable();
            $table->text('note')->nullable();
            $table->text('filename')->nullable();
            $table->string('original_file_name')->nullable()->comment('for uploading docs\' original name');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->integer('accessory_id')->nullable();
            $table->integer('accepted_id')->nullable();
            $table->integer('consumable_id')->nullable();
            $table->date('expected_checkin')->nullable();
            $table->integer('thread_id')->nullable();
            $table->tinyInteger('assigned_to_type')->nullable()->comment('To note the checkout for device or user');
            $table->tinyInteger('assigned_for')->nullable()->comment('1 - User, 2 - Place');
            $table->integer('interact_id')->nullable()->comment('In which user interacted');
            $table->string('interact_type', 4)->nullable()->comment('define which type of interact');
            $table->string('interact_module', 4)->nullable()->comment('define which type of item');
            $table->text('interact_fields')->nullable();
            $table->integer('project_id')->nullable()->comment('ID from Project Table');
            $table->string('access_code', 80)->nullable()->comment('for direct accept link');
            $table->integer('audit_id')->nullable()->comment('User response about checkout on audit table');
            $table->integer('allocation_type_id')->nullable();
            $table->string('checkin_attachment', 191)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->integer('in_out_id')->nullable();

            $table->index(['user_id', 'action_type', 'asset_id', 'checkedout_to', 'location_id', 'asset_type', 'accessory_id', 'accepted_id', 'consumable_id', 'thread_id', 'assigned_to_type', 'assigned_for', 'interact_id', 'interact_type', 'interact_module', 'project_id', 'access_code', 'audit_id', 'allocation_type_id'], 'idx_asset_logs');
            $table->index(['asset_type', 'action_type', 'assigned_for', 'created_at', 'asset_id', 'allocation_type_id'], 'idx_asset_logs_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_logs');
    }
};
