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
        Schema::create('cm_cab_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cab_id');
            $table->string('action', 191);
            $table->longText('data');
            $table->bigInteger('performed_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_cab_histories');
    }
};
