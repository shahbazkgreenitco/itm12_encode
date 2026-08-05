{{--
/**
------------------------------------------------------------
File: modal_html.blade.php
Module: Device
DEV/26/07
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #DEV-004
Created On: 2026-07-06
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}

<div id="device-mdl" class="amg-modal amg-form-modal modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="device-mdl-frm" class="w-100 amg-form-theme" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="clone_img" id="clone_img">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="forAction" id="forAction" class="hidden" value="" />
            <input type="hidden" name="temp_id" id="temp_id" value="" />
            <input type="hidden" name="company_id" id="company_id_hidden" value="" disabled />
            <input type="hidden" name="status_id" id="status_id_hidden" value="" disabled />
            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">{{ trans('devices.device_fields.add_new_device') }}</h3>
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
                            <div class="col-auto step-col">
                                <div class="step-circle">1</div>
                            </div>
                            <div class="col">
                                <div class="amg-form-section-title mb-3">
                                    <span>{{ trans('devices.device_fields.device_details') }}</span>
                                </div>

                                <div class="row px-4">
                                    <!-- Device Tag -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.device_tag') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                                    <input type="text" name="asset_tag" id="asset_tag"
                                                        class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.device_tag') }}">
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Company -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.company') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <select name="company_id" id="company_id" class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Device Model -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.device_model') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-laptop"></i></span>
                                                    <select name="model_id" id="model_id" class="form-select"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Device Name -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.device_name') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-desktop"></i></span>
                                                    <input type="text" name="name" id="name" class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.Enter_Device_Name') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Department -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.department') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <select name="department_id" id="department_id" class="form-select">
                                                    <option value="">{{ trans('devices.device_fields.department') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.location') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                                    <select name="rtd_location_id" id="rtd_location_id"
                                                        class="form-select"></select>
                                                </div>
                                            </div>
                                            {{-- <div class="amg-form-error-wrap"></div> --}}
                                        </div>
                                    </div>

                                    <!-- Internal Place -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.internal_place') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                                <select name="internal_place_id" id="internal_place"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Device Status -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.device_status') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-battery-half"></i></span>
                                                <select name="status_id" id="status_id" class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Stock Place (conditional) -->
                                    <div class="col-md-6 mb-4 stockcover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.stock_place') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-box"></i></span>
                                                <select name="stock_place" id="stock_place"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Checkout To (conditional) -->
                                    <div class="col-md-6 mb-4 checkoutcover cover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.checkout_to') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                                <select name="assigned_for" id="assigned_for"
                                                    class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Checkout User (conditional) -->
                                    <div class="col-md-6 mb-4 usercover cover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.checkout_user') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                    <select name="assigned_to" id="assigned_to"
                                                        class="form-select"></select>
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Checkout Place (conditional) -->
                                    <div class="col-md-6 mb-4 placecover cover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.checkedout_place') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                                <select name="assigned_place" id="assigned_place"
                                                    class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Checkout Reason (conditional) -->
                                    <div class="col-md-6 mb-4 reasoncover cover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">Checkout Reason</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-list"></i></span>
                                                    <select name="checkout_reason" id="checkout_reason"
                                                        class="form-select"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Project Name (conditional) -->
                                    <div class="col-md-6 mb-4 project_name_cover cover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.project_name') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-tasks"></i></span>
                                                <select name="project_name" id="project_name"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Allocation Type (conditional) -->
                                    <div class="col-md-6 mb-4 allocation_type_cover cover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.allocation_type') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-tags"></i></span>
                                                <select name="allocation_type" id="allocation_type"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Expected Checkin Date -->
                                    <div class="col-md-6 mb-4 expected_checkin_cover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.expected_checkin_date') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                <input type="text" name="expected_checkin" id="expected_checkin"
                                                    class="form-control datepicker"
                                                    placeholder="{{ trans('devices.device_fields.Choose_Checkin_Date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Serial Code -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{
                                                trans('devices.device_fields.serialcode') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                                    <input type="text" name="serial" id="serial" class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.serial_code') }}">
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Product Number -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.product_number') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-123"></i></span>
                                                <input type="text" name="product_number" id="product_number"
                                                    class="form-control"
                                                    placeholder="{{ trans('devices.device_fields.enter_the_product_number') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Device UUID -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.device_uuid') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                                    <input type="text" name="uuid" id="uuid" class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.Enter_Device_UUID_Code') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Asset Type -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.asset_type') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                                <select name="asset_type_id" id="asset_type_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Device From -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.device_from') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="bi bi-box-arrow-in-right"></i></span>
                                                <select name="device_occure_type" id="device_occure_type"
                                                    class="form-select">
                                                    <option value="0">{{ trans('devices.device_fields.purchase_device')
                                                        }}</option>
                                                    <option value="1">Project Device</option>
                                                    <option value="2">Rental</option>
                                                    <option value="3">Customer Owned</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lease Reference (conditional) -->
                                    <div class="col-md-6 mb-4 leasecover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.contract_reference') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-file-earmark"></i></span>
                                                <select name="lease_id" id="lease_id" class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Asset Owner -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.asset_owner') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                    <select name="asset_owner" id="asset_owner"
                                                        class="form-select"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Supplier -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.supplier') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-truck"></i></span>
                                                <select name="supplier_id" id="supplier_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purchase Reference -->
                                    <div class="col-md-6 mb-4 purchasecover">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.purchase_reference') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-receipt"></i></span>
                                                <select name="invoice_id" id="invoice_id" class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purchase Date -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.purchase_date') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                <input type="text" name="purchase_date" id="purchase_date"
                                                    class="form-control datepicker"
                                                    placeholder="{{ trans('devices.device_fields.Choose_Purchase_Date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purchase Currency -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.purchase_currency_format') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i
                                                        class="bi bi-currency-dollar"></i></span>
                                                <select name="purchase_currency" id="purchase_currency"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purchase Cost -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.purchase_cost') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-currency-dollar"></i></span>
                                                    <div class="d-flex flex-column flex-grow-1">
                                                        <input type="text" name="purchase_cost" id="purchase_cost"
                                                            class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_Purchase_Cost') }}"
                                                            value="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Order Number -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.order_number') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                                    <input type="text" name="order_number" id="order_number"
                                                        class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.Enter_Order_Number') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Warranty Start Date -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.warranty_start_date') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                <input type="text" name="warranty_start_date" id="warranty_start_date"
                                                    class="form-control datepicker"
                                                    placeholder="{{ trans('devices.device_fields.Choose_Warranty_Start_Date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Warranty End Date -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.warranty_end_date') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                <input type="text" name="warrenty_end_date" id="warrenty_end_date"
                                                    class="form-control datepicker"
                                                    placeholder="{{ trans('devices.device_fields.Choose_Warranty_End_Date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Warranty Months -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.warranty') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                                    <input type="text" name="warranty_months" id="warranty_months"
                                                        class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.Enter_Warranty_Months') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- AMC Expire Date -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.amc_expire_date') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                <input type="text" name="amc_expire_date" id="amc_expire_date"
                                                    class="form-control datepicker"
                                                    placeholder="{{ trans('devices.device_fields.Choose_AMC_Expire_Date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- AMC Supplier -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.amc_supplier') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                                <select name="amc_supplier_id" id="amc_supplier_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Device IP -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.device_ip') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-wifi"></i></span>
                                                    <input type="text" name="ip" id="ip" class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.Enter_IP_Address') }}">
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Device MAC -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.device_mac') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-hdd-network"></i></span>
                                                    <input type="text" name="mac" id="mac" class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.Enter_MAC_Address') }}">
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-md-12 mb-4">
                                        <div class="amg-form-field d-flex align-items-start">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.notes') }}</label>
                                            <div class="d-flex flex-column flex-grow-1">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-card-text"></i></span>
                                                    <textarea name="notes" id="notes" class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.enter_notes') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="col-md-12 mb-4">
                                        <div class="amg-form-field d-flex align-items-start">
                                            <label class="form-label me-2 mb-0 text-end">{{
                                                trans('devices.device_fields.image') }}</label>
                                            <div class="input-group">
                                                <input type="file" name="image" id="image" class="form-control">
                                            </div>
                                        </div>
                                        <small class="text-muted ms-5 ps-1">('.jpg','.jpeg', '.png') Max file size:
                                            2MB</small>
                                    </div>

                                    <!-- Image Preview & Delete -->
                                    <div class="col-md-12 mb-4 imgviewcover">
                                        <div class="amg-form-field d-flex align-items-start">
                                            <label class="form-label me-2 mb-0 text-end"></label>
                                            <div class="d-flex align-items-center gap-3">
                                                <label>
                                                    <input type="checkbox" name="delete_img" id="delete_img" value="1"
                                                        class="form-check-input">
                                                    {{ trans('devices.device_fields.delete_present_image') }}
                                                </label>
                                                <img id="imgview" src="" style="width:80px; margin-top:10px;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Checkboxes Row -->
                                    <div class="col-md-12 mb-4">
                                        <div class="d-flex flex-wrap gap-4">
                                            <label>
                                                <input type="checkbox" name="sez_device" id="sez_device" value="1"
                                                    class="form-check-input">
                                                {{ trans('devices.device_fields.sez_device') }}
                                            </label>
                                            <label>
                                                <input type="checkbox" name="requestable" id="requestable" value="1"
                                                    class="form-check-input">
                                                {{ trans('devices.device_fields.requestable_device') }}
                                            </label>
                                            @if(config("services.live_monitor.enabled"))
                                            @can("LiveMonitorAdd")
                                            <label>
                                                <input type="checkbox" name="live_monitor" id="live_monitor" value="1"
                                                    class="form-check-input">
                                                {{ trans('devices.device_fields.live_monitor_device') }}
                                            </label>
                                            @endcan
                                            @endif
                                            <label>
                                                <input type="checkbox" name="high_pririty" id="high_pririty" value="1"
                                                    class="form-check-input">
                                                {{ trans('devices.device_fields.high_priority_device') }}
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Custom Fields Container -->
                                    <div class="col-md-12 custom-fields-follow"></div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= BILLING UNIT DEFINITION (STEP 2) ================= -->
                        @if($settings->cost_earned == 1)
                        <div class="row section-row">
                            <div class="col-auto step-col">
                                <div class="step-circle">2</div>
                            </div>
                            <div class="col">
                                <div class="amg-form-section-title mb-3 collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#billing-unit-defination">
                                    <div class="d-flex align-items-start gap-2">
                                        <svg width="17" height="9" viewBox="0 0 17 9" fill="none">
                                            <path d="M16.28 1.28L8.78 8.78L0.22 1.28" fill="#7F7F7F" />
                                        </svg>
                                        <div>
                                            <p class="mb-0">{{ trans('devices.device_fields.billing_unit_defination') }}
                                            </p>
                                            <small class="text-muted">Configure Billing Rates</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="collapse" id="billing-unit-defination">
                                    <div class="row px-4 mt-3">
                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.rate_hr') }}</label>
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="bi bi-currency-dollar"></i></span>
                                                        <input type="text" name="rate_hr" class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_rate_hr') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.rate_day') }}</label>
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="bi bi-currency-dollar"></i></span>
                                                        <input type="text" name="rate_day" class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_rate_day') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.rate_week') }}</label>
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="bi bi-currency-dollar"></i></span>
                                                        <input type="text" name="rate_week" class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_rate_week') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.rate_month') }}</label>
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="bi bi-currency-dollar"></i></span>
                                                        <input type="text" name="rate_month" class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_rate_month') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.rate_quarterly') }}</label>
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="bi bi-currency-dollar"></i></span>
                                                        <input type="text" name="rate_quarterly" class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_rate_quarterly') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.rate_half_yearly') }}</label>
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="bi bi-currency-dollar"></i></span>
                                                        <input type="text" name="rate_half_yearly" class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_rate_half_yearly') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.rate_yearly') }}</label>
                                                <div class="d-flex flex-column flex-grow-1">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="bi bi-currency-dollar"></i></span>
                                                        <input type="text" name="rate_yearly" class="form-control"
                                                            placeholder="{{ trans('devices.device_fields.Enter_rate_yearly') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- ================= RFID INTEGRATION (STEP 3) ================= -->
                        @if(config('services.assets.rfid_integration'))
                        <div class="row section-row">
                            <div class="col-auto step-col">
                                <div class="step-circle">3</div>
                            </div>
                            <div class="col">
                                <div class="amg-form-section-title mb-3 collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#rfid-integration">
                                    <div class="d-flex align-items-start gap-2">
                                        <svg width="17" height="9" viewBox="0 0 17 9" fill="none">
                                            <path d="M16.28 1.28L8.78 8.78L0.22 1.28" fill="#7F7F7F" />
                                        </svg>
                                        <div>
                                            <p class="mb-0">{{ trans('devices.device_fields.device_rfid') }}</p>
                                            <small class="text-muted">RFID Configuration</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="collapse" id="rfid-integration">
                                    <div class="row px-4 mt-3">
                                        <div class="col-md-12 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.device_rfid') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-tags"></i></span>
                                                    <select name="device_rfid[]" id="device_rfid" class="form-select"
                                                        multiple></select>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.scanner_id') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                                    <input type="text" name="scanner_id" id="scanner_id"
                                                        class="form-control"
                                                        placeholder="{{ trans('devices.device_fields.scanner_id') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label>
                                                <input type="checkbox" name="block_device_movement"
                                                    id="block_device_movement" value="1" class="form-check-input">
                                                {{ trans('devices.device_fields.block_device_movement') }}
                                            </label>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.in_antenna') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-arrows-in"></i></span>
                                                    <select name="in_antenna[]" id="in_antenna" class="form-select"
                                                        multiple>
                                                        <option>Select</option>
                                                        @for ($i=1;$i<=10;$i++) <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{
                                                    trans('devices.device_fields.out_antenna') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-arrows-out"></i></span>
                                                    <select name="out_antenna[]" id="out_antenna" class="form-select"
                                                        multiple>
                                                        <option>Select</option>
                                                        @for ($i=1;$i<=10;$i++) <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 pb-4">
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">{{ trans('button.save_changes')
                        }}</button>
                    <button type="button" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">{{
                        trans('button.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>