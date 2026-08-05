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
        Schema::table('task_history', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('problem_category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->timestamp('archived_at')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_history', function (Blueprint $table) {
            $table->dropColumn([
                'company_id',
                'department_id',
                'problem_category_id',
                'sub_category_id',
                'archived_at'
            ]);
        });
    }
};
