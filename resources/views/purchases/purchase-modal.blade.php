<div id="purchaseModal" class="amg-modal modal fade user-mdl-box" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form id="purchaseForm">

            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">Add Purchase</h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal">✕</button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">

                            <!-- PURCHASE DATE -->
                            <div class="col-md-6">
                                <label class="form-label b1-text me-2 mb-0 text-end required">Purchase Date</label>
                                <span class="text-danger">*</span>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-calendar"></i>
                                    </span>
                                    {{-- <input type="date" name="invoice_date" class="form-control" required> --}}
                                    <input type="text" name="invoice_date" id="invoice_date"
                                        class="form-control datepicker" required>
                                </div>
                            </div>

                            <!-- RECEIVED DATE -->
                            <div class="col-md-6">
                                <label class="form-label">Received Date</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-calendar-check"></i>
                                    </span>
                                    <input type="text" name="received_date" class="form-control datepicker">
                                </div>
                            </div>

                            <!-- PO NUMBER -->
                            <div class="col-md-6">
                                <label class="form-label">PO Number</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-book"></i>
                                    </span>
                                    <input type="text" name="po_number" id="po_number" class="form-control">
                                </div>
                            </div>

                            <!-- INVOICE NO -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Invoice No</label>
                                <span class="text-danger">*</span>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-receipt"></i>
                                    </span>
                                    <input type="text" name="invoice_no" id="invoice_no" class="form-control">
                                </div>
                            </div>

                            <!-- COMPANY -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Company</label>
                                <span class="text-danger">*</span>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-building"></i>
                                    </span>
                                    <select name="company_id" id="company_id" class="form-select"></select>
                                </div>
                            </div>

                            <!-- SUPPLIER -->
                            <div class="col-md-6">
                                <label class="form-label">Supplier</label>
                                {{-- <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <select name="supplier_id" id="supplier_id" class="form-select"></select>
                                </div> --}}
                                <div class="input-group input-group-lg custom-select2-group">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <select id="supplier_id" name="supplier_id" class="form-select"></select>
                                </div>
                            </div>

                            <!-- LOCATION -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Location</label>
                                <span class="text-danger">*</span>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-geo-alt"></i>
                                    </span>
                                    <select name="location_id" id="location_id" class="form-select"></select>
                                </div>
                            </div>

                            <!-- QTY -->
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-box"></i>
                                    </span>
                                    <input type="text" name="qty" id="" class="form-control">
                                </div>
                            </div>

                            <!-- CURRENCY -->
                            {{-- <div class="col-md-6">
                                <label class="form-label fw-bold">Currency</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-currency-rupee"></i>
                                    </span>
                                    <select name="currency" class="form-select"></select>
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Currency</label>
                                <span class="text-danger">*</span>

                                <div class="input-group input-group-lg ">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-currency-rupee"></i>
                                    </span>

                                    <select name="currency" id="currency" class="form-select">
                                        <option value="">Select Currency</option>
                                        @foreach(App\Models\Currency::getCurrencies() as $key => $value)
                                            <option value="{{ $key }}">
                                                {{ $value['name'] }} ({!! $value['symbol_html'] !!})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- BILL AMOUNT -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bill Amount</label>
                                <span class="text-danger">*</span>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-cash"></i>
                                    </span>
                                    <input type="text" name="bill_amount" class="form-control">
                                </div>
                            </div>

                            <!-- INCLUDE TAX -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    Include Taxes
                                </label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-percent"></i>
                                    </span>

                                    <select name="include_taxes" id="include_taxes" class="form-select">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>

                            <!-- TAX -->
                            {{-- <div class="col-md-6"> --}}
                                <div class="col-md-6" id="tax_wrapper" style="display: none;">

                                    <label class="form-label">Tax</label>
                                    <select name="tax_id" id="tax_id" class="form-select"></select>
                                </div>

                                <!-- DYNAMIC TAX ELEMENTS -->
                                <div class="col-md-12 customOptionsHolders"></div>

                                <!-- STATUS -->
                                {{-- <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="fully_received" id="fully_received" class="form-select">
                                        <option value="0">Not Yet Received</option>
                                        <option value="1">Fully Received</option>
                                        <option value="2">Partially Received</option>
                                        <option value="3">Provision</option>
                                    </select>
                                </div> --}}

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        Status
                                    </label>

                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text red-bg">
                                            <i class="bi bi-flag"></i>
                                        </span>

                                        <select name="fully_received" id="fully_received" class="form-select">
                                            <option value="0">Not Yet Received</option>
                                            <option value="1">Fully Received</option>
                                            <option value="2">Partially Received</option>
                                            <option value="3">Provision</option>
                                        </select>
                                    </div>
                                </div>



                                <!-- OTHER INFO -->
                                <div class="col-md-6">
                                    <label class="form-label">Other Info</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text red-bg">
                                            <i class="bi bi-file-text"></i>
                                        </span>
                                        <input type="text" name="other_info" id="other_info" class="form-control"
                                            placeholder="{{ trans('config.purchase_fields.other_info') }}">
                                    </div>
                                </div>

                                <!-- OTHER INFO 1 -->
                                <div class="col-md-6">
                                    <label class="form-label">Other Info 1</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text red-bg">
                                            <i class="bi bi-file-text"></i>
                                        </span>
                                        <input type="text" name="other_info1" id="other_info1" class="form-control"
                                            placeholder="{{ trans('config.purchase_fields.other_info_1') }}">
                                    </div>
                                </div>

                                <!-- NOTES -->
                                <div class="col-md-12">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="50"
                                        style="height: 139px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer px-4 pb-4">
                        <button type="submit" class="amg-btn amg-btn-primary btn-save-purchase">
                            Save Purchase
                        </button>

                        <button type="button" class="amg-btn amg-btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>

                </div>
        </form>
    </div>
</div>