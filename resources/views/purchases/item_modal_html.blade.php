<div id="purchases_item_modal" class="amg-modal modal fade user-mdl-box" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form id="itemForm">

            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">
                        {{ trans("config.purchase_fields.add_Item") }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal">✕</button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">

                            <!-- ITEM NAME -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">
                                    {{ trans("config.purchase_fields.item_name") }}
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-box"></i>
                                    </span>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="{{ trans('config.purchase_fields.enter_item') }}">
                                </div>
                            </div>

                            <!-- QUANTITY -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    {{ trans("config.purchase_fields.quantity") }}
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-123"></i>
                                    </span>
                                    <input type="text" name="qty" id="qty" class="form-control"
                                        placeholder="{{ trans('config.purchase_fields.quantity') }}">
                                </div>
                            </div>

                            <!-- UNIT -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    {{ trans("config.purchase_fields.unit") }}
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group input-group-lg custom-select2-group">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-rulers"></i>
                                    </span>
                                    <select name="units" id="units" class="form-select select2"></select>
                                </div>
                            </div>

                            <!-- PRICE -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    {{ trans("config.purchase_fields.price_per_quantity") }}
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-currency-rupee"></i>
                                    </span>
                                    <input type="text" name="price" id="price" class="form-control"
                                        placeholder="{{ trans('config.purchase_fields.price_per_quantity') }}">
                                </div>
                            </div>

                            <!-- TOTAL (UX IMPROVEMENT ) -->
                            {{-- <div class="col-md-6">
                                <label class="form-label fw-bold">Total</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text red-bg">
                                        <i class="bi bi-calculator"></i>
                                    </span>
                                    <input type="text" id="total" class="form-control" readonly>
                                </div>
                            </div> --}}

                            <!-- DESCRIPTION -->
                            <div class="col-md-12">
                                <label class="form-label">
                                    {{ trans("config.purchase_fields.description") }}
                                </label>

                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 pb-4">

                    <i class="fa fa-spinner fa-spin me-auto" id="img" style="display:none"></i>

                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">
                        {{ trans("config.purchase_fields.save") }}
                    </button>

                    <button type="button" class="amg-btn amg-btn-outline-secondary" data-bs-dismiss="modal">
                        {{ trans("config.purchase_fields.close") }}
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>