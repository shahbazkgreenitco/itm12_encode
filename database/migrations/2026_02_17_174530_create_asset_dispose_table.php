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
        Schema::create('asset_dispose', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_id');
            $table->string('asset_tag', 191)->nullable();
            $table->string('serial', 191)->nullable();
            $table->string('model_id', 191)->nullable();
            $table->integer('dispose_type')->comment('1 - Sold, 2 - Donated, 3 - Recycled, 4 - Disposed, 5 - Lost');
            $table->dateTime('dispose_at')->nullable();
            $table->unsignedBigInteger('dispose_by')->nullable();
            $table->integer('is_dispose')->default(0)->comment('1-permanent delete, 2- old sold');
            $table->string('currency_format', 191)->nullable()->default('null');
            $table->decimal('dispose_price', 15)->nullable();
            $table->string('vendor_org_name', 191)->nullable();
            $table->string('ref_no', 191)->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_dispose');
    }
};
