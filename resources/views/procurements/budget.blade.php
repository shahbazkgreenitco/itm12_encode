{{-- @page-meta
{
  "page_no": "",
  "file": "procurements/budgets.blade.php",
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
@section('title', trans('content.procurement_fields.Budgets'))
@section('content')
<div id="main-budget-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('content.procurement_fields.Budgets') }}</h3>
        <div class="d-flex gap-8">
            {{-- Toolbar icon button with label --}}
            <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip" title="{{ trans('content.user_fields.Filter') }}">
                <i class="bi bi-funnel"></i>
                <span class="b6-text opacity-50">{{ trans('content.user_fields.Filter') }}</span>
                <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
            </button>
            {{-- Toolbar icon-only button --}}
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-download-excel" type="button" data-bs-toggle="tooltip" title="{{ trans('content.procurement_fields.download_excel') }}">
                <i class="bi bi-file-earmark-excel"></i>
            </button>
            {{-- Primary action button with inline SVG icon --}}
            @if(Auth::user()->hasPermissionTo('BudgetAdd'))
                <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-budget" type="button" data-bs-toggle="tooltip" title="{{ trans('content.procurement_fields.create_budgets') }}">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none"><path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" /></svg>
                    <span>{{ trans('content.procurement_fields.create_budgets') }}</span>
                </button>
            @endif
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    {{-- List controls row: page length | search | refresh --}}
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown budget-list-page-length">
                                <option value="10" selected>Show (10)</option>
                                <option value="25">Show (25)</option>
                                <option value="50">Show (50)</option>
                                <option value="100">Show (100)</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>
                        <div>
                            <div class="amg-list-searchbar">
                                <i class="bi bi-search amg-list-searchbar__icon search-icon"></i>
                                <input type="text" class="amg-list-searchbar__input budget-list-search" placeholder="{{ trans('content.procurement_fields.serach_option') }}">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list">
                            <i class="bi bi-arrow-clockwise amg-refresh-btn__icon"></i>
                            <span>{{ trans('content.procurement_fields.Refresh_List') }}</span>
                        </button>
                    </div>
                    {{-- Server-side DataTable: thead only, tbody rendered by JS --}}
                    <div class="js-budget-list-view-panel">
                        <div class="table-responsive">
                            <table id="mytable" class="table display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><input class="form-check-input" type="checkbox" value="" id="select-all"></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Action") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Category") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Account_Type") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Financial_Year") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Department") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Amount") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Account_Code") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Utilized_Budget") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.In_Progress") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Remaining_Budget") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.custom_tax.created_at") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.custom_tax.updated_at") }}</h4></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('procurements.modal_html')
    @include('procurements.budget-filter-modal')
    <input type="hidden" id="auth_user" value="{{ json_encode(Auth::check() ? Auth::user() : null) }}">
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
<style>
    .dataTables_scrollBody { width: unset; }
</style>
@endpush

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/procurements/budgets.js') !!}"></script>
    <script>
        var config = {};
        config.url = {};
        config.url.list = "{{ route('procurements.budgets.list') }}";
        config.url.add = "{{ route('procurements.budgets.ajax-add') }}";
        config.url.edit = "{{ route('procurements.budgets.ajax-edit') }}";
        config.url.get = "{{ route('procurements.budgets.ajax-get') }}";
        config.url.delete = "{{ route('procurements.budgets.ajax-delete') }}";
        config.url.account_type = "{{ route('procurements.budgets.account-type') }}";
        config.url.financial_year = "{{ route('procurements.budgets.financial-year') }}";
        config.url.getDepartmentsByQuery = "{{ route('getDepartmentsByQuery') }}";
        config.url.history = "{{ route('procurements.budgets.history') }}";
        config.token = "{{ csrf_token() }}";
        config.translations={
            something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
            serach_option: '{{ trans('content.procurement_fields.serach_option') }}',
            Edit_Budget: '{{ trans('content.procurement_fields.Edit_Budget') }}',
            Delete_Budget: '{{ trans('content.procurement_fields.Delete_Budget') }}',
            Add_Budget: '{{ trans('content.procurement_fields.Add_Budget') }}',
            are_you_Delete: '{{ trans('content.procurement_fields.are_you_Delete') }}',
            Select_Account_type: '{{ trans('content.procurement_fields.Select_Account_type') }}',
            Select_Financial_Year: '{{ trans('content.procurement_fields.Select_Financial_Year') }}',
            Select_Department: '{{ trans('content.procurement_fields.Select_Department') }}',
            save_changes: '{{ trans('button.save_changes') }}',
            save:'{{ trans('button.save') }}',
            press_enter_with_Search: '{{ trans('content.procurement_fields.press_enter_with_Search') }}',
            Search: '{{ trans('content.procurement_fields.Search') }}',
            Refresh_List: '{{ trans('content.procurement_fields.Refresh_List') }}',
        };
        config.currency = @json($vd['currencies']);
        new BudgetList(config);
    </script>
@endpush