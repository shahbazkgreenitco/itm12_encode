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
@section('title', trans("content.procurement_fields.Procurement_User_Privileges"))
@section('content')
<div id="main-privileges-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans("content.procurement_fields.User_Privileges") }}</h3>
        <div class="d-flex gap-8">
            <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.procurement_fields.Filter') }}">
                <i class="bi bi-funnel"></i>
                <span class="b6-text opacity-50">{{ trans('content.procurement_fields.Filter') }}</span>
                <span class="filter-count-badge d-none">0</span>
            </button>
            <button class="amg-btn amg-btn-primary amg-btn-sm btn-update-privileges" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.procurement_fields.Update') }}">
                <i class="bi bi-check-lg"></i>
                <span>{{ trans('content.procurement_fields.Update') }}</span>
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
                                <input type="text" class="amg-list-searchbar__input privileges-list-search"
                                       placeholder="{{ trans('content.user_fields.please_enter_valid_search') }}">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" type="button">
                            <i class="bi bi-arrow-clockwise amg-refresh-btn__icon"></i>
                            <span>{{ trans('content.procurement_fields.Refresh_List') }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="privileges_table" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><h4 class="b2-text">{{ trans("content.procurement_fields.Full_Name") }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans("content.procurement_fields.Username") }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans("content.procurement_fields.Department") }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans("content.procurement_fields.Email") }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans("content.procurement_fields.Procurement_Team") }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans("content.procurement_fields.Procurement_User") }}</h4></th>
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
                    <h5 class="modal-title">{{ trans('content.procurement_fields.Filter') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans("content.procurement_fields.Privileges_For_Selected_User") }}</label>
                                <select id="filter_user_privilege" name="user_privilege" class="form-select">
                                    <option value="">{{ trans('content.service_ticket_fields.No_Filter') }}</option>
                                    <option value="1">{{ trans("content.procurement_fields.Procurement_User") }}</option>
                                    <option value="1">{{ trans("content.procurement_fields.Procurement_Team") }}</option>
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
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<style>
    .privileges-table-checkbox { width: 18px; height: 18px; cursor: pointer; }
</style>
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/procurement/user_privileges/index.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    config.url.list = "{{ url('procurements/users-privileges/ajax-users') }}";
    config.url.update = "{{ url('procurements/users-privileges/ajax-update') }}";
    config.translations = {
        something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
        serach_option: '{{ trans('content.procurement_fields.serach_option') }}',
        Refresh_List: '{{ trans('content.procurement_fields.Refresh_List') }}',
        search: '{{ trans('content.procurement_fields.search') }}',
        Update: '{{ trans('content.procurement_fields.Update') }}',
        Privileges_For_Selected_User: '{{ trans("content.procurement_fields.Privileges_For_Selected_User") }}',
        Procurement_User: '{{ trans("content.procurement_fields.Procurement_User") }}',
        Procurement_Team: '{{ trans("content.procurement_fields.Procurement_Team") }}',
        Full_Name: '{{ trans("content.procurement_fields.Full_Name") }}',
        Username: '{{ trans("content.procurement_fields.Username") }}',
        Department: '{{ trans("content.procurement_fields.Department") }}',
        Email: '{{ trans("content.procurement_fields.Email") }}',
        Filter: '{{ trans('content.procurement_fields.Filter') }}'
    };
    new PrivilegesList(config);
</script>
@endpush
