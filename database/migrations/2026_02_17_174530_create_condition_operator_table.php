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
        Schema::create('condition_operator', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('condition_id');
            $table->integer('operator_id');

            $table->unique(['condition_id', 'operator_id'], 'tkt_condition_operator_condition_id_operator_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condition_operator');
    }
};
