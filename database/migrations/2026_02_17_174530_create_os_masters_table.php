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
        Schema::create('os_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('OsCaption', 191)->nullable()->unique();
            $table->string('OsVersion', 191)->nullable();
            $table->string('OSManufacturer', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('os_masters');
    }
};
