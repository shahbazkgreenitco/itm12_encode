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
        Schema::create('procure_users_privileges', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable();
            $table->tinyInteger('team_privilege')->nullable();
            $table->tinyInteger('user_privilege')->nullable();
            $table->timestamps();
            $table->string('role', 4)->nullable()->comment('pu - Procure User, pt - Procure Team, pa - Procure Authroity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_users_privileges');
    }
};
