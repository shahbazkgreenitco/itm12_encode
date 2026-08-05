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
        Schema::create('tkt_user_privileges', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->comment('User Id');
            $table->integer('department_id')->nullable()->comment('Department Id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_user_privileges');
    }
};
