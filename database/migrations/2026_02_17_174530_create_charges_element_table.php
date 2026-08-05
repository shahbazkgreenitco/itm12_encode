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
        Schema::create('charges_element', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 191);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->tinyInteger('action')->comment('1 - add, 2 - subtract');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges_element');
    }
};
