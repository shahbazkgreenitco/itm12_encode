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
        Schema::create('cm_categories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->bigInteger('department_id')->nullable();
            $table->bigInteger('cab_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_categories');
    }
};
