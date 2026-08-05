{{-- @page-meta
{
  "page_no": "",
  "file": "procurements/edit.blade.php",
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

@section('title', trans("content.procurement_fields.Edit_PAB"))

@section('content')
<div id="main-pab-edit-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans("content.procurement_fields.Edit_PAB") }} - {{ $pab->name }}</h3>
        <div class="d-flex gap-8">
            <button type="button" class="header-icon-btn header-icon-btn-sm" data-bs-toggle="tooltip" title="{{ trans("content.procurement_fields.Back_to_PAB_List") }}">
                <i class="bi bi-arrow-left"></i>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0"><div class="card-body pt-3">
                <form name="cab" method="POST" id="addForm" action="{{ url('procurements/pab/edit/'.$pab->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="amg-form-field mb-3">
                        <label for="name" class="form-label mandatory">{{ trans("content.procurement_fields.Name") }}</label>
                        <input type="text" name="name" placeholder="{{ trans('content.procurement_fields.Enter_PAB_Name') }}" id="name" class="form-control" value="{{ $pab->name }}" />
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="amg-form-field mb-3">
                        <label for="description" class="form-label mandatory">{{ trans("content.procurement_fields.Description") }}</label>
                        <textarea name="description" id="description" class="form-control" placeholder="{{ trans('content.procurement_fields.Enter_Description_About_PAB') }}">{{ $pab->description }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="amg-form-field mb-3">
                        <label for="hierarchy_approval" class="form-label mandatory">{{ trans("content.procurement_fields.Approval_Mode") }}</label>
                        <select name="hierarchy_approval" id="hierarchy_approval" class="form-select">
                            <option value="3" @if($pab->hierarchy_approval == 3) selected @endif >{{ trans("content.procurement_fields.Group_Approval") }}</option>
                            <option value="1" @if($pab->hierarchy_approval == 1) selected @endif >{{ trans("content.procurement_fields.Level_By_Level") }}</option>
                            <option value="2" @if($pab->hierarchy_approval == 2) selected @endif >{{ trans("content.procurement_fields.Minimum_Approval") }}</option>
                            <option value="4" @if($pab->hierarchy_approval == 4) selected @endif >{{ trans("content.procurement_fields.Manager_Approval") }}</option>
                            <option value="5" @if($pab->hierarchy_approval == 5) selected @endif >{{ trans("content.procurement_fields.Location_Approval") }}</option>
                            <option value="9" @if($pab->hierarchy_approval == 9) selected @endif >{{ trans("content.procurement_fields.Budget_Approval") }}</option>
                        </select>
                        @error('hierarchy_approval') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="amg-form-field mb-3 cover @if($pab->hierarchy_approval != 2) hide @endif">
                        <label for="required_minimum_approvals" class="form-label mandatory">{{ trans("content.procurement_fields.Required_Minimum_Approvals") }}</label>
                        <input type="number" name="required_minimum_approvals" id="required_minimum_approvals" placeholder="Enter the minimum approvals number" class="form-control" value="{{ $pab->required_minimum_approvals ? $pab->required_minimum_approvals : 1 }}" />
                        @error('required_minimum_approvals') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="cancel">{{ trans("button.cancel") }}</button>
                        <button type="submit" class="amg-btn amg-btn-primary" id="btnSubmit">{{ trans("button.update") }}</button>
                    </div>
                </form>
            </div></div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
    <script src="{!! CommonHelper::asset('js/procurements/edit.js') !!}"></script>
    <script>
        var config = {};
        config.url = {};
        config.token = "{{ csrf_token() }}";
        config.backUrl = "{{ url('procurements/pab/list') }}";
        new PabEdit(config);
    </script>
@endpush
