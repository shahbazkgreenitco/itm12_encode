{{-- @page-meta
{
  "page_no": "PC04EH-26",
  "file": "escalation-history.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans("problem_category.service_ticket_fields.escalation_history"))
@section('content')
<div id="main-user-list-wrapper" class="pbmg-list-wrapper">

    <!-- Header -->
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans("problem_category.service_ticket_fields.escalation_history") }} {{ $pc_name ? '- ' . $pc_name : '' }}</h3>
    </div>

    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">

                    <!-- Top Controls -->
                    <div class="d-flex align-items-center gap-2 mb-1">

                        <!-- Pagination -->
                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                <option value="10" selected>{{ trans("problem_category.service_ticket_fields.show_10") }}</option>
                                <option value="25">{{ trans("problem_category.service_ticket_fields.show_25") }}</option>
                                <option value="50">{{ trans("problem_category.service_ticket_fields.show_50") }}</option>
                                <option value="100">{{ trans("problem_category.service_ticket_fields.show_100") }}</option>
                            </select>
                        </div>

                        <div class="flex-grow-1"></div>

                        <!-- Search -->
                        <div>
                            <div class="amg-list-searchbar">
                                 <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                                <input type="text"
                                    class="amg-list-searchbar__input escalation-search searchbox plain-search"
                                    placeholder="{{ trans("problem_category.service_ticket_fields.search_placeholder") }}">
                            </div>
                        </div>

                        <!-- Refresh -->
                        <button class="amg-refresh-btn btn-reload-list" type="button">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none">
                                <path d="M19.5 8.99867C19.5 13.4187 15.92 17 11.5 17C8.9 17 6.6 15.8 5.2 13.9"/>
                            </svg>
                            <span>{{ trans("problem_category.service_ticket_fields.refresh_list") }}</span>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="js-escalation-list-view-panel">
                        <div class="table-responsive">
                            <table id="escalationTable" class="table display app-data-table">

                                <thead>
                                  <tr>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.id") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.problem_category") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.Escalate_For") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.esclate_stage_no") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.Esclate_Trigger_Time_Hrs") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.Escalate_To") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.sla_count") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.technician_mark_cc") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.updated_on") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.updated_by") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.deleted_on") }}</h4></th>
                                        <th><h4>{{ trans("problem_category.service_ticket_fields.deleted_by") }}</h4></th>
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


@push('scripts')
<script src="{!! CommonHelper::asset('js/tickets/problem-category/esclation-history.js') !!}"></script>

<script>
$(document).ready(function() {

    var config = {};

    config.url = {
        esclationHistoryAjax: "{{ url('tickets/problem-categories/esclation-history-ajax') }}",
    };

    config.token = "{{ csrf_token() }}";
    config.category_id = {!! json_encode($category_id) !!};

    config.translations = {
        serach_option: '{{ trans('problem_category.service_ticket_fields.serach_option') }}',
        Esclation: '{{ trans('problem_category.service_ticket_fields.Esclation') }}',
        SUB_CATEGORY: '{{ trans('problem_category.service_ticket_fields.SUB_CATEGORY') }}',
        refresh_list: '{{ trans('problem_category.service_ticket_fields.refresh_list') }}',
    };

    new EscalationHistory(config);

});
</script>
@endpush
