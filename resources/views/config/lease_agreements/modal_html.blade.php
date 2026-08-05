{{--
/**
------------------------------------------------------------
File: modal_html.blade.php
Module: Contract Agreement (Lease Agreements)
CAT/26/04
------------------------------------------------------------
Version: 1.1.0
Author: Safdar Ali
Page ID: #CAT-003
Created On: 2026-04-28
------------------------------------------------------------

Description:
Modal form for adding and editing Contract Agreements.

Features:
- Add / Edit contract details
- File upload with preview
- Delete existing attachment (checkbox-based)
- Dynamic dropdowns (company, supplier, category, model, device)
- Date selection (start & end date)
- Cost & currency handling
- Status management

------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
[1.0.1] - Added attachment preview & delete option
[1.0.2] - fix the models and devices dropdown funcality and modify the ui 
[1.0.3] - fixed close button, correct ui (manjeet) - 2026-07-26 
------------------------------------------------------------
*/
--}}
<div id="leasemd1" class="amg-modal amg-form-modal modal fade user-mdl-box" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width:50%; margin:auto;">
        <form id="leaseform" autocomplete="off" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="modal-content rounded-5">
                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4"></h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-2" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none"><path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor"></path></svg>
                    </button>
                </div>
                <!-- BODY -->
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">
                            <!-- COMPANY -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.company") }} <span
                                        class="required"></span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <select name="company_id" id="company_id" class="form-select select2"></select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <!-- CONTRACT NUMBER -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.contractor_number") }} <span
                                        class="required"></span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-text"></i></span>
                                    <input type="text" name="contract_number" id="contract_number" class="form-control bg-white" placeholder="{{
                                    trans("contract_agreement.contract_agreement_fields.contractor_number") }}">
                                </div>
                            </div>
                            <!-- LEASER -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.contractor_name") }} <span
                                        class="required"></span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <select name="leaser" id="leaser" class="form-select"></select>
                                </div>
                            </div>
                            <!-- LEASE TYPE -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.contract_type") }}<span
                                        class="required"></span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <select name="lease_type" id="lease_type" class="form-select select2"></select>
                                </div>
                            </div>
                            <!-- START DATE -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.from_date") }}<span
                                        class="required"></span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    <input type="text" name="start_date" id="start_date" class="form-control bg-white"
                                        placeholder="{{ trans("contract_agreement.contract_agreement_fields.from_date")
                                        }}">
                                </div>
                            </div>
                            <!-- END DATE -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.to_date") }}<span
                                        class="required"></span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                    <input type="text" name="end_date" id="end_date" class="form-control bg-white"
                                        placeholder="{{ trans("contract_agreement.contract_agreement_fields.to_date")
                                        }}">
                                </div>
                            </div>
                            <!-- MAINTENANCE -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.repair_maintenance") }}<span
                                        class="required"></span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tools"></i></span>
                                    <select name="maintenance_incharge" id="maintenance_incharge" class="form-control bg-white">
                                        <option value="">{{
                                            trans("contract_agreement.contract_agreement_fields.select_option") }}
                                        </option>
                                        <option value="1">By Company</option>
                                        <option value="2">By Contractor</option>
                                    </select>
                                </div>
                            </div>
                            <!-- CATEGORY -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.category") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                    <select name="category_id" id="category_id" class="form-select"></select>
                                </div>
                            </div>
                            <!-- MODEL -->
                            <div class="col-md-6" id="model_ids">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.model") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-box"></i></span>
                                    <select name="model_id" id="model_id" class="form-select"></select>
                                </div>
                            </div>
                            <!-- DEVICE -->
                            <div class="col-md-6" id="device_ids">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.device") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-laptop"></i></span>
                                    <select name="device_id[]" id="device_id" class="form-select"></select>
                                </div>
                            </div>
                            <!-- CURRENCY -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.currency_format") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <select name="currency_format" id="currency_format"
                                        class="form-select select2"></select>
                                </div>
                            </div>
                            <!-- COST -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{
                                    trans("contract_agreement.contract_agreement_fields.cost") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="text" name="cost" id="cost" class="form-control bg-white"
                                        placeholder="{{ trans("contract_agreement.contract_agreement_fields.cost") }}">
                                </div>
                                                                <div class="amg-form-error-wrap"></div>

                            </div>
                            <!-- STATUS -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ trans("content.device_fields.status") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                    <select name="activated" id="active_status" class="form-select"></select>
                                </div>
                            </div>
                            <!-- DESCRIPTION -->
                            <div class="col-md-12">
                                <label class="form-label">{{
                                    trans("contract_agreement.contract_agreement_fields.description") }}</label>
                                <div class="input-group">
                                    <textarea name="description" id="description" class="form-control bg-white" placeholder="{{ trans('contract_agreement.contract_agreement_fields.description') }}"></textarea>
                                </div>
                            </div>
                            <!-- ATTACHMENT -->
                            <div class="col-md-12 amg-form-field">
                                <label class="form-label">{{
                                    trans("contract_agreement.contract_agreement_fields.attachment") }}</label>
                                <input type="file" name="attachment" id="attachment" class="form-control bg-white">
                                    <div class="mt-1 fs-1">
                                        {{ trans('contract_agreement.contract_agreement_fields.maximum_up_to_1_file_with_size') }}
                                    </div>
                                    <div class="mt-1 fs-1 mb-1">
                                        {{ trans('contract_agreement.contract_agreement_fields.allowed_file_formats') }}
                                    </div>
                            </div>
                            <div class="row imgviewcover">
                                <div class="col-md-12">
                                    <!-- DELETE CHECKBOX -->
                                    <div class="mb-2">
                                        <label>
                                            <input type="checkbox" name="delete_img" id="delete_img" value="1" />
                                            {{
                                            trans("contract_agreement.contract_agreement_fields.delete_present_image")
                                            }}
                                        </label>
                                    </div>
                                    <!-- FILE PREVIEW -->
                                    <img id="imgview" src="" class="img-thumbnail" style="max-height:120px;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FOOTER -->
                <div class="modal-footer justify-content-end px-4 pb-4">
                    <button type="button" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">
                        {{ trans('contract_agreement.contract_agreement_fields.cancel') }}
                    </button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">
                        {{ trans('contract_agreement.contract_agreement_fields.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>