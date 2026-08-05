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
        Schema::create('projects_management', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->timestamps();
            $table->string('project_no', 30)->nullable();
            $table->date('start_date')->nullable();
            $table->integer('project_head')->nullable();
            $table->text('description')->nullable();
            $table->date('end_date')->nullable();
            $table->bigInteger('budget')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('tags', 191)->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('currency', 191)->default('INR');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_management');
    }
};
