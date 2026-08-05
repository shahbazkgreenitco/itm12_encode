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
        Schema::create('knowledge_document', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 191)->nullable()->fulltext('idx_documents_title');
            $table->tinyInteger('status')->default(1)->comment('1 = Published, 2 = Draft');
            $table->text('tags')->nullable();
            $table->bigInteger('parent_category_id')->default(1)->comment('1 = Ticketing System, 2 = Procurement');
            $table->bigInteger('sub_category_id')->nullable();
            $table->bigInteger('department_id')->nullable();
            $table->longText('content')->nullable();
            $table->integer('ticket_id')->default(0);
            $table->text('card_img')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->text('attached_comments')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->string('starred', 191)->nullable();
            $table->unsignedInteger('visited_count')->default(0);

            $table->fullText(['title', 'content'], 'fulltext_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_document');
    }
};
