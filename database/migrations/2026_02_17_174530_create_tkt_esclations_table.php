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
        Schema::create('tkt_esclations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('problem_category_id')->nullable()->comment('Id from tkt_problem_categories table');
            $table->unsignedInteger('esclate_to')->nullable()->comment('Id from user table');
            $table->unsignedInteger('esclate_stage_no')->nullable();
            $table->unsignedInteger('esclate_need_at')->nullable()->default(1)->comment('specific hrs that trigger esclation from ticket create time');
            $table->timestamps();
            $table->tinyInteger('sla_count')->nullable()->default(0);
            $table->bigInteger('escalation_group_id')->default(0);
            $table->tinyInteger('technician_mark_cc')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_esclations');
    }
};
