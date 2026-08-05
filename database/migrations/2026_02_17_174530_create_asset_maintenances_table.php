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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('asset_id')->index('fk_asset_id');
            $table->integer('supplier_id')->nullable();
            $table->string('asset_maintenance_type', 191)->nullable();
            $table->string('title', 100);
            $table->boolean('is_warranty');
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->integer('asset_maintenance_time')->nullable();
            $table->longText('notes')->nullable();
            $table->decimal('cost', 10)->nullable();
            $table->string('currency_format', 10)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
            $table->tinyInteger('is_amc')->nullable()->comment('know whether service done by amc policy');
            $table->integer('category_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->string('expense_type', 191)->nullable();
            $table->date('expense_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
