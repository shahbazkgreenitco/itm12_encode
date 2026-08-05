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
        Schema::create('tax_elements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tax_element', 200)->nullable();
            $table->integer('tax_defination_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->bigInteger('position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_elements');
    }
};
