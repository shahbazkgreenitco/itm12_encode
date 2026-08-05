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
        Schema::create('holidays', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('date_name')->nullable();
            $table->string('location_id')->nullable()->comment('Null - Common Holiday, Not Null - Location Based');
            $table->string('location_country_id', 191)->nullable();
            $table->string('name', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('creator_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
