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
        Schema::create('tkt_procurement_following', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('procurement_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->longText('remarks')->nullable();
            $table->tinyInteger('is_note')->nullable();
            $table->timestamps();
            $table->string('comment_supplier_id', 191)->nullable();
            $table->integer('who_comments')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_procurement_following');
    }
};
