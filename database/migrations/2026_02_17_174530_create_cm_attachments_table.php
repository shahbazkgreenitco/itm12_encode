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
        Schema::create('cm_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('record_id')->nullable();
            $table->integer('following_id')->nullable();
            $table->string('attachment_link', 500)->nullable();
            $table->string('original_file_name')->nullable();
            $table->text('description')->nullable();
            $table->string('file_name', 250)->nullable();
            $table->string('file_hash', 32)->nullable();
            $table->string('extension', 15)->nullable();
            $table->timestamps();
            $table->integer('uploader_id')->nullable();
            $table->string('tmp_id', 25)->nullable();
            $table->string('thumbnail', 250)->nullable();
            $table->string('cid')->nullable()->comment('CID for embedded image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_attachments');
    }
};
