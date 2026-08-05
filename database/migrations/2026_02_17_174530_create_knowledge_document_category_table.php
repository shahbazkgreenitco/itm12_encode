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
        Schema::create('knowledge_document_category', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content')->nullable();
            $table->string('category_name', 191)->nullable();
            $table->integer('parent_category_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('department_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_document_category');
    }
};
