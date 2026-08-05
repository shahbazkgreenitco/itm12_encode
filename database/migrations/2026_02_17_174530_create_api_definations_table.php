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
        Schema::create('api_definations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191)->comment('Name of the API');
            $table->text('description')->nullable()->comment('Description of the API');
            $table->string('url', 191)->comment('URL for the API');
            $table->unsignedTinyInteger('reference')->comment('Reference: 1 - Custom, 2 - Ticket, 3 - Request');
            $table->unsignedTinyInteger('method')->comment('Method: 1 - GET, 2 - POST, 3 - PUT, 4 - PATCH, 5 - DELETE');
            $table->text('body')->nullable()->comment('api Request body');
            $table->text('headers')->nullable()->comment('API headers');
            $table->boolean('include_response_as_comment')->default(false);
            $table->boolean('status')->default(true)->comment('Status: Active or Inactive');
            $table->softDeletes();
            $table->timestamps();
            $table->string('body_type', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_definations');
    }
};
