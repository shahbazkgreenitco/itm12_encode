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
        Schema::create('ticket_references', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cron_expression', 191)->nullable();
            $table->bigInteger('ticket_id')->nullable();
            $table->bigInteger('scheduler_id')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(0)->comment('0 => revoke will be created , 1 => revoke was created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_references');
    }
};
