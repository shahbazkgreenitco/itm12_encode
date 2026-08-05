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
        Schema::create('purchase_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reference_id')->nullable();
            $table->string('attachment_link')->nullable();
            $table->string('original_file_name', 100)->nullable();
            $table->string('file_name', 100)->nullable();
            $table->string('extension', 15)->nullable();
            $table->timestamps();
            $table->integer('uploader_id')->nullable();
            $table->string('tmp_id', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_attachments');
    }
};
