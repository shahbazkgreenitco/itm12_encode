{{--
/**
* ------------------------------------------------------------
* File: modal_expense_html.blade.php
* Module: Devices
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-018
* Created On: 2026-14-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div id="dev-main-mdl" class="amg-modal amg-form-modal modal fade user-mdl-box relevant-ticket-mdl-box dev-main-mdl-box" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="dev-main-mdl-frm" name="devMainMdlForm" method="post" action="#" class="form-horizontal w-100" enctype="multipart/form-data" onsubmit="return false;">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="forAction" id="forAction" value="" />
            <input type="hidden" id="temp_id" name="temp_id" value="" />
            <input type="hidden" name="asset_id" value="{{ isset($device) ? $device->id : ''}}">

            <div class="modal-content rounded-5">
                
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4"></h3>
                    <button type="button" class="modal-close px-4 close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body no-side-pad">
                    <div class="container-fluid py-3 device_div">
                        <div class="row g-4 px-4">
                            <div class="col-md-6 amg-form-field-row form-group">
                                <label for="asset_id" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.device") }} <span class="required"></span>
                                </label>
                                <div class="input-group">
                                    <div id="asset_id"></div>
                                </div>
                            </div>
                            <div class="col-md-6 amg-form-field amg-form-field-row form-group">
                                <label for="expense_type" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.expense_type") }} <span class="required"></span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-chevron-down"></i>
                                    </span>
                                    <select name="expense_type" id="expense_type" class="form-select select2-hidden-accessible"></select>
                                </div>
                            </div>
                            <div class="col-md-6 amg-form-field amg-form-field-row form-group">
                                <label for="title" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.title") }} <span class="required"></span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off" name="title" id="title" class="form-control" placeholder="{{ trans('devices.device_info.modal.enter_title') }}" value="" />
                                    <div class="field-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6 amg-form-field amg-form-field-row form-group">
                                <label for="expense_date" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.expense_date") }} <span class="required"></span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25Z" fill="currentColor"></path></svg>
                                    </span>
                                    <input type="text" autocomplete="off" name="expense_date" id="expense_date" class="form-control" placeholder="{{ trans('devices.device_info.modal.choose_expense_date') }}" />
                                    <div class="field-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6 amg-form-field amg-form-field-row form-group">
                                <label for="cost" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.cost") }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                    <input type="text" autocomplete="off" name="cost" id="cost" class="form-control" placeholder="{{ trans('devices.device_info.modal.enter_service_cost') }}" value="0.00" />
                                </div>
                            </div>
                            <div class="col-md-6 amg-form-field amg-form-field amg-form-field-row form-group">
                                <label for="currency_format" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.currency_format") }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-cash-coin"></i>
                                    </span>
                                    <select name="currency_format" id="currency_format" class="form-select select2-hidden-accessible"></select>
                                </div>
                            </div>
                            <div class="col-md-6 amg-form-field amg-form-field-row form-group">
                                <label for="is_warranty" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.covered_under_warrenty") }}
                                </label>
                                <div class="d-flex align-items-center" style="height: 46px;">
                                    <label class="form-check-label d-flex align-items-center gap-2">
                                        <input type="checkbox" autocomplete="off" name="is_warranty" id="is_warranty" class="form-check-input mt-0" /> 
                                        {{ trans("devices.device_info.modal.yes") }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 amg-form-field amg-form-field-row form-group">
                                <label for="is_amc" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.covered_under_amc") }}
                                </label>
                                <div class="d-flex align-items-center" style="height: 46px;">
                                    <label class="form-check-label d-flex align-items-center gap-2">
                                        <input type="checkbox" autocomplete="off" name="is_amc" id="is_amc" class="form-check-input mt-0" /> 
                                        {{ trans("devices.device_info.modal.yes") }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12 amg-form-field amg-form-field-row form-group">
                                <label for="notes" class="form-label b1-text mb-2">
                                    {{ trans("devices.device_info.modal.notes") }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="black" />
                                        </svg>
                                    </span>
                                    <textarea name="notes" autocomplete="off" class="form-control textarea" id="notes" placeholder="{{ trans('devices.device_info.modal.enter_notes') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="amg-form-footer modal-footer justify-content-end mb-4 pb-4 py-0" style="padding-inline: 40px;">
                    <div class="d-flex gap-2">
                        <button type="button" id="btnClear" class="amg-btn amg-btn-secondary amg-dark-secondary-button amg-btn-block bg-black text-white amg-btn-md mb-2" data-bs-dismiss="modal" data-dismiss="modal">
                            {{ trans("devices.device_info.modal.close") }}
                        </button>
                        <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-block amg-btn-md mb-2">
                            {{ trans("devices.device_info.modal.save_changes") }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>