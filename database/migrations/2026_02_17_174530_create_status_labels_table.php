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
        Schema::create('status_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('deployable')->default(false);
            $table->boolean('pending')->default(false);
            $table->boolean('archived')->default(false);
            $table->text('notes')->nullable();
            $table->tinyInteger('deployed')->nullable();
            $table->tinyInteger('sold')->nullable()->comment('To identify the sold out status');
            $table->tinyInteger('is_edit_disabled')->nullable()->comment('To disable edit functionality on specific status labels');
            $table->tinyInteger('stolen_item')->nullable()->comment('To identify stolen status from list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_labels');
    }
};
