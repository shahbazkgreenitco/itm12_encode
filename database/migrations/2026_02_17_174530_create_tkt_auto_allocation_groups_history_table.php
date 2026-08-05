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
        Schema::create('tkt_auto_allocation_groups_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('auto_allocation_id')->nullable();
            $table->text('name')->nullable();
            $table->text('desc')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->tinyInteger('enabled')->nullable()->default(1);
            $table->string('remark', 191)->nullable();
            $table->integer('location_approval_required')->nullable();
            $table->integer('location_approval_required_for')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_auto_allocation_groups_history');
    }
};
