{{-- @page-meta
{
  "page_no": "",
  "file": "procurements/add.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "AI Migration",
      "from": "2026-07",
      "reviewer": null,
      "description": "Migrated from Laravel 8 / Bootstrap 3 legacy module"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans("content.procurement_fields.New_PAB"))
@section('content')
<div id="main-pab-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans("content.procurement_fields.Add_New_PAB") }}</h3>
        <div class="d-flex gap-8">
            <button type="button" class="header-icon-btn header-icon-btn-sm" data-bs-toggle="tooltip" title="{{ trans("content.procurement_fields.Back_to_PAB_List") }}">
                <i class="bi bi-arrow-left"></i>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0"><div class="card-body pt-3">
                <form name="pab" method="POST" id="addForm" action="{{ url('procurements/pab/add') }}">
                    @csrf
                    <div class="amg-form-field mb-3">
                        <label for="name" class="form-label mandatory">{{ trans("content.procurement_fields.Name") }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" placeholder="{{ trans('content.procurement_fields.Enter_PAB_Name') }}" id="name" class="form-control" @if(request()->name) value="{{ request()->name }}" @endif />
                        @if(isset($errors) && $errors->get('name'))
                            <div class="invalid-feedback d-block">{{ $errors->get('name')[0] }}</div>
                        @endif
                    </div>
                    <div class="amg-form-field mb-3">
                        <label for="description" class="form-label mandatory">{{ trans("content.procurement_fields.Description") }} <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" placeholder="{{ trans('content.procurement_fields.Enter_Description_About_PAB') }}">@if(request()->description){{ request()->description }}@endif</textarea>
                        @if(isset($errors) && $errors->get('description'))
                            <div class="invalid-feedback d-block">{{ $errors->get('description')[0] }}</div>
                        @endif
                    </div>
                    <div class="amg-form-field mb-3">
                        <label for="hierarchy_approval" class="form-label mandatory">{{ trans("content.procurement_fields.Approval_Mode") }} <span class="text-danger">*</span></label>
                        <select name="hierarchy_approval" id="hierarchy_approval" class="form-select">
                            <option value="3">{{ trans("content.procurement_fields.Group_Approval") }}</option>
                            <option value="1">{{ trans("content.procurement_fields.Level_By_Level") }}</option>
                            <option value="2">{{ trans("content.procurement_fields.Minimum_Approval") }}</option>
                            <option value="4">{{ trans("content.procurement_fields.Manager_Approval") }}</option>
                            <option value="5">{{ trans("content.procurement_fields.Location_Approval") }}</option>
                            <option value="9">{{ trans("content.procurement_fields.Budget_Approval") }}</option>
                        </select>
                        @if(isset($errors) && $errors->get('hierarchy_approval'))
                            <div class="invalid-feedback d-block">{{ $errors->get('hierarchy_approval')[0] }}</div>
                        @endif
                    </div>
                    <div class="amg-form-field mb-3">
                        <label for="required_minimum_approvals" class="form-label mandatory">{{ trans("content.procurement_fields.Required_Minimum_Approvals") }} <span class="text-danger">*</span></label>
                        <input type="number" name="required_minimum_approvals" id="required_minimum_approvals" placeholder="Enter the minimum approvals number" class="form-control" value="{{ request()->required_minimum_approvals ? request()->required_minimum_approvals : 1 }}" />
                        @if(isset($errors) && $errors->get('required_minimum_approvals'))
                            <div class="invalid-feedback d-block">{{ $errors->get('required_minimum_approvals')[0] }}</div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="cancel">{{ trans("button.cancel") }}</button>
                        <button type="submit" class="amg-btn amg-btn-primary" id="btnSubmit">{{ trans("button.add") }}</button>
                    </div>
                </form>
            </div></div>
        </div>
    </main>
</div>
@endsection
@push('css')
<style>
    .mandatory {
        color: var(--app-table-heading);
    }
</style>
@endpush
@push('scripts')
<script src="{!! CommonHelper::asset('js/procurements/add.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    config.url.add = "{{ route('procurements.pab.add') }}";
    config.token = "{{ csrf_token() }}";
    new PabAdd(config);
</script>
@endpush
