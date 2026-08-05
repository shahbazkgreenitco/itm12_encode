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
        Schema::create('gate_pass_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gate_pass_id');
            $table->tinyInteger('purpose_type')->nullable()->comment('1=Daily ,2=Permanant');
            $table->tinyInteger('purpose')->comment('1=Repair, 2=Scrap, 3=Transfer, 4=User Home Use, 5=Others');
            $table->unsignedBigInteger('location_id');
            $table->bigInteger('moved_to_location')->nullable();
            $table->unsignedBigInteger('place_id')->nullable();
            $table->tinyInteger('moved_with_type')->nullable()->comment('1=User ,2=Supplier,3=Movement');
            $table->unsignedBigInteger('moved_with_id')->nullable();
            $table->string('moved_with_name', 191)->nullable();
            $table->string('moved_with_email', 191)->nullable();
            $table->string('moved_with_phone', 50)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=In Transit, 2=Completed, 3=Pending, 4=Cancelled');
            $table->date('expected_return')->nullable();
            $table->date('actual_return')->nullable();
            $table->text('reason_of_moving');
            $table->string('action', 50)->nullable();
            $table->unsignedBigInteger('performed_by');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_pass_histories');
    }
};
