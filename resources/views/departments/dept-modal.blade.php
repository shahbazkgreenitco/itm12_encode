{{-- * ------------------------------------------------------------
* File: dept-modal.blade.php
* Module: Department Module
* DEPT/26/02
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #002
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
<div class="amg-modal amg-form-modal modal fade" id="departmentmodal" tabindex="-1" aria-labelledby="vertical-center-modal" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="DepartmentForm" method="post" class="w-100 amg-form-theme" autocomplete="off" onsubmit="return false;">
            <div class="modal-content rounded-5">
                <!-- Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4 px-4">
                    <h3 class="modal-title s2-text fw-semibold px-2">
                        <span id="headingLabel">{{ trans('department.form_fields_and_buttons.add') }}</span> {{ trans('department.department') }}
                    </h3>

                    <button data-bs-dismiss="modal" class="modal-close px-2" aria-label="Close"  >
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#7F7F7F" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body bg-white">
                    <div class="container-fluid py-3">
                        <!-- Row 1: Company + Department Name -->
                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="company_name" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("department.form_fields_and_buttons.company_name") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <select name="company_id" id="company_name" class="form-select"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="name" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("department.form_fields_and_buttons.department_name") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="{{ trans("department.form_fields_and_buttons.department_name") }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Initial + Description -->
                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="department_tag" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.initial") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Aa</span>
                                        <input type="text" name="department_tag" id="department_tag" class="form-control" placeholder="{{ trans("department.placeholder_and_options.dept_initial") }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="description" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.description") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-textarea"></i></span>
                                        <textarea name="description" id="description" class="form-control" placeholder="{{ trans("department.form_fields_and_buttons.description") }}"></textarea>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Configuration Fieldset -->
                        <div class="row px-4 mt-4">
                            <div class="col-12">
                                <fieldset class="p-3 border rounded-4 ticketFieldset">
                                    <legend class="px-2 s2-text fw-semibold">{{ trans("department.form_fields_and_buttons.ticket_configuration") }}</legend>

                                    <!-- Row: Department Head + Attender -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="department_head_id" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.department_head") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                    <select name="department_head_id" id="department_head_id" class="form-select select2"></select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="attender_id" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.attender") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                                    <select name="attender_id" id="attender_id" class="form-select select2"></select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Row: Custom Fieldset -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="department_custom_fieldset" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.custom_fieldset") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-list"></i></span>
                                                    <select name="department_custom_fieldset" id="department_custom_fieldset" class="form-select select2"></select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Row: Compulsory PC + Compulsory SC -->
                                    <div class="row mb-4 pc_row">
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="pc_custom_fieldset" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.complusory_pc") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-list"></i></span>
                                                    <select name="pc_custom_fieldset" id="pc_custom_fieldset" class="form-select select2"></select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="sc_custom_fieldset" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.complusory_sc") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-list"></i></span>
                                                    <select name="sc_custom_fieldset" id="sc_custom_fieldset" class="form-select select2"></select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Row: Ticket Auto Creation + Department Admin -->
                                    <div class="row mb-4 tkt_auto_ceration">
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="tkt_auto_creation_id" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.tkt_auto_creation") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-list"></i></span>
                                                    <select name="tkt_auto_creation_id" id="tkt_auto_creation_id" class="form-control select2">
                                                        <option value="">{{ trans("department.placeholder_and_options.select_email") }}</option>                                                        
                                                        @foreach($outGoingEmails as $email)
                                                            <option value="{{ $email->id }}">{{ $email->ebts_username }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="department_admin" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.department_admin") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-list"></i></span>
                                                    <select name="department_admin" id="department_admin" class="form-control select2" multiple></select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <!-- Asset Configuration Fieldset -->
                        <div class="row px-4 mt-4">
                            <div class="col-12">
                                <fieldset class="p-3 border rounded-4 assetFieldset">
                                    <legend class="px-2 s2-text fw-semibold">{{ trans("department.form_fields_and_buttons.asset_configuration") }}</legend>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                                <label for="asset_department" class="form-label b1-text me-2 mb-0 text-end">{{ trans("department.form_fields_and_buttons.asset_department") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-box"></i></span>
                                                    <select name="asset_department" id="asset_department" class="form-select">
                                                        <option value="0">{{ trans("department.placeholder_and_options.no") }}</option>
                                                        <option value="1">{{ trans("department.placeholder_and_options.yes") }}</option>
                                                    </select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="amg-form-footer modal-footer px-4 pb-4">
                    <button type="button" id="btnClear" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">{{ trans("department.form_fields_and_buttons.close") }}</button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">{{ trans("department.form_fields_and_buttons.save") }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
