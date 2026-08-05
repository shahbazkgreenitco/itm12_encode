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
        Schema::create('tkt_board_item_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ticket_id')->nullable()->index('idx_ticket_id');
            $table->unsignedBigInteger('board_id')->index('idx_board_id');
            $table->unsignedBigInteger('board_item_id')->nullable()->index('idx_board_item_id');
            $table->timestamp('created_at')->nullable()->index('idx_created_at');
            $table->timestamp('updated_at')->nullable();
            $table->string('title', 500)->nullable();
            $table->longText('description')->nullable();
            $table->string('assign_to', 500)->nullable();
            $table->integer('task_type')->nullable()->index('idx_task_type');
            $table->integer('status')->nullable()->index('idx_status');
            $table->integer('priority')->nullable()->index('idx_priority');
            $table->dateTime('expected_date')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->integer('type_id')->nullable();
            $table->string('client_name', 191)->nullable();
            $table->softDeletes();
            $table->text('_itm_inpt1')->nullable();
            $table->text('_itm_date_field')->nullable();
            $table->text('_itm_inpt_nullable')->nullable();
            $table->text('_itm_date_nullable')->nullable();
            $table->text('_itm_reqired_dd_kanba_1_predefined_users')->nullable();
            $table->text('_itm_nullable_dd_kanban_custom')->nullable();
            $table->text('_itm_dd_test_kanban_nullable_custom')->nullable();
            $table->text('_itm_nullable_dd_kanban_predefined')->nullable();
            $table->text('_itm_radio_kanban')->nullable();
            $table->text('_itm_checkbox_kanban')->nullable();
            $table->text('_itm_kanban_testbox_test')->nullable();
            $table->string('_itm_kanban_date')->nullable();
            $table->string('_itm_kanban_time')->nullable();
            $table->text('_itm_kanban_predrop_user')->nullable();
            $table->text('_itm_kanban_cusdrop')->nullable();
            $table->string('_itm_textalpha')->nullable();
            $table->string('_itm_textnumber')->nullable();
            $table->string('_itm_testdatetime')->nullable();
            $table->text('_itm_customcheckbox')->nullable();
            $table->text('_itm_preticketdrop')->nullable();
            $table->text('_itm_predropdepartment')->nullable();
            $table->text('_itm_customradiobutton')->nullable();
            $table->text('_itm_std_list')->nullable();
            $table->text('_itm_custom_dropdown_custom_field')->nullable();
            $table->text('_itm_normal_dropdown_custom_field')->nullable();
            $table->boolean('archived')->default(false);
            $table->string('_itm_kanban_start_date')->nullable();
            $table->text('_itm_kanban_end_date')->nullable();
            $table->text('_itm_customer_name')->nullable();
            $table->text('_itm_customer_renewal_date')->nullable();
            $table->text('_itm_customer_spoc')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('problem_category_id')->nullable();
            $table->integer('sub_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_board_item_tickets');
    }
};
