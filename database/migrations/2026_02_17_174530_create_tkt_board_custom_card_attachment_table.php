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
        Schema::create('tkt_board_custom_card_attachment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tkt_board_item_id')->nullable();
            $table->integer('following_id')->nullable();
            $table->string('attachment_link')->nullable();
            $table->string('original_file_name', 100)->nullable();
            $table->string('file_name', 100)->nullable();
            $table->string('extension', 15)->nullable();
            $table->integer('uploader_id')->nullable();
            $table->string('tmp_id', 25)->nullable();
            $table->string('thumbnail', 100)->nullable();
            $table->string('cid')->nullable()->comment('CID for embedded image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_board_custom_card_attachment');
    }
};
