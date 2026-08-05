{{-- @page-meta
{
  "page_no": "",
  "file": "index.blade.php",
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
@section('title', trans("content.custom_charges.custom_charges"))
@section('content')
<div id="main-charges-element-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans("content.custom_charges.custom_charges") }}</h3>
        <div class="d-flex gap-8">
            <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-charges-element" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.custom_charges.add') }}">
                <i class="bi bi-plus-lg"></i>
                <span>{{ trans('content.custom_charges.add') }}</span>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <i class="bi bi-search amg-list-searchbar__icon search-icon"></i>
                                <input type="text" class="amg-list-searchbar__input charges-element-list-search"
                                       placeholder="{{ trans('content.custom_charges.press_enter_with_Search') }}">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" type="button">
                            <i class="bi bi-arrow-clockwise amg-refresh-btn__icon"></i>
                            <span>{{ trans('content.custom_charges.Refresh_List') }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="chargesTable" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><h4 class="b2-text">{{ trans('button.actions') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_charges.title') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_charges.description') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_charges.element_action') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_charges.status') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_charges.created_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_charges.created_at') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_charges.updated_at') }}</h4></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Advance filter modal — FIXED id per project convention, do not rename --}}
    <div class="modal fade" id="advanceFilterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('content.custom_charges.Filter') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.custom_charges.select_the_element_action') }}</label>
                                <select id="filter_element_action" name="element_actions" class="form-select">
                                    <option value="">{{ trans('content.service_ticket_fields.No_Filter') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.custom_charges.Select_the_status') }}</label>
                                <select id="filter_status" name="statuses" class="form-select">
                                    <option value="">{{ trans('content.service_ticket_fields.No_Filter') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-clear-filter" id="btnClrFilter">
                        {{ trans('content.filter_heading.Clear') }}
                    </button>
                    <button type="button" class="amg-btn amg-btn-primary btn-filter">
                        {{ trans('button.filter') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('procurements.charges_element.add_modal')
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
@endpush

@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/procurements/charges_element.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    config.url.ajaxlistCustomCharge = "{{ url('procurements/ajaxlistCustomCharge') }}";
    config.url.create_charges = "{{ url('procurements/create-charges') }}";
    config.url.edit_charge = "{{ url('procurements/edit-charges') }}";
    config.url.update_charges = "{{ url('procurements/update-charges') }}";
    config.url.charge_delete = "{{ url('procurements/charge_delete') }}";
    config.url.charge_history  = "{{ url('procurements/charge_history') }}";
    config.token = "{{ csrf_token() }}";
    config.permissions = {!! json_encode($permissionArray) !!};
    config.translations = {
        edit:'{{ trans('content.custom_charges.edit') }}',
        delete:'{{ trans('content.custom_charges.delete') }}',
        press_enter_with_Search:'{{ trans('content.custom_charges.press_enter_with_Search') }}',
        search:'{{ trans('content.custom_charges.search') }}',
        add_new:'{{ trans('content.custom_charges.add') }}',
        confirm:'{{ trans('content.custom_charges.confirm') }}',
        wrong:'{{ trans('content.custom_charges.wrong') }}',
        create_status:'{{ trans('content.custom_charges.create_status') }}',
        refresh:'{{ trans('content.custom_charges.refresh') }}',
        refresh_list:'{{ trans('content.custom_charges.Refresh_List') }}',
        clone:'{{ trans('content.custom_charges.clone') }}',
        history:'{{ trans('content.custom_charges.history') }}',
        select_the_element_action:'{{ trans('content.custom_charges.select_the_element_action') }}',
        Select_the_status:'{{ trans('content.custom_charges.Select_the_status') }}',
        enter_desc:'{{ trans('content.custom_charges.enter_desc') }}',
        Filter: '{{ trans('content.custom_charges.Filter') }}',
        Add: '{{ trans('content.custom_charges.add') }}',
        Title: '{{ trans('content.custom_charges.title') }}',
        Description: '{{ trans('content.custom_charges.description') }}',
        Element_Action: '{{ trans('content.custom_charges.element_action') }}',
        Status: '{{ trans('content.custom_charges.status') }}',
        Created_By: '{{ trans('content.custom_charges.created_by') }}',
        Created_At: '{{ trans('content.custom_charges.created_at') }}',
        Updated_At: '{{ trans('content.custom_charges.updated_at') }}',
        Something_Went_Wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
        Are_You_Delete: '{{ trans('content.custom_charges.are_you_delete') }}',
        Please_Select_Status: '{{ trans('content.custom_charges.Please_select_status') }}',
        Please_Select_Action: '{{ trans('content.custom_charges.Please_select_action') }}',
        Please_Select_Action_And_Status: '{{ trans('content.custom_charges.Please_select_action_and_status') }}'
    };
    new ChargesElement(config);
</script>
@endpush
