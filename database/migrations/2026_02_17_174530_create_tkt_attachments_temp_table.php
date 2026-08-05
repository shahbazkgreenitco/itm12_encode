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
        Schema::create('tkt_attachments_temp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id')->nullable();
            $table->integer('following_id')->nullable()->default(0);
            $table->string('attachment_link');
            $table->string('original_file_name', 500)->nullable();
            $table->string('file_name', 30);
            $table->string('extension', 15);
            $table->unsignedInteger('creator_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_attachments_temp');
    }
};
