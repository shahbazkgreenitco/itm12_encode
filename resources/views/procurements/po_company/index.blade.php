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
      "description": "Migrated from Laravel 8 / Bootstrap 3 legacy po_company module"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('left_nav.po_companies.list'))
@section('content')
<div id="main-po-company-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('content.po_companies.Po_company') }}</h3>
        <div class="d-flex gap-8">
            <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.po_companies.Filter') }}">
                <i class="bi bi-funnel"></i>
                <span class="b6-text opacity-50">{{ trans('content.po_companies.Filter') }}</span>
                <span class="filter-count-badge d-none">0</span>
            </button>
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-download-report" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.po_companies.Download_report') }}">
                <i class="bi bi-download"></i>
            </button>
            <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-po-company" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('content.po_companies.Add_po_company') }}">
                <i class="bi bi-plus-lg"></i>
                <span>{{ trans('content.po_companies.Add_po_company') }}</span>
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
                                <input type="text" class="amg-list-searchbar__input po-company-list-search"
                                       placeholder="{{ trans('content.user_fields.please_enter_valid_search') }}">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list" type="button">
                            <i class="bi bi-arrow-clockwise amg-refresh-btn__icon"></i>
                            <span>{{ trans('content.ticket_status.Refresh_List') }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="po_comapny" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><h4 class="b2-text">{{ trans('button.actions') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.po_companies.name') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.po_companies.logo') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.po_companies.country') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.po_companies.state') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.po_companies.city') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.po_companies.zipcode') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.po_companies.gstin') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_tax.created_at') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.custom_tax.updated_at') }}</h4></th>
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
                    <h5 class="modal-title">{{ trans('content.po_companies.Filter') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.po_companies.fileter_country') }}</label>
                                <select id="filter_country" name="countries" class="form-select">
                                    <option value="">{{ trans('content.service_ticket_fields.No_Filter') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.po_companies.fileter_state') }}</label>
                                <select id="filter_state" name="states" class="form-select"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.po_companies.fileter_city') }}</label>
                                <select id="filter_city" name="cities" class="form-select"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.filter_heading.filter_by_date') }}</label>
                                <select name="filter_by_date" id="filter_by_date" class="form-select">
                                    <option value="null">{{ trans('content.service_ticket_fields.No_Filter') }}</option>
                                    <option value="1">{{ trans('content.ticket_status.Created_Date') }}</option>
                                    <option value="2">{{ trans('content.ticket_status.Updated_Date') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans('content.filter_heading.filter_by_daterange') }}</label>
                                <div id="reportrange" class="form-control d-flex align-items-center justify-content-between" style="cursor:pointer;">
                                    <span><i class="bi bi-calendar3 me-1"></i><span id="reportrange-label"></span></span>
                                    <i class="bi bi-caret-down-fill"></i>
                                    <input type="hidden" name="daterange" id="daterange">
                                </div>
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

    @include('procurements.po_company.po_company_modal')
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
<style>
    .logo-thumb { width: 60px; height: 40px; object-fit: contain; }
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/procurements/po_company/index.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    {{-- EXACT route/URL values copied from the old file — do not rename or invent --}}
    config.url.add = "{{ url('procurements/AddPoCompanyByAjax') }}";
    config.url.getPoCompanyByAjax = "{{ url('procurements/getPoCompanyByAjax') }}";
    config.url.getPoCompanyByAjaxForEdit = "{{ url('procurements/getPoCompanyByAjaxForEdit') }}";
    config.url.edit = "{{ url('procurements/getPoCompany/edit') }}";
    config.url.update = "{{ url('procurements/getPoCompany/update') }}";
    config.url.delete = "{{ url('procurements/getPoCompany/delete') }}";
    config.url.poCompanyExport = "{{ url('procurements/getPoCompany/export') }}";
    config.url.path = "{{ asset('uploads/procurements') }}";
    config.url.city = "{{ url('fetchCityByAjax') }}";
    config.url.country = "{{ url('getCountryByQuery') }}";
    config.url.state = "{{ url('fetchStateByAjax') }}";
    config.permissions = {!! json_encode($permissionArray ?? [], true) !!};
    config.client = "{{ config('app.client') }}";
    config.token = "{{ csrf_token() }}";
    config.translations = {
        something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
        save: '{{ trans('button.save') }}',
        Search: '{{ trans('content.ticket_status.Search') }}',
        Refresh_List: '{{ trans('content.ticket_status.Refresh_List') }}',
        press_enter_with_Search: '{{ trans('content.user_fields.please_enter_valid_search') }}',
        are_you_delete: '{{ trans('content.po_companies.are_you_delete') }}',
        Filter: '{{ trans('content.po_companies.Filter') }}',
        Add_po_company: '{{ trans('content.po_companies.Add_po_company') }}',
        Edit_po_company: '{{ trans('content.po_companies.Edit_po_company') }}',
        Download_report: '{{ trans('content.po_companies.Download_report') }}',
        select_image: '{{ trans('content.procurement_fields.select_image') }}',
        Select_City: '{{ trans('content.procurement_fields.Select_City') }}',
        Select_State: '{{ trans('content.procurement_fields.Select_State') }}',
        Select_Country: '{{ trans('content.procurement_fields.Select_Country') }}',
        Please_select_country: '{{ trans('content.procurement_fields.Please_select_country') }}',
        Please_select_state: '{{ trans('content.procurement_fields.Please_select_state') }}',
        Please_select_country_and_state: '{{ trans('content.procurement_fields.Please_select_country_and_state') }}'
    };
    new POCompanyList(config);
</script>
@endpush
