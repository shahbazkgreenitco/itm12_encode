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
        Schema::create('tkt_ticket_pabs_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pab_id')->nullable();
            $table->string('name', 191)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('hierarchy_approval')->nullable();
            $table->tinyInteger('required_minimum_approvals')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('tkt_ticket_pabs_history');
    }
};
