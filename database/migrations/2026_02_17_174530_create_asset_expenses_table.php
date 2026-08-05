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
        Schema::create('asset_expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_id');
            $table->string('title', 191);
            $table->string('expense_type', 191)->nullable();
            $table->boolean('is_amc')->default(false);
            $table->boolean('is_warranty')->default(false);
            $table->date('expense_date')->nullable();
            $table->decimal('cost', 13)->nullable();
            $table->string('currency_format', 191)->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('temp_id', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_expenses');
    }
};
