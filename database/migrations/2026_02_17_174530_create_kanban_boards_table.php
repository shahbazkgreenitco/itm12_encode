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
        Schema::create('kanban_boards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->nullable();
            $table->longText('Description')->nullable();
            $table->string('department_id', 191)->nullable()->index('idx_department_id');
            $table->string('problem_category_id', 191)->nullable()->index('idx_problem_category_id');
            $table->string('sub_category_id', 191)->nullable()->index('idx_sub_category_id');
            $table->integer('board_items_type')->nullable();
            $table->boolean('auto_comment_on_card_change')->nullable();
            $table->boolean('access_to_all_department_technician')->nullable();
            $table->bigInteger('created_by')->index('idx_created_by');
            $table->timestamp('created_at')->nullable()->index('idx_created_at');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->string('custom_fieldset_id', 500)->nullable();
            $table->integer('archive_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanban_boards');
    }
};
