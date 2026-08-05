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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->integer('user_id');
            $table->softDeletes();
            $table->longText('eula_text')->nullable();
            $table->boolean('use_default_eula')->default(false);
            $table->boolean('require_acceptance')->default(false);
            $table->string('category_type')->nullable()->default('asset');
            $table->boolean('checkin_email')->default(false);
            $table->integer('account_type_id')->nullable()->comment('For procurement account type id');
            $table->integer('service_cycle_period')->nullable();
            $table->integer('fieldset_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->text('image')->nullable();
            $table->text('image_thumbnail')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->tinyInteger('checkout_email_accept')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
