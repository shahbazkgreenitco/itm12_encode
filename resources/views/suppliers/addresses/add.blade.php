{{--
/**
* ------------------------------------------------------------
* File: add.blade.php
* Module: Suppliers
* SUP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #SUP-002
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Changes for the validation error message
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div class="amg-modal modal fade user-mdl-box device-mdl-box" id="addAddressModal" tabindex="-1" role="dialog"
    aria-labelledby="addAddressModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content rounded-5">
            <form method="post" id="AddAddressForm" autocomplete="off" accept-charset="UTF-8"
                enctype="multipart/form-data" class="w-100">
                <div class="modal-header">
                    <h3 class="modal-title px-4">{{ trans('suppliers.modal.add_new_address') }}</h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div class="container-fluid">
                        <div class="row g-4 px-4">
                            <div class="col-md-6 amg-form-field-row">
                                <label class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.street1') }}
                                    <span class="required">*</span>
                                </label>
                                <div class="d-flex select-div">
                                    <span class="input-group-text"><i class="bi bi-signpost-2"></i></span>
                                    <input type="text" name="address" id="add_address" class="form-control"
                                        placeholder="{{ trans('suppliers.modal.street1_placeholder') }}" required>
                                </div>
                                <div class="field-error text-danger">{!! $errors->first('address') !!}</div>
                            </div>
                            <div class="col-md-6 amg-form-field-row">
                                <label
                                    class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.street2') }}</label>
                                <div class="d-flex select-div">
                                    <span class="input-group-text"><i class="bi bi-signpost-2"></i></span>
                                    <input type="text" name="address2" id="add_address2" class="form-control"
                                        placeholder="{{ trans('suppliers.modal.street2_placeholder') }}">
                                </div>
                                <div class="field-error text-danger">{!! $errors->first('address2') !!}</div>
                            </div>
                            <div class="col-md-6 amg-form-field-row">
                                <label class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.country') }}
                                    <span class="required">*</span>
                                </label>
                                <div class="d-flex select-div">
                                    <span class="input-group-text"><i class="bi bi-globe-central-south-asia"></i></span>
                                    <select name="country_id" id="add_country_id" class="form-control" required>
                                        <option value="">{{ trans('suppliers.modal.country_placeholder') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="field-error text-danger">{!! $errors->first('country_id') !!}</div>
                            </div>
                            <div class="col-md-6 amg-form-field-row">
                                <label class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.state') }}
                                    <span class="required">*</span>
                                </label>
                                <div class="d-flex select-div">
                                    <span class="input-group-text"><i class="bi bi-map-fill"></i></span>
                                    <select name="state_id" id="add_state_id" class="form-control" required>
                                        <option value="">{{ trans('suppliers.modal.state_placeholder') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="field-error text-danger">{!! $errors->first('state_id') !!}</div>
                            </div>
                            <div class="col-md-6 amg-form-field-row">
                                <label class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.city') }}
                                    <span class="required">*</span>
                                </label>
                                <div class="d-flex select-div">
                                    <span class="input-group-text"><i class="bi bi-buildings"></i></span>
                                    <select name="city_id" id="add_city_id" class="form-control" required>
                                        <option value="">{{ trans('suppliers.modal.city_placeholder') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="field-error text-danger">{!! $errors->first('city_id') !!}</div>
                            </div>
                            <div class="col-md-6 amg-form-field-row">
                                <label
                                    class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.zip') }}</label>
                                <div class="d-flex select-div">
                                    <span class="input-group-text"><i class="bi bi-mailbox"></i></span>
                                    <input type="text" name="zip" id="add_zip" class="form-control"
                                        placeholder="{{ trans('suppliers.modal.zip_placeholder') }}">
                                </div>
                                <div class="field-error text-danger">{!! $errors->first('zip') !!}</div>
                            </div>
                            <div class="col-md-6 amg-form-field-row">
                                <label
                                    class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.phone') }}</label>
                                <div class="d-flex select-div">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone" id="add_phone" class="form-control"
                                        placeholder="{{ trans('suppliers.modal.phone_placeholder') }}">
                                </div>
                                <div class="field-error text-danger">{!! $errors->first('phone') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end mb-4 pb-4 py-0" style="padding-inline: 40px;">
                    <div class="d-flex gap-2">
                        <button type="button" id="btnAddressSubmit"
                            class="amg-btn amg-btn-primary amg-btn-block amg-btn-md mb-2">
                            {{ trans('suppliers.modal.save') }}
                        </button>
                        <button type="button" class="amg-btn amg-btn-secondary amg-btn-block amg-btn-md mb-2"
                            data-bs-dismiss="modal">
                            {{ trans('suppliers.modal.cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
