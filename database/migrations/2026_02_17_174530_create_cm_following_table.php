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
        Schema::create('cm_following', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('record_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->longText('remarks')->nullable();
            $table->tinyInteger('is_note')->nullable();
            $table->string('comment_change_id', 191)->nullable();
            $table->integer('who_comments')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_following');
    }
};
