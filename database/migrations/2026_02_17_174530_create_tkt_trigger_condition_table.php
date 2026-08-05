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
        Schema::create('tkt_trigger_condition', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trigger_id')->index();
            $table->integer('condition_id')->index();
            $table->integer('operator_id')->index();
            $table->text('condition_value');
            $table->string('match_type', 3)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_trigger_condition');
    }
};
