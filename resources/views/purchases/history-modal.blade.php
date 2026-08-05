<div id="history-mdl" class="amg-modal modal fade user-mdl-box" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-5">
            <!-- HEADER -->
            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title px-4">
                    {{ trans("config.purchase_fields.purchase_history") }}
                </h3>
                <button type="button" class="modal-close px-4" data-bs-dismiss="modal">✕</button>
            </div>
            <!-- BODY -->
            <div class="modal-body">
                <div class="container-fluid py-3 px-4">

                    <div id="history-loader" class="text-center py-5 d-none">
                        <i class="bi bi-arrow-repeat spin fs-1"></i>
                        <p class="mt-2">Loading history...</p>
                    </div>
                    <div id="history-content"></div>
                </div>
            </div>
            <!-- FOOTER -->
            <div class="modal-footer px-4 pb-4">
                <button type="button" class="amg-btn amg-btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>