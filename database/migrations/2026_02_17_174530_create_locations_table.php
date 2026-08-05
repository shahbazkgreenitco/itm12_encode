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
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id')->index('id_locations_idx');
            $table->string('name')->index('name_locations_idx');
            $table->string('city', 191)->nullable()->index('city_locations_idx');
            $table->string('state', 191)->nullable()->index('state_locations_idx');
            $table->string('country', 191)->nullable()->index('country_locations_idx');
            $table->bigInteger('country_id');
            $table->bigInteger('state_id');
            $table->bigInteger('city_id');
            $table->timestamps();
            $table->integer('user_id')->index('user_id_locations_idx');
            $table->string('address', 80)->nullable();
            $table->string('address2')->nullable();
            $table->string('zip', 10)->nullable()->index('zip_locations_idx');
            $table->softDeletes();
            $table->integer('parent_id')->nullable()->index('parent_id_locations_idx');
            $table->string('currency', 10)->nullable();
            $table->string('branch_code')->nullable();
            $table->string('zone')->nullable();
            $table->string('location_type')->nullable()->index('location_type_locations_idx');
            $table->integer('deleted_by')->nullable()->index('deleted_by_locations_idx');
            $table->string('location_user_id', 191)->nullable()->index('location_user_id_locations_idx');

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
