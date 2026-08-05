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
        Schema::create('status_request_approvar', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('approvar_id');
            $table->unsignedBigInteger('requested_id');
            $table->tinyInteger('status')->default(1); // 1=pending,2=approved,3=rejected
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_request_approvar');
    }
};
