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
        Schema::create('purchase_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('po_id');
            $table->string('doc_link', 100)->nullable();
            $table->string('original_file_name', 500)->nullable();
            $table->string('file_name', 30);
            $table->string('extension', 15);
            $table->boolean('doc_type')->nullable()->default(false)->comment('1 - PDF, 2 - IMAGE, 3 - FILE');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_docs');
    }
};
