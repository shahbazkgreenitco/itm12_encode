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
        Schema::create('tkt_procure_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('procurement_id')->nullable();
            $table->integer('following_id')->nullable();
            $table->string('attachment_link')->nullable();
            $table->string('original_file_name', 100)->nullable();
            $table->string('file_name', 100)->nullable();
            $table->string('extension', 15)->nullable();
            $table->string('uploader_id', 20)->nullable();
            $table->string('tmp_id', 15)->nullable();
            $table->string('thumbnail', 100)->nullable();
            $table->string('cid', 15)->nullable();
            $table->timestamps();
            $table->integer('attachment_via')->nullable()->comment('1:attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_procure_attachments');
    }
};
