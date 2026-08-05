{{-- 
/**
------------------------------------------------------------
File: modal_html.blade.php
Module: Accessories
ACC/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-002
Created On: 2026-05-12
Reviewed By: -
------------------------------------------------------------
Purpose:
Add/Edit/Clone Accessories Modal

------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}


<div id="accessory-mdl" class="amg-modal amg-form-modal modal fade" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="accessory-mdl-frm" class="w-100 amg-form-theme" method="post" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="clone_img" id="clone_img">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="forAction" id="forAction" class="hidden" value=""/>

            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">{{ trans('accessories.accessory_fields.add_accessory') }}</h3>
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
                                    <span>{{ trans('accessories.accessory_fields.accessory_details') }}</span>
                                </div>

                                <div class="row px-4">
                                    <!-- Unique Tag -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.unique_tag') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                                <input type="text" name="unique_tag" id="unique_tag" class="form-control" placeholder="{{ trans('accessories.accessory_fields.unique_tag') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Company -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{ trans('accessories.accessory_fields.company') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <select name="company_id" id="company_id" class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Name -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{ trans('accessories.accessory_fields.name') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-type"></i></span>
                                                <input type="text" name="name" class="form-control" placeholder="{{ trans('accessories.accessory_fields.name') }}">
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{ trans('accessories.accessory_fields.category') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                                <select name="category_id" id="category_id"
                                                    class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">

                                            <label class="form-label me-2 mb-0 text-end required">
                                                {{ trans('accessories.accessory_fields.qty') }}
                                                <span class="text-danger"></span>
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-box"></i>
                                                </span>

                                                <input type="number" name="qty" class="form-control"
                                                    placeholder="{{ trans('accessories.accessory_fields.qty') }}">
                                            </div>

                                            <div class="amg-form-error-wrap"></div>

                                        </div>
                                    </div>

                                    <!-- Location -->
                                   

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{ trans('accessories.accessory_fields.location') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                                <select name="location_id" id="location_id"
                                                    class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>


                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.internal_place') }}</label>
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
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.supplier') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                <select name="supplier_id" id="supplier_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.purchase_reference') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-receipt"></i></span>
                                                <select name="invoice_id" id="invoice_id" class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                              
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.purchase_date') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-calendar"></i>
                                                </span>
                                                <input type="text" name="purchase_date" id="purchase_date"
                                                    class="form-control" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.purchase_currency_format') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-currency-dollar"></i>
                                                </span>
                                                <select name="purchase_currency" id="purchase_currency"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Purchase Cost -->
                                      <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.purchase_cost') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-currency-dollar"></i>
                                                </span>
                                                <input type="text" name="purchase_cost" id="purchase_cost"
                                                    class="form-control" value="" placeholder="{{ trans('accessories.accessory_fields.purchase_cost') }}">
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.order_number') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-hash"></i>
                                                </span>
                                                <input type="text" name="order_number" id="order_number"
                                                    class="form-control" placeholder="{{ trans('accessories.accessory_fields.order_number') }}">
                                            </div>
                                        </div>
                                    </div>

                                   

                                    {{-- <div class="col-md-6 mb-4">
                                        <label>
                                            <input type="checkbox" name="thresholds_alerts" value="1"> Send Alerts
                                        </label>
                                    </div> --}}

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end required">{{ trans('accessories.accessory_fields.manufacturer') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                                <select name="manufacturer_id" id="manufacturer_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.department') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <select name="department_id" id="department_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                  

                                    <!-- Notes -->
                                    <div class="col-md-12 mb-4">
                                        <div class="amg-form-field d-flex align-items-start">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.notes') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-card-text"></i>
                                                </span>
                                                <textarea name="notes" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Image -->
                                     <div class="col-md-12 mb-4">
                                        <div class="amg-form-field d-flex align-items-start">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.image') }}</label>
                                            <div class="input-group">
                                               
                                                <input type="file" name="image" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6 mb-4">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.image') }}</label>
                                            <input type="file" name="image" class="form-control">
                                        </div>
                                    </div> --}}


                                    <div class="col-md-6 mb-4 imgviewcover">
                                        <div  class="amg-form-field d-flex align-items-start">
                                            <label>
                                                <input type="checkbox" name="delete_img" id="delete_img" value="1" class="form-check-input">
                                               {{ trans('accessories.accessory_fields.delete_image') }}
                                            </label>
                                        </div>
                                        <img id="imgview" src="" style="width:80px; margin-top:10px;">
                                    </div>

                                     {{-- @if($requestable_enabled) --}}
                                    <div class="col-md-6 mb-4">
                                        <label>
                                            <input type="checkbox" name="requestable_accessory" class="form-check-input"
                                                id="requestable_accessory" value="1"> {{ trans('accessories.accessory_fields.requestable_accessory') }}
                                        </label>
                                    </div>
                                    {{-- @endif --}}

                                </div>
                            </div>
                        </div>

                        <!-- ================= STEP 2 (COLLAPSE) ================= -->
                        <div class="row section-row">

                            <div class="col-auto step-col">
                                <div class="step-circle">2</div>
                            </div>

                            <div class="col">

                                <!-- COLLAPSE HEADER -->
                                <div class="amg-form-section-title d-flex flex-column mb-3 collapsed"
                                    data-bs-toggle="collapse" data-bs-target="#accessory-threshold">
                                    <div class="d-flex align-items-start gap-2">
                                        <svg width="17" height="9" viewBox="0 0 17 9" fill="none">
                                            <path d="M16.28 1.28L8.78 8.78L0.22 1.28" fill="#7F7F7F" />
                                        </svg>
                                        <div>
                                            <p class="mb-0">{{ trans('accessories.accessory_fields.threshold_settings') }}</p>
                                            <small class="text-muted">{{ trans('accessories.accessory_fields.configure_alerts_and_limits') }}</small>
                                        </div>

                                    </div>
                                </div>

                                <!-- COLLAPSE BODY -->
                                <div class="collapse" id="accessory-threshold">
                                    <div class="row px-4 mt-3">
                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.threshold_value') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-bar-chart"></i>
                                                    </span>
                                                    <input type="number" name="accessory_thresholds"
                                                        class="form-control" placeholder="{{ trans('accessories.accessory_fields.threshold_value') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.reorder_limit') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </span>
                                                    <input type="number" name="reorder_limits" class="form-control" placeholder="{{ trans('accessories.accessory_fields.reorder_limit') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <div class="amg-form-field d-flex align-items-start">
                                                <label class="form-label me-2 mb-0 text-end">{{ trans('accessories.accessory_fields.alert_to') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-people"></i>
                                                    </span>
                                                    <select multiple name="threshold_alert_users[]" id="threshold_alert_users"
                                                        class="form-select"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 pb-4">
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">{{ trans('accessories.accessory_fields.save') }}</button>
                    <button type="button" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">{{ trans('accessories.accessory_fields.close') }}</button>
                </div>

            </div>
        </form>
    </div>
</div>