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
        Schema::create('cm_cabs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('hierarchy_approval')->nullable();
            $table->integer('required_minimum_approvals')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_cabs');
    }
};
