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
        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workflow_id');
            $table->bigInteger('elementable_id')->nullable()->index();
            $table->string('elementable_type', 191)->nullable()->index();
            $table->bigInteger('triggerable_id')->nullable()->index();
            $table->string('triggerable_type', 191)->nullable()->index();
            $table->string('name', 191);
            $table->string('status', 191);
            $table->text('message')->nullable();
            $table->text('databus')->nullable();
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_logs');
    }
};
