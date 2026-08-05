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
        Schema::create('bulk_actions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('action_type')->nullable()->default(0)->comment('1 - Checkout, 2 - Checkin, 3 - Delete, 4 - Import, 5 - Update, 6 - Dispose');
            $table->string('doc_name')->nullable()->comment('Original Name');
            $table->string('doc_path')->nullable()->comment('Our unique name');
            $table->integer('tot_success')->nullable();
            $table->integer('tot_failure')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->smallInteger('module_id')->nullable()->comment('1 - Devices, 2 - License, 3 - Accessory, 4 - Consumable, 5 - Component, 6 - Users');
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_actions');
    }
};
