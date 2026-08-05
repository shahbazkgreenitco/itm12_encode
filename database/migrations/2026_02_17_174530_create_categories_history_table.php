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
        Schema::create('categories_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name');
            $table->integer('user_id');
            $table->longText('eula_text')->nullable();
            $table->tinyInteger('use_default_eula')->default(0);
            $table->tinyInteger('require_acceptance')->default(0);
            $table->string('category_type')->default('asset');
            $table->tinyInteger('checkin_email')->default(0);
            $table->integer('account_type_id')->nullable()->comment('For procurement account type id	');
            $table->integer('service_cycle_period')->nullable();
            $table->integer('fieldset_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->text('image')->nullable();
            $table->text('image_thumbnail')->nullable();
            $table->tinyInteger('checkout_email_accept')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_history');
    }
};
