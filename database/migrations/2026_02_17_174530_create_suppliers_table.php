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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 32)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->softDeletes();
            $table->string('zip', 10)->nullable();
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->string('pan', 60)->nullable();
            $table->string('bank_acc_number', 160)->nullable();
            $table->string('bank_ifsc', 60)->nullable();
            $table->string('bank_branch', 60)->nullable();
            $table->string('bank_acc_name', 160)->nullable();
            $table->string('bank_name', 160)->nullable();
            $table->boolean('supplier_login')->default(false);
            $table->integer('user_supplier_id')->nullable();
            $table->text('business_categories')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->unsignedMediumInteger('state_id')->nullable()->index('state_id');
            $table->unsignedMediumInteger('city_id')->nullable()->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
