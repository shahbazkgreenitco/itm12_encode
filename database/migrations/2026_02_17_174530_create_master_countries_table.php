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
        Schema::create('master_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->char('country_code', 3)->unique('country_code');
            $table->string('name', 50)->nullable();
            $table->float('lat', 11)->nullable()->default(0);
            $table->float('lng', 11)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_countries');
    }
};
