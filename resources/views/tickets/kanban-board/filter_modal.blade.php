<div class="amg-modal modal fade" id="mdl-filterModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content rounded-5">

            <!-- Header -->
            <div class="modal-header d-flex align-items-center py-3 pt-4">

                <h3 class="modal-title s1-text fw-semibold">
                    Filter Options
                </h3>

                <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">

                    <svg style="height:27px;width:27px;min-width:27px;" viewBox="0 0 31 31" fill="none">
                        <path
                            d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277Z"
                            fill="#7F7F7F" />
                    </svg>

                </button>

            </div>

            <form id="frm-filterModal" name="frm-filterModal">

                <!-- Body -->
                <div class="modal-body">

                    <div class="row mb-3 align-items-center">

                        <label class="col-lg-4 col-md-4 col-form-label">
                            Filter By Department
                        </label>

                        <div class="col-lg-8 col-md-8">

                            <select id="department" name="department" class="form-control select2">
                            </select>

                        </div>

                    </div>

                    <div class="row mb-3 align-items-center">

                        <label class="col-lg-4 col-md-4 col-form-label">
                            Filter By Problem Category
                        </label>

                        <div class="col-lg-8 col-md-8">

                            <select id="problemCategory" name="problemCategory" class="form-control select2">
                            </select>

                        </div>

                    </div>

                    <div class="row mb-3 align-items-center d-none" id="subCategoryWrapper">

                        <label class="col-lg-4 col-md-4 col-form-label">
                            Filter By Sub Category
                        </label>

                        <div class="col-lg-8 col-md-8">

                            <select id="subCategory" name="subCategory" class="form-control select2">
                            </select>

                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer justify-content-end pb-4 px-4">

                    <div class="amg-btn-group gap-3">

                        <button type="button" id="btnFilter" class="amg-btn amg-btn-primary">

                            Filter

                        </button>

                        <button type="button" id="btnClear" class="amg-btn amg-btn-ghost bg-black text-white">

                            Clear

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>
