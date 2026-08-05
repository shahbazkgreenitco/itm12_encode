<div id="licensemodal" class="amg-modal amg-form-modal modal fade" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="license-mdl-frm" class="w-100 amg-form-theme" method="post" enctype="multipart/form-data">
            @csrf

            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="myLargeModalLabel"></h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <!-- BODY -->
                <div class="modal-body bg-white">
                    <div class="container-fluid py-3">
                        <div class="row section-row">
                            <input type="hidden" name="id" id="id" class="hidden" value="" />
                            <div class="col-12 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-3 mb-0 text-end">{{
                                        trans('licenses.license_fields.added_via') }} </label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="radio" name="added_via"
                                                id="manual_check" value="1">
                                            <label class="form-check-label" for="manual_check">{{
                                                trans('licenses.license_fields.manually_added_license') }} </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="added_via"
                                                id="network_check" value="2">
                                            <label class="form-check-label" for="network_check">
                                                {{ trans('licenses.license_fields.network_license') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label
                                        class="form-label me-2 mb-0 text-end ">{{trans('licenses.license_fields.software')}}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <select name="product_id[]" id="product_id" class="form-select"
                                            multiple></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label
                                        class="form-label me-2 mb-0 text-end ">{{trans('licenses.license_fields.version')}}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <select name="version[]" id="version" class="form-select" multiple></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                                    <div class="col-md-6 mb-4 ml-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end ">{{ trans('licenses.license_fields.check_all_versions') }}</label>
                                            <input type="checkbox" name="all_version_check" autocomplete="off" class="form-check-input" id="all_version_check" value="1"> 
                                        </div>
                                    </div>
                                                    
                                    <!-- Company -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required ">{{trans('licenses.license_fields.company')}}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <select name="company_id" id="company_id" class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                            <!-- Unique Tag -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.unique_tag') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="unique_tag" id="unique_tag" class="form-control"
                                            placeholder="{{ trans('licenses.license_fields.unique_tag') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Software Name -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 required mb-0 text-end">{{
                                        trans('licenses.license_fields.software_name') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-type"></i></span>
                                        <input type="text" name="name" id="software_name" class="form-control "
                                            placeholder="{{ trans('licenses.license_fields.software_name') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{ trans('licenses.license_fields.department') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <select name="department_id" id="department_id" 
                                                    class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                            <!-- Location -->

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end required">{{
                                        trans('licenses.license_fields.location') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                        <select name="location_id" id="location_id" class="form-select"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('licenses.license_fields.internal_place') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                                <select name="internal_place_id" id="internal_place"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                            <!-- Manufacturer -->

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.manufacturer') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <select name="manufacturer_id" id="manufacturer_id"
                                            class="form-select"></select>
                                    </div>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end ">{{
                                        trans('licenses.license_fields.category') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                        <select name="category_id" id="category_id" class="form-select"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- Unique Serial -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 required text-end">{{
                                        trans('licenses.license_fields.unq_serial') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-card-text"></i>
                                        </span>
                                        <textarea name="serial" id="uniqueSerial_id" class="form-control"></textarea>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end ">{{
                                        trans('licenses.license_fields.license_to_name') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-type"></i></span>
                                        <input type="text" name="license_name" id="license_to_name" class="form-control"
                                            placeholder="{{ trans('licenses.license_fields.license_to_name') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end ">{{
                                        trans('licenses.license_fields.license_to_email') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path
                                                    d="M17.5 3.75H2.5C2.33424 3.75 2.17527 3.81585 2.05806 3.93306C1.94085 4.05027 1.875 4.20924 1.875 4.375V15C1.875 15.3315 2.0067 15.6495 2.24112 15.8839C2.47554 16.1183 2.79348 16.25 3.125 16.25H16.875C17.2065 16.25 17.5245 16.1183 17.7589 15.8839C17.9933 15.6495 18.125 15.3315 18.125 15V4.375C18.125 4.20924 18.0592 4.05027 17.9419 3.93306C17.8247 3.81585 17.6658 3.75 17.5 3.75ZM15.893 5L10 10.4023L4.10703 5H15.893ZM16.875 15H3.125V5.79609L9.57734 11.7109C9.69265 11.8168 9.84348 11.8755 10 11.8755C10.1565 11.8755 10.3073 11.8168 10.4227 11.7109L16.875 5.79609V15Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <input type="text" name="license_email" id="license_email" autocomplete="off"
                                            class="form-control"
                                            placeholder="{{ trans('licenses.license_fields.license_to_email') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 required mb-0 text-end ">{{
                                        trans('licenses.license_fields.seats') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-hash"></i>
                                        </span>
                                        <input type="text" name="seats" id="seats_id" autocomplete="off"
                                            class="form-control "
                                            placeholder="{{ trans('licenses.license_fields.seats') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4 ml-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end ">{{
                                        trans('licenses.license_fields.reassignable') }}</label>
                                    <input type="checkbox" name="reassignable" autocomplete="off"
                                        class="form-check-input" id="reassignable" value="1">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4 ml-4 hide" id="is_tracked_div">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end ">{{
                                        trans('licenses.license_fields.enable_software_tracking') }}</label>
                                    <input type="checkbox" name="is_tracked" id="is_tracked" autocomplete="off"
                                        class="form-check-input" value="1">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.supplier') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <select name="supplier_id" id="supplier_id" class="form-select"></select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.order_number') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-hash"></i>
                                        </span>
                                        <input type="text" name="order_number" id="order_number" class="form-control"
                                            placeholder="{{ trans('licenses.license_fields.order_number') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.purchase_reference') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-receipt"></i></span>
                                        <select name="invoice_id[]" id="invoice_id" class="form-select"></select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.purchase_date') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="text" name="purchase_date" id="purchase_date" class="form-control"
                                            placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.bill_currency_format') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-currency-dollar"></i>
                                        </span>
                                        <select name="currency" id="bill_currency" class="form-select"></select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.purchase_cost') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-currency-dollar"></i>
                                        </span>
                                        <input type="text" name="purchase_cost" id="purchase_cost" class="form-control"
                                            value="" placeholder="{{ trans('licenses.license_fields.purchase_cost') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.purchase_order') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-hash"></i>
                                        </span>
                                        <input type="text" name="purchase_order" id="purchase_order"
                                            class="form-control"
                                            placeholder="{{ trans('licenses.license_fields.purchase_order') }}">
                                    </div>
                                </div>
                            </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('licenses.license_fields.agreement_no') }}/{{ trans('licenses.license_fields.oem_number') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-hash"></i>
                                                </span>
                                                <input type="text" name="agreement_no" id="agreement_no"
                                                    class="form-control" placeholder="{{ trans('licenses.license_fields.agreement_no')}}/{{ trans('licenses.license_fields.oem_number') }}">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('licenses.license_fields.expire_date') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-calendar"></i>
                                                </span>
                                                <input type="text" name="expiration_date" id="expire_date"
                                                    class="form-control" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>
                                    </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.depreciation') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <select name="depreciation_id" id="depreciation_id"
                                            class="form-select"></select>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label class="form-label mb-0 text-end me-3">
                                        {{ trans('licenses.license_fields.maintained') }}
                                    </label>

                                    <label class="mb-0 ms-2">
                                        <input type="checkbox" name="maintained_id" class="form-check-input me-2"
                                            id="maintained_id" value="1">
                                        {{ trans('licenses.license_fields.yes') }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.termination_date') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="text" name="termination_date" id="termination_date"
                                            class="form-control" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field  amg-form-field-row ">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.notes') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-card-text"></i>
                                        </span>
                                        <textarea name="notes" id="notes_id" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end ">{{
                                        trans('licenses.license_fields.support') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-type"></i></span>
                                        <input type="text" name="support" id="support_id" class="form-control"
                                            placeholder="{{ trans('licenses.license_fields.support') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('licenses.license_fields.image') }}</label>
                                    <div class="input-group">
                                        <input type="file" name="image" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 hide " id="license_image_view">
                                <div class="amg-form-field  amg-form-field-row">
                                    <label for="delete_img" class="form-label text-end me-2 mb-0">{{
                                        trans('licenses.license_fields.delete_present_img') }}</label>
                                    <div class="input-group ">
                                        <input name="delete_img" id="delete_img" autocomplete="off" type="checkbox"
                                            value="1" class="form-check-input ms-13 mt-2">
                                    </div>
                                    <div class="amg-form-image-preview-wrap imgviewcover">
                                        <div class="amg-form-image-preview">
                                            <img src="" id="imgview" class="amg-form-image-preview-img"
                                                alt="{{ trans('licenses.license_fields.licenses_img_alt') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FOOTER -->
                <div class="modal-footer px-4 pb-4">
                    <button type="button" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">{{
                        trans('licenses.license_form.close') }}</button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">{{
                        trans('licenses.license_form.save_license') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>