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
        Schema::create('custom_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('field_id')->nullable();
            $table->integer('action')->nullable();
            $table->integer('scheduler_id')->nullable();
            $table->text('condition')->nullable();
            $table->integer('no_of_day')->nullable();
            $table->longText('mail_trigger_content')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_actions');
    }
};
