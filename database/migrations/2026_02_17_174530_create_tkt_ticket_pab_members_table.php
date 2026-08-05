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
        Schema::create('tkt_ticket_pab_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pab_id');
            $table->integer('user_id');
            $table->integer('hierarchy_level')->nullable();
            $table->timestamps();
            $table->bigInteger('location_id')->nullable();
            $table->bigInteger('department_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_ticket_pab_members');
    }
};
