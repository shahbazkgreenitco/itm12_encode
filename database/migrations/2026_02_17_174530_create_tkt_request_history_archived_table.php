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
        Schema::create('tkt_request_history_archived', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pr_id');
            $table->integer('user_id')->nullable();
            $table->text('change_info')->nullable();
            $table->longText('comment')->nullable();
            $table->bigInteger('action_type')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_request_history_archived');
    }
};
