@extends('layouts.layout1')
@section('title', trans('content.report_fields.Ticket_location_Wise_Report'))

@section('page_title')
    <div class="page-header">
        <h3>{{ trans('content.report_fields.Ticket_location_Wise_Report') }}</h3>
    </div>
@endsection

@section('content')
  <div class="location-report-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans('content.report_fields.Ticket_location_Wise_Report') }}</h3>
            <div class="d-flex gap-8">
                <button class="header-icon-btn header-icon-btn-sm btn-filter-open" type="button" data-bs-toggle="tooltip" title="{{ trans("problem_category.service_ticket_fields.Filter") }}">
                    <svg viewBox="0 0 20 18" fill="none" ><path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z" fill="currentColor" /></svg>
                    <span class="b6-text">{{ trans("problem_category.service_ticket_fields.Filter") }}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>
                <div class="amg-sort-wrapper position-relative">
                    <button type="button" class="amg-header-sort header-icon-btn header-icon-btn-sm" id="srqSortDrop">
                        <span class="sort-action">
                            <i id="sortDirectionIcon" class="bi bi-sort-down"></i>
                        </span>
                        <span class="dropdown-action">
                            <i class="bi bi-caret-down-fill"></i>
                        </span>
                    </button>
                    <ul class="dropdown-menu amg-sort-menu" id="short_items"></ul>
                </div>
                @can("LocationReportDownload")
                    <button class="header-icon-btn-only header-icon-btn-only-sm btn-download btn-internal-place-export" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('internal_place.view.download') }}">
                        <svg viewBox="0 0 18 18" fill="none">
                            <path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path>
                        </svg>
                    </button>
                @endcan
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">

                <div class="tab-pane fade show active" id="main-user-list-wrapper">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                <!-- show select -->
                                <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                    <select id="showSelect" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                        <option value="10" selected>{{ trans('internal_place.view.show') }} (10)</option>
                                        <option value="25">{{ trans('internal_place.view.show') }} (25)</option>
                                        <option value="50">{{ trans('internal_place.view.show') }} (50)</option>
                                        <option value="100">{{ trans('internal_place.view.show') }} (100)</option>
                                    </select>
                                </div>

                                <!-- spacer -->
                                <div class="flex-grow-1"></div>

                                <!-- searchbar -->
                                <div>
                                    <div class="amg-list-searchbar">
                                        <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input" id="tableSearch" placeholder="{{ trans('internal_place.view.search') }}...">
                                    </div>
                                </div>

                    <!-- refresh button -->
                    <button class="amg-refresh-btn btn-reload-list">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                        </svg>
                        <span>{{ trans('internal_place.view.refresh') }}</span>
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="lgTbl" class="mytable amg-datatable table display app-data-table" style="width:100%">
                        <thead>
                            <tr id="tHeadeRow"></tr>
                        </thead>
                        <tbody id="lg"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  </div>
</main>
{{-- FILTER MODAL --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">
            {{ __('report.filter_options') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ trans("content.filter_heading.filter_by_location") }}</label>
                <select name="filter_by_location[]" id="filter_by_location" class="form-select" multiple="multiple"></select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ trans("content.filter_heading.filter_by_date") }}</label>
                <select name="filter_by_date" id="filter_by_date" class="form-select">
                    <option value="null">{{ trans("content.report_fields.No_Filter") }}</option>
                    <option value="1">Created Date</option>
                    <option value="2">Resolved Date</option>
                    <option value="3">Closed Date</option>
                    <option value="4">Reopened Date</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ trans("content.filter_heading.filter_by_daterange") }}</label>
                <div id="reportrange"
                    class="form-control d-flex align-items-center justify-content-between"
                    style="cursor:pointer;">

                                <span></span>

                                <svg width="15px" height="15px" viewBox="0 0 48 48" fill="currentColor">
                                    <title>{{ trans('content.filter_heading.filter_by_daterange') }}</title>
                                    <g id="Layer_2" data-name="Layer 2">
                                        <g id="invisible_box" data-name="invisible box">
                                            <rect width="48" height="48" fill="none" />
                                        </g>
                                        <g id="icons_Q2" data-name="icons Q2">
                                            <path
                                                d="M44,8H35V4.1A2.1,2.1,0,0,0,33.3,2,2,2,0,0,0,31,4V8H17V4.1A2.1,2.1,0,0,0,15.3,2,2,2,0,0,0,13,4V8H4a2,2,0,0,0-2,2V42a2,2,0,0,0,2,2H44a2,2,0,0,0,2-2V10A2,2,0,0,0,44,8ZM42,40H6V20H42Zm0-24H6V12H42Z" />
                                            <rect x="8" y="24" width="8" height="8" rx="2"
                                                ry="2" />
                                            <rect x="32" y="24" width="8" height="8" rx="2"
                                                ry="2" />
                                            <rect x="20" y="24" width="8" height="8" rx="2"
                                                ry="2" />
                                        </g>
                                    </g>
                                </svg>
                                <input type="hidden" name="daterange" id="daterange">
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="modal-footer justify-content-end mb-4 pb-4 py-0">
                        <div class="amg-btn-group mt-3 gap-3 px-4">
                            <button id="btnClrFilter" type="button"
                                class="amg-btn amg-btn-ghost s2-text bg-black text-white hover-opacity-80 btn-clear-filter">
                                {{ trans('button.clear') }}
                            </button>
                            <button type="button" id="advanced_filter" class="amg-btn amg-btn-primary s2-text btn-filter btn-filter-apply">
                                {{ trans('user.user_filter.apply_filter') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('js/reports/tickets/location_wise.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function() {
    var config = {};
    config.url = {};
    config.url.get_tickets = "{{ url('reports/tickets/jx-location-wise') }}";
    config.url.export_ticket = "{{ url('reports/tickets/jx-download-location-wise') }}";
    config.url.departments_by_company = "{{ url('departments/by-company/privilage') }}";
    config.user = {!! json_encode(Auth::user()->only("id", "first_name", "last_name", "username", "company_id")) !!};
    config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
    config.sort_fields = {!! json_encode($sort_fields) !!};
    config.sort_dir = {id:1,dir:1};
    config.token = "{{ csrf_token() }}";
    config.locations = {!! json_encode($locations) !!};
    config.translations = {
        serach_option: '{{ trans('content.report_fields.serach_option') }}',
        No_Filter: '{{ trans('content.report_fields.No_Filter') }}',
        Show_Categories: '{{ trans('content.report_fields.Show_Categories') }}',
        Filter_By_Ticket_Handler: '{{ trans('content.filter_heading.Filter_By_Ticket_Handler') }}',
        filter_by_location: '{{ trans('content.filter_heading.filter_by_location_id') }}',    
    };
    new MyApp(config);
});
</script>
@endpush