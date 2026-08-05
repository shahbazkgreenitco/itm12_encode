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
        Schema::create('tkt_request_form_builder_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('form_id')->nullable();
            $table->string('form_name', 191)->nullable();
            $table->string('descriptions', 191)->nullable();
            $table->longText('fields')->nullable();
            $table->string('remark', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('tkt_request_form_builder_history');
    }
};
