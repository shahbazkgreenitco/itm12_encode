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
        Schema::create('tkt_problem_categories_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('category_id')->nullable();
            $table->text('name')->nullable();
            $table->string('category_tag', 191)->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->longText('remarks')->nullable();
            $table->integer('tat')->nullable();
            $table->integer('ticket_attender')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('pab_id')->nullable();
            $table->integer('approval_required')->nullable();
            $table->tinyInteger('required_minimum_approvals')->nullable();
            $table->string('hierarchy_approval')->nullable();
            $table->text('auto_allocation_group')->nullable();
            $table->bigInteger('global_prob_cat_id')->nullable();
            $table->bigInteger('global_sub_cat_id')->nullable();
            $table->integer('form_id')->nullable();
            $table->tinyInteger('is_form_required')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('custom_fieldset', 191)->nullable();
            $table->string('response_sla', 191)->nullable();
            $table->string('workaround_sla', 191)->nullable();
            $table->string('ticket_status_form', 191)->nullable();
            $table->integer('number_of_days')->nullable();
            $table->integer('privilege_access')->nullable();
            $table->boolean('status')->default(true)->comment('1 => Enable , 0 => Disable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_problem_categories_history');
    }
};
