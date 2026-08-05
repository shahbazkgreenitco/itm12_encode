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
        Schema::create('triggers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 191);
            $table->string('name', 191);
            $table->boolean('queueable')->default(true);
            $table->longText('data_fields')->nullable();
            $table->longText('conditions')->nullable();
            $table->bigInteger('workflow_id');
            $table->integer('pos_x');
            $table->integer('pos_y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triggers');
    }
};
