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
        Schema::create('pr_request_challans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pr_id')->comment('procurement id');
            $table->string('challan_no', 191)->comment('challan number');
            $table->unsignedBigInteger('updated_by')->comment('updated_by from users table');
            $table->longText('file')->nullable();
            $table->timestamp('received_date')->useCurrent();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_request_challans');
    }
};
