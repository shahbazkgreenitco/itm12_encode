{{--
/**
* ------------------------------------------------------------
* File: add.blade.php
* Module: Suppliers
* SUP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #SUP-005
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Changes for the validation error message
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div class="amg-modal modal fade user-mdl-box" id="addSupplierModal" tabindex="-1" role="dialog"
    aria-labelledby="addSupplierLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="locationModalLabel">
                    {{ trans('suppliers.modal.add_internal_place') }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <form method="post" id="SupplierForm" autocomplete="off" accept-charset="UTF-8"
                enctype="multipart/form-data" class="form-horizontal">
                    <div class="modal-body no-side-pad back-gray pt-0">
                        <div id="mytabs" class="nav-tabs-custom tab-base">
                        {{-- <div class="tab-bar">
                            <ul class="nav nav-underline no-border-top pt-2" id="myTab" role="tablist">
                                <li class="nav-item border-0">
                                    <a class="nav-link active" id="basic-info-tab" data-bs-toggle="tab"
                                        href="#basic-info" role="tab">
                                            <span>{{ trans('suppliers.modal.tab_basic') }}</span>
                                        </a>
                                    </li>
                                <li class="nav-item border-0">
                                    <a class="nav-link" id="address-details-tab" data-bs-toggle="tab"
                                        href="#address-details" role="tab">
                                            <span>{{ trans('suppliers.modal.tab_address') }}</span>
                                        </a>
                                </li>
                                <li class="nav-item border-0">
                                    <a class="nav-link" id="bank-details-tab" data-bs-toggle="tab" href="#bank-details"
                                        role="tab">
                                            <span>{{ trans('suppliers.modal.tab_bank') }}</span>
                                        </a>
                                    </li>
                                </ul>
                        </div> --}}
                            <div class="tab-content mt-3" id="supplierTabContent">
                                <div class="tab-pane active" id="basic-info" role="tabpanel">
                                    <div class="row g-4 px-4">
                                    <div class="col-md-6 amg-form-field-row ">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.supplier_name') }}
                                                <span class="required">*</span>
                                            </label>
                                            <div class="d-flex select-div">
                                                <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                    </svg>
                                                </span>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.supplier_name_placeholder') }}"
                                                required>
                                            {!! $errors->first('name', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.business_category') }}</label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <select name="business_category[]" id="business_category"
                                                class="form-select" multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.contact_person') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" name="contact" id="contact" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.contact_person_placeholder') }}">
                                            {!! $errors->first('contact', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6 amg-form-field-row">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.phone') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" name="phone" id="phone" autocomplete="off"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.phone_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('phone', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>

                                    <div class="col-md-6 amg-form-field-row">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.email') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M17.5 3.75H2.5C2.33424 3.75 2.17527 3.81585 2.05806 3.93306C1.94085 4.05027 1.875 4.20924 1.875 4.375V15C1.875 15.3315 2.0067 15.6495 2.24112 15.8839C2.47554 16.1183 2.79348 16.25 3.125 16.25H16.875C17.2065 16.25 17.5245 16.1183 17.7589 15.8839C17.9933 15.6495 18.125 15.3315 18.125 15V4.375C18.125 4.20924 18.0592 4.05027 17.9419 3.93306C17.8247 3.81585 17.6658 3.75 17.5 3.75ZM15.893 5L10 10.4023L4.10703 5H15.893ZM16.875 15H3.125V5.79609L9.57734 11.7109C9.69265 11.8168 9.84348 11.8755 10 11.8755C10.1565 11.8755 10.3073 11.8168 10.4227 11.7109L16.875 5.79609V15Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" name="email" id="email" autocomplete="off"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.email_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('email', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>

                                    {{-- <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.pan') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" name="pan" id="pan" autocomplete="off"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.pan_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('pan', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div> --}}
                                </div>
                            </div>
                            {{-- <div class="tab-pane cp-bsd-view" id="address-details" role="tabpanel">
                                <div class="row g-4 px-4">
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.street1') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <i class="bi bi-signpost-2 mb-2"></i>
                                            </span>
                                            <input type="text" name="address" id="address" autocomplete="off"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.street1_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('address', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.street2') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <i class="bi bi-signpost-2 mb-2"></i>
                                            </span>
                                            <input type="text" name="address2" id="address2" autocomplete="off"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.street2_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('address2', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
        
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.country') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <i class="bi bi-globe-central-south-asia mb-2"></i>
                                            </span>
                                            <select name="country_id" id="country_id" class="form-control">
                                                <option value="">
                                                    {{ trans('header.location_fields.select_country') }}</option>
                                            </select>
                                        </div>
                                        {!! $errors->first('country_id', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
        
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.state') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <i class="bi bi-map-fill mb-2"></i>
                                            </span>
                                            <select name="state_id" id="state_id" class="form-control">
                                                <option value="">
                                                    {{ trans('header.location_fields.select_state') }}</option>
                                            </select>
                                        </div>
                                        {!! $errors->first('state_id', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
        
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.city') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <i class="bi bi-buildings mb-2"></i>
                                            </span>
                                            <select name="city_id" id="city_id" class="form-control">
                                                <option value="">
                                                    {{ trans('header.location_fields.select_city') }}</option>
                                            </select>
                                        </div>
                                        {!! $errors->first('city_id', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
        
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.zip') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <i class="bi bi-mailbox mb-2"></i>
                                            </span>
                                            <input type="text" name="zip" id="zip" autocomplete="off"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.zip_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('zip', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane cp-bsd-view" id="bank-details" role="tabpanel">
                                <div class="row g-4 px-4">
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.account_number') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" autocomplete="off" name="bank_acc_number"
                                                id="bank_acc_number" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.account_number_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('bank_acc_number', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
        
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.bank_name') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" autocomplete="off" name="bank_name" id="bank_name"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.bank_name_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('bank_name', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
        
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.ifsc_code') }}
                                        </label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" autocomplete="off" name="bank_ifsc" id="bank_ifsc"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.ifsc_code_placeholder') }}"
                                                value="">
                                        </div>
                                        {!! $errors->first('bank_ifsc', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
    
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.branch_name') }}
                                    </label>
                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <input type="text" autocomplete="off" name="bank_branch"
                                                id="bank_branch" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.branch_name_placeholder') }}"
                                                value="">
                                    </div>
                                    {!! $errors->first('bank_branch', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
    
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.account_name') }}
                                    </label>
                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                            </svg>
                                        </span>
                                            <input type="text" autocomplete="off" name="bank_acc_name"
                                                id="bank_acc_name" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.account_name_placeholder') }}"
                                                value="">
                                    </div>
                                    {!! $errors->first('bank_acc_name', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
    
                                    <div class="col-md-6">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.website_url') }}
                                    </label>
                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                            </svg>
                                        </span>
                                        <input type="text" autocomplete="off" name="url" id="url"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.website_url_placeholder') }}"
                                                value="">
                                    </div>
                                    {!! $errors->first('url', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
    
                                    <div class="col-md-12">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.notes') }}
                                    </label>
                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                            </svg>
                                        </span>
                                            <textarea name="notes" id="notes" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.notes_placeholder') }}"></textarea>
                                    </div>
                                    {!! $errors->first('notes', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
    
                                    <div id="preview-image" class="col-md-12" style="display:none;">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.preview_image') }}
                                    </label>
                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                            </svg>
                                        </span>
                                        <ul class="list-group" id="supplier-image-list"></ul>
                                    </div>
                                </div>
    
                                    <div class="col-md-12">
                                        <label class="form-label b1-text fw-bold mb-2">
                                            {{ trans('suppliers.modal.upload_image') }}
                                    </label>
                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                            </svg>
                                        </span>
                                            <input type="file" id="supplier-image-upload" name="image"
                                                class="form-control" multiple>
                                    </div>
                                    {!! $errors->first('image', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end mb-4 pb-4 py-0" style="padding-inline: 40px;">
                    <i class="bi bi-arrow-repeat d-none" id="img"></i>
                    <div class="d-flex gap-2">
                        <button type="submit" id="btnSubmit"
                            class="amg-btn amg-btn-primary amg-btn-block amg-btn-md mb-2">{{ trans('suppliers.modal.save') }}</button>
                        <button type="button" id="btnClose"
                            class="amg-btn amg-btn-secondary amg-btn-block amg-btn-md mb-2"
                            data-bs-dismiss="modal">{{ trans('suppliers.modal.cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
