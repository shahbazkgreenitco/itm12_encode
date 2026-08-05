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
        Schema::create('procure_requests', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('procure_tag', 30)->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->integer('cost_center')->nullable()->comment('Department ID');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('pab_id')->nullable();
            $table->tinyInteger('approval_required')->nullable();
            $table->tinyInteger('required_minimum_approvals')->nullable()->default(1)->comment('if hierarchy_approval = 2, then this value will active');
            $table->tinyInteger('hierarchy_approval')->nullable()->comment('Set when created: 1 - Level By Level, 2 - Minimum Approval, 3 - Group Approval');
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->timestamps();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('po_generated_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->integer('po_created_by')->nullable()->comment('Who created the PO');
            $table->string('vendor_id', 191)->nullable();
            $table->string('document_file', 191)->nullable();
            $table->string('document_file_path', 191)->nullable();
            $table->bigInteger('incharge_id')->nullable();
            $table->date('final_delivery_date')->nullable();
            $table->boolean('without_quotation_approval')->default(false);
            $table->bigInteger('financial_year')->nullable();
            $table->text('is_temp')->nullable();
            $table->softDeletes();
            $table->boolean('notification_supplier')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_requests');
    }
};
