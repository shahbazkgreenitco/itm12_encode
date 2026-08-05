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
        Schema::create('files_tracker', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file_number', 191)->unique();
            $table->string('title', 191);
            $table->text('description');
            $table->string('last_seen_at', 191)->nullable();
            $table->text('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('checkout_to')->nullable();
            $table->bigInteger('checkout_user')->nullable();
            $table->bigInteger('checkout_place')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_tracker');
    }
};
