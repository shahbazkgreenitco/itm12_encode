{{-- * ------------------------------------------------------------
* File: add-customfield-modal.blade.php
* Module: Custom Fields Module
* CUSTF/26/03
* ------------------------------------------------------------
* Version: 1.0.1
* Author: Hrishikesh Pandey 
* Page ID: #003
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Aligned UI Point (Hrishikesh Pandey)
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
<div class="amg-modal amg-form-modal modal fade" id="custom-field-mdl" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="CustomFieldForm" class="w-100 amg-form-theme">
            @csrf
            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">
                        {{ trans("header.custom_fields.add_custom_field") }}
                    </h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body bg-white">
                    <div class="container-fluid py-3">
                        <input type="hidden" id="_field_id" name="_field_id" value="">
                        <!-- ROW: Field Name + Form Element -->
                        <div class="row mb-4 px-4">
                            <div class="col-12 col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end required" style="min-width: 120px;">Field Name</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-type"></i></span>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Field Name">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end required" style="min-width: 120px;">Form Element</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-ui-checks"></i></span>
                                        <select name="element" id="fieldType" class="form-select">
                                            <option value="">Select</option>
                                            <option value="text">Text</option>
                                            <option value="date">Date</option>
                                            <option value="time">Time</option>
                                            <option value="datetime">DateTime</option>
                                            <option value="dropdown">Dropdown</option>
                                            <option value="radio">Radio</option>
                                            <option value="checkbox">Checkbox</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <!-- OPTION TYPE (for dropdown/radio/checkbox) -->
                        <div class="row mb-4 px-4 optionTypeDiv d-none">
                            <div class="col-12 col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end required" style="min-width: 120px;">Option Type</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-list"></i></span>
                                        <select name="option_type" id="optionType" class="form-select">
                                            <option value="">Select</option>
                                            <option value="1">Predefined</option>
                                            <option value="2">Custom</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 predefinedOptioDiv d-none">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end required" style="min-width: 120px;">Predefined Options</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-collection"></i></span>
                                        <select name="preDefinedOptions" id="preDefinedOptions" class="form-select">
                                            <option value="">Select</option>
                                            <option value="1">Locations</option>
                                            <option value="2">Users</option>
                                            <option value="3">Devices</option>
                                            <option value="4">Internal Places</option>
                                            <option value="5">Manufacturers</option>
                                            <option value="6">Models</option>
                                            <option value="7">Component</option>
                                            <option value="8">Tickets</option>
                                            <option value="9">Service Request</option>
                                            <option value="10">Change Management</option>
                                            <option value="11">Tasks</option>
                                            <option value="12">Licence</option>
                                            <option value="13">Projects</option>
                                            <option value="14">Purchase</option>
                                            <option value="15">Supplier</option>
                                            <option value="16">Contract</option>
                                            <option value="17">ESMS Software List</option>
                                            <option value="18">Department</option>
                                            <option value="19">Courier Provider</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 customOptionDiv d-none">
                                <div class="amg-form-field amg-form-field-row">
                                    <div class="d-flex align-items-center gap-3 mb-3 ps-4">
                                        <label class="form-label b1-text mb-0">Custom Options</label>
                                        <button type="button" class="btn btn-sm btn-primary addOptionDiv">
                                            <i class="bi bi-plus"></i> Add Option
                                        </button>
                                    </div>
                                    <!-- Container for dynamic option rows -->
                                    <div class="customOptionsHolders ps-4"></div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <!-- FORMAT ROW (two columns: Format + Field Type) -->
                        <div class="row mb-4 px-4 formatdiv">
                            <div class="col-12 col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end" style="min-width: 120px;">Format</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-file-earmark"></i></span>
                                        <select name="format" id="format" class="form-select">
                                            <option value="">Select the Format</option>
                                            <option value="ANY">ANY</option>
                                            <option value="ALPHA">ALPHA</option>
                                            <option value="NUMERIC">NUMERIC</option>
                                            <option value="DATE">Date</option>
                                            <option value="TIME">Time</option>
                                            <option value="DATETIME">DateTime</option>
                                            <option value="MAC">MAC</option>
                                            <option value="IP">IP</option>
                                            <option value="custom">Custom Format...</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end required" style="min-width: 120px;">Field Type</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                        <select name="custom_field_types" id="custom_field_types" class="form-select">
                                            <option value="">Select</option>
                                            @foreach($custom_field_types as $dep)
                                                <option value="{{ $dep['id'] }}">{{ $dep['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <!-- CUSTOM FORMAT INPUT (hidden by default) -->
                        <div class="row mb-4 px-4 custom_format--div d-none">
                            <div class="col-12 col-md-6">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end" style="min-width: 120px;">Custom Format</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-book"></i></span>
                                        <input type="text" name="custom_format" id="custom_format" class="form-control" placeholder="Custom Format">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <!-- HELP NOTE -->
                        <div class="row mb-4 px-4">
                            <div class="col-12">
                                <div class="amg-form-field amg-form-field-row d-flex flex-column flex-md-row align-items-md-center">
                                    <label class="form-label b1-text mb-2 mb-md-0 me-md-2 text-md-end" style="min-width: 120px;">Help Note</label>
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                        <textarea name="help_note" id="help_note" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 pb-4">
                    <button type="button" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">{{ trans("button.cancel") }}</button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">{{ trans("button.save") }}</button>
                </div>

            </div>
        </form>
    </div>
</div>