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
        Schema::create('procure_pabs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('hierarchy_approval')->nullable()->comment('1 - Level By Level, 2 - Minimum Approval, 3 - Group Approval');
            $table->timestamps();
            $table->tinyInteger('required_minimum_approvals')->nullable()->default(1)->comment('if hierarchy_approval = 2, then this value will active');
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
        Schema::dropIfExists('procure_pabs');
    }
};
