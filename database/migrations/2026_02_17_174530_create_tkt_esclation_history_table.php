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
        Schema::create('tkt_esclation_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('esclation_id');
            $table->unsignedInteger('problem_category_id')->nullable();
            $table->unsignedInteger('esclate_to')->nullable();
            $table->unsignedInteger('esclate_stage_no')->nullable();
            $table->unsignedInteger('esclate_need_at')->nullable()->default(1);
            $table->boolean('sla_count')->nullable()->default(false);
            $table->unsignedBigInteger('escalation_group_id')->default(0);
            $table->boolean('technician_mark_cc')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_esclation_history');
    }
};
