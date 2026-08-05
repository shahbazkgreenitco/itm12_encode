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
        Schema::create('procure_charges_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pri_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action_msg', 191)->nullable();
            $table->text('change_info')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_charges_histories');
    }
};
