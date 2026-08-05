<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_signals', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('call_id');
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id');

            $table->enum('type', [
                'offer',
                'answer',
                'ice'
            ]);

            $table->longText('data');

            $table->timestamps();

           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_signals');
    }
};