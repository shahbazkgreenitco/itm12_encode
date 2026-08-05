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
        Schema::create('itm_common_credentials', function (Blueprint $table) {
            $table->integer('id', true);
            $table->enum('platform', ['Windows', 'Linux/Unix', 'iOS'])->nullable();
            $table->string('username')->nullable();
            $table->string('password', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_common_credentials');
    }
};
