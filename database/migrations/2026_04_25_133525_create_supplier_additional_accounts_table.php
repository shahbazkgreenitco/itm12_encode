<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('supplier_additional_accounts')) {
            Schema::create('supplier_additional_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('bank_acc_number', 160)->nullable();
                $table->string('bank_name', 160)->nullable();
                $table->string('bank_ifsc', 60)->nullable();
                $table->string('bank_branch', 60)->nullable();
                $table->string('bank_acc_name', 160)->nullable();
                $table->string('url')->nullable();
                $table->longText('notes')->nullable();
                $table->string('image')->nullable();
                $table->integer('supplier_id');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('supplier_additional_accounts')) {
            Schema::dropIfExists('supplier_additional_accounts');
        }
    }
};
