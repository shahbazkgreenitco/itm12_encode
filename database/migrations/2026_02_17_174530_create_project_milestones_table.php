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
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191);
            $table->string('currency', 191)->nullable();
            $table->decimal('cost', 15)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('handler_id', 191)->comment('Only users who are members of this milestone project will come here');
            $table->unsignedBigInteger('status_id')->nullable()->comment('1. In Progress ,2.Completed,3.Hold, 4.In Complete');
            $table->unsignedBigInteger('project_id')->comment('project id of this milestone');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
