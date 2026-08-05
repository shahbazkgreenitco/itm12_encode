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
        Schema::create('tkt_problem_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('service_type_id');
            $table->unsignedInteger('priority_id')->default(0);
            $table->string('remarks', 750)->nullable();
            $table->timestamps();
            $table->integer('tat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_problem_types');
    }
};
