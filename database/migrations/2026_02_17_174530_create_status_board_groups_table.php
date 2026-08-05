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
        Schema::create('status_board_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 80)->nullable()->unique('title');
            $table->unsignedInteger('tot_items')->nullable()->default(0);
            $table->tinyInteger('is_enabled')->nullable()->default(1);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_board_groups');
    }
};
