{{-- @page-meta { "page_no": "ACR-01", "version": "1.0", "description": "All Change Request Listing" } --}}
{{-- * ------------------------------------------------------------
* File: index.blade.php
* Module: Change Management Module
* CRM/26/01
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version of All Change Request Listing (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
@extends('layouts.layout1')
@section('title', 'All Change Request')
@section('content')
<div id="page_boxed">
    <section class="content">
        {{-- ① HEADER --}}
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <div class="d-flex align-items-center gap-2">
                <h3 class="h3-text mb-0" data-bs-toggle="tooltip" title="{{ $title }}">{{ $title }}</h3>
            </div>
            <div class="d-flex align-items-center gap-1">
                <div class="acr-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" placeholder="{{ trans('change_management/list.search') }}" id="acrSearch" class="plain-search">
                </div>
                <button class="header-icon-btn header-icon-btn-sm" id="btnOpenFilter" type="button" data-bs-toggle="tooltip"
                    title="{{ trans('ticket.filter') }}">
                    <svg viewBox="0 0 20 18" fill="none">
                        <path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z" fill="currentColor" />
                    </svg>
                    <span class="b6-text">{{ trans('ticket.filter') }}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>
                <div id="short_wraper" class="position-relative">
                    <button type="button" class="header-icon-btn header-icon-btn-sm" id="srqSortDrop">
                        <span class="sort-action">
                            <i id="sortDirectionIcon" class="bi bi-sort-down"></i>
                        </span>
                        <span class="dropdown-action">
                            <i class="bi bi-caret-down-fill"></i>
                        </span>
                    </button>
                    <ul class="dropdown-menu" id="short_items"></ul>
                </div>
                @can("ChangeRequestDownload")
                    <button class="header-icon-btn-only header-icon-btn-only-sm btn-download" type="button"
                        data-bs-toggle="tooltip" title="{{ trans('ticket.download') }}">
                        <svg viewBox="0 0 18 18" fill="none">
                            <path
                                d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.61011 11.921 8.80098 12.0006 9 12.0006C9.19902 12.0006 9.38989 11.921 9.53063 11.7806L13.2806 8.03063C13.421 7.88989 13.5004 7.69902 13.5004 7.5C13.5004 7.30098 13.421 7.11011 13.2806 6.96937C13.1399 6.82864 12.949 6.74958 12.75 6.74958C12.551 6.74958 12.3601 6.82864 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                @endcan
                <button class="header-icon-btn header-icon-btn-sm btn-reload-list" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('ticket.refresh') }}">
                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                            fill="currentColor"></path>
                    </svg>
                </button>
                <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-record" type="button">
                    <svg width="14" height="14" viewBox="0 0 19 19" fill="none">
                        <path
                            d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                            fill="currentColor" />
                    </svg>
                    <span>{{ trans('change_management/list.Change_Request') }}</span>
                </button>
            </div>
        </div>

        {{-- ② MAIN --}}
        <main class="main-content" id="mainContent">
            <div class="acr-body">

                {{-- Toolbar --}}
                <div class="acr-toolbar">
                    <button class="acr-select-all">
                        <input type="checkbox" id="acrSelectAll" onclick="acrCheckAll(this)">
                        {{ trans('change_management/list.Select_All') }}
                    </button>                    
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth" id="pageLimiter">
                            <option value="10" selected>{{ trans('auto-allocation-group.table.show_10') }}</option>
                            <option value="25">{{ trans('auto-allocation-group.table.show_25') }}</option>
                            <option value="50">{{ trans('auto-allocation-group.table.show_50') }}</option>
                            <option value="100">{{ trans('auto-allocation-group.table.show_100') }}</option>
                        </select>
                    </div>
                    <div class="acr-legend">
                        <div class="acr-leg-item">
                            <span class="acr-leg-dot" style="background:#22c55e;"></span> {{ trans('change_management/list.Minor') }}
                        </div>
                        <div class="acr-leg-item">
                            <span class="acr-leg-dot" style="background:#ef4444;"></span> {{ trans('change_management/list.Emergency') }}
                        </div>
                        <div class="acr-leg-item">
                            <span class="acr-leg-dot" style="background:#8b5cf6;"></span> {{ trans('change_management/list.Standard') }}
                        </div>
                        <div class="acr-leg-item">
                            <span class="acr-leg-dot" style="background:#E28F16;"></span> {{ trans('change_management/list.Standard') }}
                        </div>
                    </div>
                </div>

                {{-- Cards Container --}}
                <div class="acr-cards" id="lg"></div>

                <div class="d-flex align-items-center justify-content-between mt-2 mb-5 flex-wrap gap-2">
                    <div>
                        <span id="page-btm-summary" class="b5-text text-muted">{{ trans('ticket.available_records') }} 0</span>
                    </div>
                    <nav>
                        <ul id="pagebtns" class="pagination mb-0 tkt-pagination d-flex align-items-center gap-1">
                            <!-- Pagination buttons will be dynamically generated -->
                        </ul>
                    </nav>
                </div>
            </div>
        </main>
    </section>
    @include('change_management.crm_add_modal')
    @include('change_management.crm_filter_modal')
</div>
@endsection

@push('css')
    <link rel="stylesheet" href="{!! CommonHelper::asset('assets/css/changemanagement.css') !!}">
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/simple_pagination/pagination.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/flatpicker/css/flatpicker.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
@endpush

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('js/change_management/index.js') !!}"></script>
    <script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/simple_pagination/pagination.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/flatpicker/js/flatpicker.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script>
        /* Select All */
        function acrCheckAll(cb) {
            document.querySelectorAll('.acr-row-cb').forEach(c => c.checked = cb.checked);
        }
        
       
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.url.requests = "{{ url('change-management/ajaxList') }}";
            config.url.add = "{{ url('change-management/add') }}";
            config.url.info = "{{ url('change-management/myRequestList/info') }}";
            config.url.cm_attachment = "{{ url('change-management/change-attachment') }}";
            config.url.attachment_remove = "{{ url('change-management/remove-attachment') }}";
            config.url.getSelectedUser = "{{ url('change-management/getUser') }}";
            config.url.getDevices = "{{ url('getDeviceByModel') }}";
            config.url.getSupplierByQuery = "{{ url('getSupplierByQuery') }}";
            config.url.getUserByAjax = "{{ url('change-management/getUser') }}";
            config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
            config.url.get_categories_by_company_access = "{{ url('change-management/getCategoriesByCompanyAccess') }}";
            config.url.export_change_management = "{{ url('change-management/export-change-management') }}";
            config.token = "{{ csrf_token() }}";
            config.client = "{{ config('app.client') }}";
            config.userId = "{{ auth()->user()->id ?? '' }}";
            config.companyId = "{{ auth()->user()->company_id ?? '' }}";
            config.change_type = @json($vd->change_type ?? []);
            config.category = @json($vd->category ?? []);
            config.priorities = @json($vd->priorities ?? []);
            config.currency = @json($vd->currency ?? []);
            config.risk         = @json($vd->risks ?? []);      
            config.impact       = @json($vd->impacts ?? []);
            config.statuses       = @json($vd->statuses ?? []);
            config.sort_fields = {!! json_encode($sort_fields) !!};
            config.company_defulte = {!! json_encode($company_id) !!};
            config.company_user_detail = {!! json_encode($userDatail) !!};
            config.translations = {
                Enter_text: "{{ trans('change_management/list.Enter_text') }}",
                No_Found: "{{ trans('change_management/list.No_Found') }}",
                Select_user: "{{ trans('change_management/list.Select_user') }}",
                upload_file: "{{ trans('service_ticket_fi/listload_file') }}",
                file_size: "{{ trans('change_management/list.file_size') }}",
                total_file_size: "{{ trans('change_management/list.total_file_size') }}",
                Select_the_Device: "{{ trans('change_management/list.Select_the_Device') }}",
                Available_Records: "{{ trans('change_management/list.Available_Records') }}",
                confirmation_message: "{{ trans('change_management/list.confirmation_message') }}",
                Select_the_Priority: "{{ trans('change_management/list.Select_the_Priority') }}",
                Select_the_Category: "{{ trans('change_management/list.Select_the_Category') }}",
                Select_the_Impact: "{{ trans('change_management/list.Select_the_Impact') }}",
                Select_the_Risk: "{{ trans('change_management/list.Select_the_Risk') }}",
                Select_the_Change_Type: "{{ trans('change_management/list.Select_the_Change_Type') }}",
                select_currency: "{{ trans('change_management/list.select_currency') }}",
                select_company: "{{ trans('change_management/list.select_company') }}",
                available_records: "{{ trans('ticket.available_records') }}",
                required_fields_info: "{{ trans('change_management/list.required_fields_info') }}",
                creator: "{{ trans('change_management/list.Creator') }}",
                Status: "{{ trans('change_management/list.Status') }}",
                Category: "{{ trans('change_management/list.Category') }}",
                Change_Type: "{{ trans('change_management/list.Change_Type') }}",
                Cab_name: "{{ trans('change_management/list.Cab_name') }}",
                priority: "{{ trans('change_management/list.priority') }}",
                impact: "{{ trans('change_management/list.impact') }}",
                risk: "{{ trans('change_management/list.risk') }}",
                Created_at: "{{ trans('change_management/list.Created_at') }}",
                updated_at: "{{ trans('change_management/list.updated_at') }}",
                manager: "{{ trans('change_management/list.manager') }}",
                start_date: "{{ trans('change_management/list.start_date') }}",
                history: "{{ trans('change_management/list.history') }}",
                View: "{{ trans('change_management/list.View') }}",
                New_change_request:"{{ trans('change_management/list.New_change_request') }}",
                has_been_created_successfully: " {{ trans('change_management/list.has_been_created_successfully') }}",
                Loading_Data: " {{ trans('change_management/list.Loading_Data') }}",
                records: " {{ trans('change_management/list.records') }}",
            };

            new Listing(config);
        });
    </script>
@endpush