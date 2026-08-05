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
        Schema::create('license_seats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('license_id');
            $table->integer('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('asset_id')->nullable();
            $table->string('serial')->nullable();
            $table->date('expected_checkin')->nullable();
            $table->decimal('cost', 9)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_seats');
    }
};
