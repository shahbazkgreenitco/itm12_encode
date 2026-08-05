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
        Schema::create('tkt_operators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191)->index('tky_operators_name_index');
            $table->string('display_name', 191);
            $table->string('type', 191)->default('primitive');
            $table->string('value_type', 191)->default('text');
            $table->string('value_placeholder', 191)->nullable();
            $table->string('validation_rules', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_operators');
    }
};
