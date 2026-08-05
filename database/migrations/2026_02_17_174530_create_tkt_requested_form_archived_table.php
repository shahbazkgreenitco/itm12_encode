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
        Schema::create('tkt_requested_form_archived', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('form_id');
            $table->longText('field_values')->nullable();
            $table->integer('request_id');
            $table->string('tmp_id', 191)->nullable();
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_requested_form_archived');
    }
};
