{{--
/**
* ------------------------------------------------------------
* File: basic_info_html.blade.php
* Module: Suppliers
* SUP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #SUP-006
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Changes for the validation error message
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div id="courier_info_modal" class="amg-modal modal fade user-mdl-box device-mdl-box" tabindex="-1" role="dialog"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="courier_form" name="courier_form" method="post" action="#" class="form-horizontal w-100"
            onsubmit="return false;">
            @csrf
            <div class="modal-content rounded-5">
                <div class="modal-header">
                    <h3 id="courier_title" class="modal-title px-4">
                        {{ trans('suppliers.modal.courier_title') }}
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
                <div class="modal-body no-side-pad back-gray py-0">
                    <div id="courierTabs" class="nav-tabs-custom tab-base">
                        <div class="tab-bar">
                            <ul class="nav nav-underline no-border-top pt-3" role="tablist">
                                <li class="nav-item border-0">
                                    <a class="nav-link active" id="courier-basic-detail-tab" data-bs-toggle="tab"
                                        href="#courier-mdl-basic-detail" role="tab">
                                        <span>{{ trans('suppliers.modal.courier_tab_basic_details') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item border-0 cp-bsd-view">
                                    <a class="nav-link" id="courier-insurance-tab" data-bs-toggle="tab"
                                        href="#courier-mdl-insurance-tab" role="tab">
                                        <span>{{ trans('suppliers.modal.courier_tab_insurance') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item border-0 cp-bsd-view">
                                    <a class="nav-link" id="courier-taxation-tab" data-bs-toggle="tab"
                                        href="#courier-mdl-taxation-tab" role="tab">
                                        <span>{{ trans('suppliers.modal.courier_tab_taxation') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content mt-3 py-4">
                            <div class="tab-pane fade show active" id="courier-mdl-basic-detail" role="tabpanel">
                                <div class="row g-4 px-4">
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="base_charge"
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.courier_basic_charge') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span>
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <input type="number" name="base_charge" id="base_charge"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.courier_basic_charge_placeholder') }}"
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="rate_per_gram"
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.courier_rate_per_gram') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span>
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <input type="number" name="rate_per_gram" id="rate_per_gram"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.courier_rate_per_gram_placeholder') }}"
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="enable_volumetric_charge"
                                                class="form-label b1-text fw-bold mb-0">{{ trans('suppliers.modal.courier_enable_volumetric_charge') }}</label>
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="enable_volumetric_charge" value="0" />
                                                <input class="form-check-input" type="checkbox"
                                                    name="enable_volumetric_charge" id="enable_volumetric_charge"
                                                    value="1" role="switch">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row" id="volumetric_divisor_group">
                                        <label for="volumetric_divisor"
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.courier_volumetric_divisor') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span>
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <input type="number" name="volumetric_divisor" id="volumetric_divisor"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.courier_volumetric_divisor_placeholder') }}"
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="fragile_liquid_charge"
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.courier_fragile_liquid_charge') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span>
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <input type="number" name="fragile_liquid_charge"
                                                id="fragile_liquid_charge" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.courier_fragile_liquid_charge_placeholder') }}"
                                                min="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="courier-mdl-insurance-tab" role="tabpanel">
                                <div class="row g-4 px-4">
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="insurance_rate"
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.courier_insurance_rate') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span>
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <input type="number" name="insurance_rate" id="insurance_rate"
                                                class="form-control"
                                                placeholder="{{ trans('suppliers.modal.courier_insurance_rate_placeholder') }}"
                                                min="0" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="insurance_handling_charges"
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.courier_insurance_handling_charges') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span>
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <input type="number" name="insurance_handling_charges"
                                                id="insurance_handling_charges" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.courier_insurance_handling_charges_placeholder') }}"
                                                min="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="courier-mdl-taxation-tab" role="tabpanel">
                                <div class="row g-4 px-4">
                                    <div class="col-md-12 amg-form-field-row">
                                        <label for="tax_id"
                                            class="form-label b1-text fw-bold mb-2">{{ trans('suppliers.modal.courier_tax_name') }}</label>
                                        <div class="d-flex select-div">
                                            <span class="input-group-text">
                                                <span>
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="black" />
                                                    </svg>
                                                </span>
                                            </span>
                                            <select name="tax_id" id="tax_id" class="form-control"
                                                placeholder="{{ trans('suppliers.modal.courier_tax_name_placeholder') }}"></select>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-12 amg-form-field-row customOptionsHolders taxNameDef row px-0 mx-0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end mb-4 pb-4 py-0" style="padding-inline: 40px;">
                    <i class="fa fa-spinner fa-spin me-2" id="loader_img" style="display:none"></i>
                    <div class="d-flex gap-2">
                        <button type="button" id="courierBtnSubmit"
                            class="amg-btn amg-btn-primary amg-btn-block amg-btn-md mb-2">
                            {{ trans('suppliers.modal.save') }}
                        </button>
                        <button type="button" id="btnClear" data-bs-dismiss="modal"
                            class="amg-btn amg-btn-secondary amg-btn-block amg-btn-md mb-2">
                            {{ trans('suppliers.modal.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
