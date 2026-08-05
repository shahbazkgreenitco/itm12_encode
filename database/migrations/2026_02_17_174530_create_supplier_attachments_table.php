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
        Schema::create('supplier_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('suplier_id');
            $table->string('original_file_name', 500)->nullable();
            $table->string('file_name', 30);
            $table->string('thumbnail_file_name')->nullable();
            $table->string('extension', 15);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_attachments');
    }
};
