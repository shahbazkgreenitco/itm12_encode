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
        Schema::create('batches', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Primary key: Unique batch record ID');
            $table->string('batch_id', 191)->default('')->unique()->comment('System-generated unique batch identifier (e.g., ITMXXXXtimestamp)');
            $table->bigInteger('no_items')->default(0)->comment('Total number of items included in this batch');
            $table->timestamp('date')->useCurrent()->comment('Date and time when the batch was created');
            $table->string('courier_provider', 191)->nullable()->comment('Name of the courier or delivery service provider handling this batch');
            $table->string('reference_number', 191)->nullable()->comment('Optional reference or tracking number for courier or shipment');
            $table->text('signature')->nullable()->comment('Digital or manual signature of the person authorizing the batch');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
