{{--
/**
------------------------------------------------------------
File: transfer_modal.blade.php
Module: Device
DEV/26/07
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #DEV-005
Created On: 2026-07-28
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}

<div id="transer-mdl" class="amg-modal amg-form-modal modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="transer-mdl-frm" class="w-100 amg-form-theme" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="device_ids" id="device_ids" value="">
            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">{{ trans('devices.transfer.transfer_devices') }}</h3>
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
                        <div class="row px-4">
                            <!-- Device List Summary -->
                            <div class="col-md-12 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('devices.transfer.view_bulk_device_list') }}</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <span>{{ trans('devices.transfer.total_devices_in_transfer') }}: <strong
                                                class="device_transfer--count">0</strong></span>
                                        <button type="button"
                                            class="btn-show-print-devices btn btn-sm btn-outline-secondary"
                                            data-toggle="tooltip"
                                            data-original-title="{{ trans('devices.transfer.view_bulk_device_list') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 384 512" fill="currentColor">
                                                <path
                                                    d="M96 64a48 48 0 1 1 0 96 48 48 0 1 1 0-96zm0 160a48 48 0 1 1 0 96 48 48 0 1 1 0-96zm192 160a48 48 0 1 1 0 96 48 48 0 1 1 0-96zM96 160c17.7 0 32 14.3 32 32v32c0 53 43 96 96 96v64c-53 0-96 43-96 96v0c0 17.7-14.3 32-32 32s-32-14.3-32-32v0c0-70.7 57.3-128 128-128v-64c-70.7 0-128-57.3-128-128v-32c0-17.7 14.3-32 32-32zm192 224c17.7 0 32 14.3 32 32v16c0 17.7-14.3 32-32 32s-32-14.3-32-32v-16c0-17.7 14.3-32 32-32z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer To (Location) -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label
                                        class="form-label me-2 mb-0 text-end required">{{trans('devices.transfer.transfer_to')
                                        }}</label>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <select name="transfer_to" id="transfer_to" class="form-select">
                                                <option value="">{{ trans('devices.transfer.select_transfer_location')
                                                    }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- Internal Place -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{
                                        trans('devices.transfer.internal_place') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo"></i></span>
                                        <select name="internal_place" id="internal_place" class="form-select">
                                            <option value="">{{ trans('devices.transfer.select_internal_place') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Responsible User -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end required">{{
                                        trans('devices.transfer.responsible_user') }}</label>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <select name="responsible_user" id="responsible_user" class="form-select">
                                                <option value="">{{ trans('devices.transfer.select_responsible_user') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- Expected Received Date -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end required">{{
                                        trans('devices.transfer.expected_received_date') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                        <input type="text" autocomplete="off" name="expected_received_date"
                                            id="expected_received_date" class="form-control datepicker"
                                            placeholder="{{ trans('devices.transfer.select_expected_received_date') }}" />
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- CC Users -->
                            <div class="col-md-6 mb-4">
                                <div class="amg-form-field amg-form-field-row">
                                    <label class="form-label me-2 mb-0 text-end">{{ trans('devices.transfer.cc_users')
                                        }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-people"></i></span>
                                        <select multiple name="cc_users[]" id="cc_users" class="form-select">
                                            <option value="">{{ trans('devices.transfer.select_cc_users') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12 mb-4">
                                <div class="amg-form-field d-flex align-items-start">
                                    <label class="form-label me-2 mb-0 text-end">{{ trans('devices.transfer.notes')
                                        }}</label>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                            <textarea name="notes" id="notes" class="form-control"
                                                placeholder="{{ trans('devices.transfer.enter_transfer_notes') }}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 pb-4">
                    <button type="button" id="btnSubmit1" class="amg-btn amg-btn-primary">{{
                        trans('devices.transfer.transfer_device') }}</button>
                    <button type="button" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">{{
                        trans('button.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>