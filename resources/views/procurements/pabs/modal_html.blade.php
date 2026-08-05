<div class="modal fade" id="addPABModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <form id="pabForm" name="pabForm" method="post" action="#" class="form-horizontal" onsubmit="return false;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPABModalLabel">{{ trans('content.procurement_fields.Add_New_PAB') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body no-pad">
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="name" class="form-label mandatory">{{ trans('content.procurement_fields.Name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-text-width"></i></span>
                                    <input type="text" autocomplete="off" name="name" id="name" class="form-control" placeholder="{{ trans('content.procurement_fields.Enter_PAB_Name') }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="description" class="form-label">{{ trans('content.procurement_fields.Description') }}</label>
                                <textarea name="description" id="description" class="form-control" placeholder="{{ trans('content.procurement_fields.Enter_Description_About_PAB') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="hierarchy_approval" class="form-label mandatory">{{ trans('content.procurement_fields.Approval_Mode') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-check"></i></span>
                                    <select id="hierarchy_approval" name="hierarchy_approval" class="form-select">
                                        <option value="" disabled selected>{{ trans('content.procurement_fields.Approval_Mode') }}</option>
                                        <option value="3">{{ trans('content.procurement_fields.Group_Approval') }}</option>
                                        <option value="1">{{ trans('content.procurement_fields.Level_By_Level') }}</option>
                                        <option value="2">{{ trans('content.procurement_fields.Minimum_Approval') }}</option>
                                        <option value="4">{{ trans('content.procurement_fields.Manager_Approval') }}</option>
                                        <option value="5">{{ trans('content.procurement_fields.Location_Approval') }}</option>
                                        <option value="9">{{ trans('content.procurement_fields.Budget_Approval') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row cover hide">
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label for="required_minimum_approvals" class="form-label mandatory">{{ trans('content.procurement_fields.Required_Minimum_Approvals') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-list"></i></span>
                                    <input type="number" name="required_minimum_approvals" id="required_minimum_approvals" class="form-control" placeholder="Enter the minimum approvals number" value="1" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <i class="fa fa-spinner fa-spin" id="img" style="display:none"></i>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">
                        {{ trans('button.save') }}
                        <span class="spinner-border spinner-border-sm d-none" id="pab-loader" role="status"></span>
                    </button>
                    <button type="button" id="btnClear" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ trans('button.close') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{!! CommonHelper::asset('js/procurement/pab/pab_modal.js') !!}"></script>
    <script>
        var config = {};
        config.url = {};
        config.url.save = "{{ url('procurements/pab/add') }}";
        config.token = "{{ csrf_token() }}";
        new PabModal(config);
    </script>
@endpush
