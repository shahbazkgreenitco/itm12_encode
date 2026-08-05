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
        Schema::create('procure_history_entries', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('history_id');
            $table->text('change_info')->nullable();
            $table->timestamps();
            $table->longText('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_history_entries');
    }
};
