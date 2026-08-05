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
        Schema::create('tkt_incident_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('incident_id')->nullable();
            $table->integer('following_id')->nullable();
            $table->string('attachment_link', 191)->nullable();
            $table->string('original_file_name', 191)->nullable();
            $table->string('file_name', 191)->nullable();
            $table->string('extension', 191)->nullable();
            $table->integer('uploader_id')->nullable();
            $table->string('tmp_id', 191)->nullable();
            $table->string('thumbnail', 191)->nullable();
            $table->string('cid', 191)->nullable()->comment('CID for embedded image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_incident_attachments');
    }
};
