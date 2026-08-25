{{--
/**
------------------------------------------------------------
File: modal_custom_tax.blade.php
Module: Custom Tax
CTX/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #CTX-002
Created On: 2026-04-30
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
[1.0.1] - Updated modal structure and styling
------------------------------------------------------------
------------------------------------------------------------
Version: 1.0.2
Author: Muzaffar shaikh
Page ID: #CTX-002
Created On: 2026-07
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.2] - Fixed Add button size.
[1.0.2] - Fixed Name label and removed extra required (*).
[1.0.2] - Updated form label font size and weight.
[1.0.2] - Reduced modal side padding.
[1.0.2] - Fixed modal header font size.
[1.0.2] - Updated modal close button style.
[1.0.2] - Fixed Dynamic Tax field size.
[1.0.2] - Updated Remove button spacing and color.
[1.0.2] - Added missing modal footer border.
[1.0.2] - Updated modal footer button positions.
[1.0.2] - Changed Cancel button to secondary style.
[1.0.2] - Reduced modal footer spacing.
[1.0.2] - Fixed dark mode for Custom Tax Add/Edit forms.
------------------------------------------------------------
*/
--}}

<div id="custom_tax_mdl" class="amg-modal modal fade user-mdl-box" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <form id="custom_tax_form" class="amg-form-theme w-100" autocomplete="off">
            {{ csrf_field() }}
            <div class="modal-content rounded-5">
                <!-- HEADER -->
                 @include('common.modal_header', ['title' => trans("custom_tax.tax.add_tax")])
                <!-- BODY -->
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">
                            <!-- NAME -->
                            <div class="col-md-12 amg-form-field amg-form-field-row d-block">
                                <label class="form-label b1-text fw-bold mb-2 required">
                                    {{ trans('custom_tax.form.name') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tag"></i>
                                    </span>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="{{ trans('custom_tax.form.name') }}">
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12 amg-form-field amg-form-field-row d-block">
                                <label class="form-label b1-text fw-bold mb-1">
                                    {{ trans('custom_tax.form.tax_elements') }}
                                </label>
                                <div class="mb-2">
                                    <button type="button"
                                        class="amg-btn amg-btn-primary amg-btn-sm addOptionDiv d-inline-flex align-items-center gap-1">
                                        <svg style="width: 14px;height: 14px;" viewBox="0 0 19 19" fill="none">
                                            <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor"></path>
                                        </svg> {{ trans('custom_tax.buttons.add_options') }}
                                    </button>
                                </div>
                                <div class="customOptionsHolders"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FOOTER -->
                <div class="amg-form-footer modal-footer justify-content-end py-4 px-4">
                    <button type="button" class="amg-btn amg-btn-secondary amg-btn-md" data-bs-dismiss="modal">
                        {{ trans('custom_tax.buttons.cancel') }}
                    </button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-md">
                        {{ trans('custom_tax.buttons.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>