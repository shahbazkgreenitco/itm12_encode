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
        Schema::create('procure_budget_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('budget_id')->nullable();
            $table->bigInteger('at_id')->nullable();
            $table->bigInteger('fy_id')->nullable()->comment('Financial Year Id');
            $table->bigInteger('department_id')->nullable()->comment('Department Id');
            $table->decimal('amount', 30)->nullable();
            $table->string('category')->nullable()->comment('Name of the budget');
            $table->string('account_code', 20)->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_budget_history');
    }
};
