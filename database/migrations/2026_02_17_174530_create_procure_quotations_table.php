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
        Schema::create('procure_quotations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('pr_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->date('quotate_date')->nullable();
            $table->decimal('total_amount', 20)->nullable();
            $table->timestamps();
            $table->bigInteger('quotation_position')->nullable()->comment('To store the quotation added on list 1st, 2nd or 3rd');
            $table->string('po_number', 100)->nullable()->comment('Manual PO Number of Purchase Order Bill');
            $table->date('po_due_date')->nullable()->comment('PO Due Date');
            $table->text('po_shipping_address')->nullable();
            $table->longText('po_terms_n_conditions')->nullable();
            $table->longText('po_payment_terms')->nullable();
            $table->longText('po_notes')->nullable();
            $table->tinyInteger('po_approved_quotate')->nullable()->comment('To identify which quotation approved');
            $table->dateTime('po_created_at')->nullable()->comment('To know when po generated');
            $table->decimal('po_tax_cgst', 30)->nullable();
            $table->decimal('po_tax_sgst', 30)->nullable();
            $table->decimal('po_net_amount', 30)->nullable();
            $table->string('po_invoice_num', 100)->nullable()->comment('Supplier Invoice Number');
            $table->string('po_invoice_attachment')->nullable()->comment('Supplier invoice file soft copy');
            $table->string('po_invoice_attachment_path')->nullable()->comment('storage path of file on mechine');
            $table->string('quotation_file')->nullable()->comment('Quotation File Soft Copy');
            $table->string('quotation_file_attachment_path')->nullable()->comment('storage path of file on mechine');
            $table->integer('po_created_by')->nullable()->comment('Who created the PO');
            $table->longText('quotation_notes')->nullable()->comment('Notes of quotation');
            $table->boolean('include_taxes')->nullable();
            $table->text('tax_name')->nullable();
            $table->integer('tax_percentage')->nullable();
            $table->bigInteger('tax_amount')->nullable();
            $table->bigInteger('po_company_id')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->boolean('partial_payment')->nullable();
            $table->decimal('grand_total_amount', 12)->nullable();
            $table->longText('quote_ref')->nullable();
            $table->longText('ship_to')->nullable();
            $table->longText('project')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procure_quotations');
    }
};
