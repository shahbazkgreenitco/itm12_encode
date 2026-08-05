<div class="amg-modal modal fade" id="purchaseFilterModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1200px;">
        <div class="modal-content rounded-5 bg-white">
            <!-- HEADER -->
            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title px-4">Purchase Filters</h3>
                <button data-bs-dismiss="modal" class="modal-close px-4">
                    ✖
                </button>
            </div>
            <!-- BODY -->
            <div class="modal-body">
                <div class="row g-3 px-4">

                    <!-- LOCATION -->
                    <div class="col-md-3">
                        <label class="form-label">Location</label>
                        <select id="filter_by_location" class="form-select filter-input"></select>
                    </div>
                    <!-- SUPPLIER -->
                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <select id="filter_by_supplier" class="form-select filter-input"></select>
                    </div>
                    <!-- STATUS -->
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select id="filter_by_status" class="form-select filter-input">
                            <option value="null">No Filter</option>
                            <option value="0">Not Yet Received</option>
                            <option value="1">Fully Received</option>
                            <option value="2">Partially Received</option>
                            <option value="3">Provision</option>
                        </select>
                    </div>
                    <!-- DATE TYPE -->
                    <div class="col-md-3">
                        <label class="form-label">Filter By Date</label>
                        <select id="filter_by_date" class="form-select filter-input">
                            <option value="null">No Filter</option>
                            <option value="1">Purchase Date</option>
                            <option value="2">Received At</option>
                            <option value="3">Created At</option>
                            <option value="4">Updated At</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- FOOTER -->
            <div class="modal-footer justify-content-start mb-4 pb-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button type="button" class="amg-btn amg-btn-primary btn-filter" style="min-width: 300px;">
                        Apply Filters
                    </button>
                    <button type="button" class="amg-btn amg-btn-ghost bg-black text-white btn-clear-filter"
                        style="min-width: 300px;">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>