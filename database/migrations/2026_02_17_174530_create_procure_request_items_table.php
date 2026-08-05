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
        Schema::create('procure_request_items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('pr_id')->nullable();
            $table->integer('bc_id')->nullable()->comment('Budget Category ID');
            $table->decimal('qty', 11)->nullable();
            $table->integer('unit_id')->nullable();
            $table->string('item_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->text('delivery_instruction')->nullable();
            $table->boolean('different_location')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_request_items');
    }
};
