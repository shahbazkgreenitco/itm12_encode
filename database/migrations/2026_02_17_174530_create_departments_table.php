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
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name', 80);
            $table->string('department_tag', 191)->nullable();
            $table->integer('attender_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->tinyInteger('module_ticket_enabled')->nullable()->comment('1- Enabled for Ticket, Null-disabled for Ticket');
            $table->integer('tkt_auto_creation_id')->nullable();
            $table->bigInteger('global_department_id')->nullable();
            $table->text('description')->nullable();
            $table->text('department_admin')->nullable();
            $table->timestamp('created_at')->useCurrentOnUpdate()->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->boolean('asset_department')->default(false);
            $table->boolean('is_default_asset_department')->nullable();
            $table->text('asset_department_admin')->nullable();
            $table->text('department_custom_fieldset')->nullable();
            $table->bigInteger('department_head_id')->nullable();
            $table->bigInteger('pc_id')->nullable();
            $table->bigInteger('sc_id')->nullable();
            $table->string('access_role_id', 191)->nullable();

            $table->index(['name', 'attender_id', 'company_id', 'module_ticket_enabled', 'tkt_auto_creation_id', 'global_department_id'], 'idx_departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
