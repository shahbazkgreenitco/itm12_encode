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
        Schema::create('procure_budgets', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('at_id')->nullable()->comment('Account Type Id');
            $table->integer('fy_id')->nullable()->comment('Financial Year Id');
            $table->integer('department_id')->nullable()->comment('Department Id');
            $table->decimal('amount', 30)->nullable();
            $table->timestamps();
            $table->string('category')->nullable()->comment('Name of the budget');
            $table->string('account_code', 20)->nullable()->comment('Number to sync other systems');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_budgets');
    }
};
