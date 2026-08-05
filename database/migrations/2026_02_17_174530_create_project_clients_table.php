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
        Schema::create('project_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_name', 191);
            $table->string('client_email', 191);
            $table->string('client_phone', 191)->nullable();
            $table->string('logo', 191)->nullable();
            $table->string('client_fax', 191)->nullable();
            $table->string('client_address', 191)->nullable();
            $table->unsignedInteger('state_id')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_clients');
    }
};
