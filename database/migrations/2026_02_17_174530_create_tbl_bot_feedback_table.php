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
        Schema::create('tbl_bot_feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feedback')->comment('1 = Excellent, 2 = Very Good, 3 = Good, 4 = Unhappy, 5 = Need Improvement');
            $table->text('comment')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_bot_feedback');
    }
};
