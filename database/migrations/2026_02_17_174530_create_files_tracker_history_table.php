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
        Schema::create('files_tracker_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('file_tracker_id')->nullable();
            $table->string('file_number', 191)->nullable();
            $table->string('title', 191)->nullable();
            $table->text('tags')->nullable();
            $table->text('description')->nullable();
            $table->string('remark', 191)->nullable();
            $table->string('last_seen_at', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('checkout_to')->nullable();
            $table->bigInteger('checkout_user')->nullable();
            $table->bigInteger('checkout_place')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_tracker_history');
    }
};
