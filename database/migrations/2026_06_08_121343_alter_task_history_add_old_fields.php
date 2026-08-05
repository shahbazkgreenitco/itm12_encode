<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_history', function (Blueprint $table) {

            // name should be nullable according to document
            $table->string('name')->nullable()->change();

            // Old Values
            $table->string('old_name')->nullable()->after('name');

            $table->tinyInteger('old_type_id')->nullable()->after('type_id');

            $table->integer('old_status_id')->nullable()->after('status_id');

            $table->integer('old_priority_id')->nullable()->after('priority_id');

            $table->dateTime('old_start_date')->nullable()->after('start_date');

            $table->dateTime('old_due_date')->nullable()->after('due_date');

            $table->dateTime('old_end_date')->nullable()->after('end_date');

            $table->decimal('old_cost', 11, 2)->nullable()->after('cost');

            $table->integer('old_assigned_to')->nullable()->after('assigned_to');

            $table->integer('old_project_id')->nullable()->after('project_id');

            $table->integer('old_change_id')->nullable()->after('change_id');

            $table->longText('old_description')->nullable()->after('description');

            $table->unsignedBigInteger('old_ticket_id')->nullable()->after('ticket_id');

            $table->boolean('old_is_visible_user')->nullable()->after('is_visible_user');

            $table->unsignedBigInteger('old_department_id')->nullable()->after('department_id');

            $table->unsignedBigInteger('old_problem_category_id')->nullable()->after('problem_category_id');

            $table->unsignedBigInteger('old_sub_category_id')->nullable()->after('sub_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('task_history', function (Blueprint $table) {

            $table->dropColumn([
                'old_name',
                'old_type_id',
                'old_status_id',
                'old_priority_id',
                'old_start_date',
                'old_due_date',
                'old_end_date',
                'old_cost',
                'old_assigned_to',
                'old_project_id',
                'old_change_id',
                'old_description',
                'old_ticket_id',
                'old_is_visible_user',
                'old_department_id',
                'old_problem_category_id',
                'old_sub_category_id',
            ]);

            $table->string('name')->nullable(false)->change();
        });
    }
};