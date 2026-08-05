{{-- Modal: Add/Edit Po Company --}}
<div class="modal fade" id="po-company-mdl" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="po-company-mdl-frm" method="post" action="#" enctype="multipart/form-data" autocomplete="off">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="po-company-mdl-title">{{ trans('content.po_companies.Add_po_company') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="company" class="form-label">{{ trans('content.po_companies.name') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" autocomplete="off" id="company" name="company" class="form-control"
                                            placeholder="{{ trans('content.po_companies.name') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="logo" class="form-label">{{ trans('content.po_companies.logo') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-image"></i></span>
                                    <input type="file" id="logo" name="logo" class="form-control" accept="image/*"
                                            placeholder="{{ trans('content.po_companies.logo') }}" />
                                    <span class="input-group-text js-preview-logo" id="eye-button-visiable" style="cursor:pointer;" role="button">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="contact_no" class="form-label">Contact No</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" autocomplete="off" id="contact_no" name="contact_no" class="form-control" placeholder="Contact no" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="cin_number" class="form-label">CIN No</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" class="form-control" name="cin_number" id="cin_number" placeholder="CIN Number">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="amg-form-field">
                                <label for="country" class="form-label">{{ trans('content.po_companies.country') }} <span class="text-danger">*</span></label>
                                <select name="country" id="country" class="form-select">
                                    <option value="">Select country</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="amg-form-field">
                                <label for="state" class="form-label">{{ trans('content.po_companies.state') }} <span class="text-danger">*</span></label>
                                <select name="state" id="state" class="form-select"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="amg-form-field">
                                <label for="city" class="form-label">{{ trans('content.po_companies.city') }} <span class="text-danger">*</span></label>
                                <select name="city" id="city" class="form-select"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="zip" class="form-label">{{ trans('content.po_companies.zipcode') }} <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" id="zip" name="zip" class="form-control"
                                        placeholder="{{ trans('content.po_companies.zipcode') }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="gstin" class="form-label">{{ trans('content.po_companies.gstin') }} <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" id="gstin" name="gstin" class="form-control"
                                        placeholder="{{ trans('content.po_companies.gstin') }}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="email_id" class="form-label">Email</label>
                                <input type="text" autocomplete="off" id="email_id" name="email_id" class="form-control" placeholder="Email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label for="fax_id" class="form-label">Fax</label>
                                <input type="text" autocomplete="off" id="fax_id" name="fax_id" class="form-control" placeholder="Fax" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="address" class="form-label">{{ trans('content.po_companies.address') }} <span class="text-danger">*</span></label>
                                <textarea id="address" name="address" class="form-control" placeholder="{{ trans('content.po_companies.address') }}"></textarea>
                                <div id="shows_error" class="invalid-feedback d-block"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="terms_conditions" class="form-label">{{ trans('content.procurement_fields.Terms_Conditions') }}</label>
                                <textarea id="terms_conditions" name="terms_conditions" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="payment_terms" class="form-label">{{ trans('content.procurement_fields.Payment_Terms') }}</label>
                                <textarea id="payment_terms" name="payment_terms" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="notes" class="form-label">{{ trans('content.procurement_fields.Notes') }}</label>
                                <textarea id="notes" name="notes" class="form-control"></textarea>
                            </div>
                        </div>
                        @if(config('app.client') == 'knightfrank')
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="delivery_terms" class="form-label">Delivery Terms</label>
                                <textarea id="delivery_terms" name="delivery_terms" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="warranty_and_support" class="form-label">Warranty and Support</label>
                                <textarea id="warranty_and_support" name="warranty_and_support" class="form-control"></textarea>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="btnClear" data-bs-dismiss="modal">
                        {{ trans('button.close') }}
                    </button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">
                        {{ trans('content.user_fields.Save_changes') }}
                        <span class="spinner-border spinner-border-sm d-none" id="po-company-mdl-loader" role="status"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Image Preview --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="image-preview" src="#" alt="Image Preview" style="max-width: 100%; height: auto;" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
