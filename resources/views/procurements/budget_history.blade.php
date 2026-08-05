{{-- @page-meta
{
  "page_no": "",
  "file": "budget-history.blade.php",
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
@section('title', trans('budget.history.title'))
@section('content')
<div id="main-budget-history-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('budget.history.title') }}</h3>
        <div class="d-flex gap-8">
            {{-- Toolbar icon-only button --}}
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-back" type="button" data-bs-toggle="tooltip" title="{{ trans('budget.history.back') }}">
                <i class="bi bi-arrow-left"></i>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    {{-- List controls row: page length | search | refresh --}}
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown budget-history-page-length">
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
                                <input type="text" class="amg-list-searchbar__input budget-history-search" placeholder="Search...">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list">
                            <i class="bi bi-arrow-clockwise amg-refresh-btn__icon"></i>
                            <span>Refresh</span>
                        </button>
                    </div>
                    {{-- Server-side DataTable: thead only, tbody rendered by JS --}}
                    <div class="js-budget-history-view-panel">
                        <div class="table-responsive">
                            <table id="mytable" class="table display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Category') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Account_Type') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Financial_Year') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Department') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Amount') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Account_Code') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Utilized_Budget') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.In_Progress') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.Remaining_Budget') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.updated_by') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('content.procurement_fields.updated_at') }}</h4></th>
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
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<style>
    .dataTables_scrollBody { width: unset; }
</style>
@endpush

@push('scripts')
    <script src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/procurement/budget-history.js') !!}"></script>
    <script>
        var config = {};
        config.url = {};
        config.url.BudgetList = "{{ route('procurements.budgets.list') }}";
        config.url.historyAjax = "{{ route('procurements.budgets.history-ajax') }}";
        config.token = "{{ csrf_token() }}";
        config.budget_id = @json($budget_id);
        config.translations = {
            serach_option: '{{ trans('content.service_ticket_fields.serach_option') }}'
        };
        new BudgetHistory(config);
    </script>
@endpush