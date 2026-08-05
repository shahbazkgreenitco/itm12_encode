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
        Schema::create('lease_agreement_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lease_id');
            $table->integer('company_id')->nullable();
            $table->integer('leaser')->nullable()->comment('Supplier ID');
            $table->integer('lease_type')->nullable()->comment('Lease Types');
            $table->integer('maintenance_incharge')->nullable()->comment('Maintenance Incharge');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->string('contract_number', 100)->nullable();
            $table->longText('description')->nullable();
            $table->string('attachment')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->text('device_id')->nullable();
            $table->decimal('cost', 9)->nullable();
            $table->text('currency_format')->nullable();
            $table->integer('status')->default(1)->comment('0 = Inactive, 1 = Active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_agreement_history');
    }
};
