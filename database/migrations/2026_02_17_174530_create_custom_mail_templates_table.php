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
        Schema::create('custom_mail_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('problem_category_id')->nullable();
            $table->integer('sub_category_id')->nullable();
            $table->integer('type')->nullable();
            $table->integer('cc')->nullable();
            $table->string('subject', 191)->nullable();
            $table->longText('content')->nullable();
            $table->string('template_for', 191)->nullable();
            $table->integer('status')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_mail_templates');
    }
};
