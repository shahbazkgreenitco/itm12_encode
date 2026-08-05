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
        Schema::create('ms_office_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Caption', 191)->nullable()->unique();
            $table->string('Version', 191)->nullable();
            $table->string('Vendor', 191)->nullable();
            $table->string('Publisher', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_office_masters');
    }
};
