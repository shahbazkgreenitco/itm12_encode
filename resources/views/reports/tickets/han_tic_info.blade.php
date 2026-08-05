@extends('layouts.layout1')
@php
    if ($main_filter == "day-hand-tic-info") {
        $pageTitle = trans('report.day_handler_tickets_info');
    } elseif ($main_filter == "location_info") {
        $pageTitle = trans('report.location_wise_tickets_info');
    } elseif ($main_filter == "department_info") {
        $pageTitle = trans('report.department_tickets_info');
    } else {
        $pageTitle = trans('report.handler_tickets_info');
    }
@endphp

@section('title', $pageTitle)


@section('page_title_css', 'pad-no')

@section('page_title')
@if($main_filter != "department_info" && $main_filter != "day-hand-tic-info" && $main_filter != "location_info")
    <h3>{{ trans('report.handler_tickets_info') }}</h3>
@elseif($main_filter == "day-hand-tic-info")
    <h3>{{ trans('report.day_handler_tickets_info') }}</h3>
@elseif($main_filter == "location_info")
    <h3>{{ trans('report.location_wise_tickets_info') }}</h3>
@else
    <h3>{{ trans('report.department_tickets_info') }}</h3>
@endif
@endsection
@section('content')
<main class="main-content" id="mainContent">
  <div class="container-fluid px-0">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between px-4">
        <button type="button" class="bg-transparent border-0 d-flex align-items-center gap-2"
            onclick="window.location.href='{{ url('reports/tickets/ticket-handler-wise') }}'">
            <svg width="20" height="20" viewBox="0 0 25 21" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                    fill="currentColor"></path>
            </svg>
            @if($main_filter != "department_info" && $main_filter != "day-hand-tic-info" && $main_filter != "location_info")
                <h3 class="h3-text mb-0">{{ trans('report.handler_tickets_info') }}</h3>
            @elseif($main_filter == "day-hand-tic-info")
                <h3 class="h3-text mb-0">{{ trans('report.day_handler_tickets_info') }}</h3>
            @elseif($main_filter == "location_info")
                <h3 class="h3-text mb-0">{{ trans('report.location_wise_tickets_info') }}</h3>
            @else
                <h3 class="h3-text mb-0">{{ trans('report.department_tickets_info') }}</h3>
            @endif
        </button>
        <div class="d-flex gap-8">
            <button class="header-icon-btn header-icon-btn-sm btn-filter-open" type="button" data-bs-toggle="tooltip" title="{{ trans("problem_category.service_ticket_fields.Filter") }}">
                <svg viewBox="0 0 20 18" fill="none" ><path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z" fill="currentColor" /></svg>
                <span class="b6-text">{{ trans("problem_category.service_ticket_fields.Filter") }}</span>
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
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-download"
                data-bs-toggle="tooltip" data-bs-original-title="{{ trans('internal_place.view.download') }}">
                <svg viewBox="0 0 18 18" fill="none">
                    <path
                        d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.61011 11.921 8.80098 12.0006 9 12.0006C9.19902 12.0006 9.38989 11.921 9.53063 11.7806L13.2806 8.03063C13.421 7.88989 13.5004 7.69902 13.5004 7.5C13.5004 7.30098 13.421 7.11011 13.2806 6.96937C13.1399 6.82864 12.949 6.74958 12.75 6.74958C12.551 6.74958 12.3601 6.82864 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                        fill="currentColor" />
                </svg>
            </button>
        </div>
    </div>

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
                    <button class="amg-refresh-btn btn-reload-list" id="btn-reload-list">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                        </svg>
                        <span>{{ trans('internal_place.view.refresh') }}</span>
                    </button>
                </div>
                <div class="table-responsive gtable-cover">
                    <table id="mytable" class="mytable amg-datatable table display app-data-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ trans('report.ticket_id') }}</th>
                                <th>{{ trans('report.ticket_subject') }}</th>
                                <th>{{ trans('report.department') }}</th>
                                <th>{{ trans('report.problem_category') }}</th>
                                <th>{{ trans('report.sub_category') }}</th>
                                <th>{{ trans('report.location') }}</th>
                                <th>{{ trans('report.status') }}</th>
                                <th>{{ trans('report.ticket_creator') }}</th>
                                <th>{{ trans('report.ticket_handler') }}</th>
                                <th>{{ trans('report.tat') }}</th>
                                <th>{{ trans('report.priority') }}</th>
                                <th>{{ trans('report.updated_at') }}</th>
                                <th>{{ trans('report.ticket_sla_breached') }}</th>
                            </tr>
                        </thead>
                        <colgroup>
                        </colgroup>
                        <tbody id="lg"></tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div id="page-btm-summary" class="page-summary"></div>
                    <div id="pagebtns" class="pagination-container"></div>
                </div>
            </div>
        </div>
    </div>
  </div>
</main>
<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Tickets</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 px-4">
                    <div class="col-lg-4 col-md-6 hide">
                        <label class="form-label b5-text">{{ trans("content.report_fields.filter_by_handler") }}</label>
                        <select id="filter_non_handler_modal" autocomplete="off" class="form-select">
                            <option value="null">{{ trans("content.report_fields.No_Filter") }}</option>
                            <option value="1">{{ trans("content.report_fields.non_handler") }}</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label b5-text">{{ trans("content.report_fields.filter_by_department") }}</label>
                        <select id="filter_by_department" class="form-select">
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label b5-text">{{ trans("content.report_fields.filter_by_category") }}</label>
                        <select id="filter_by_category" autocomplete="off" class="form-select">
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label b5-text">{{ trans("content.filter_heading.Filter_By_Sub_Category") }}</label>
                        <select id="filter_by_sub_category" autocomplete="off" class="form-select">
                        </select>
                    </div>
                </div>
                <div class="row g-3 px-4">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label b5-text">{{ trans("content.filter_heading.filter_by_location_id") }}</label>
                        <select id="filter_by_location" autocomplete="off" class="form-select">
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 hide">
                        <label class="form-label b5-text">{{ trans("content.filter_heading.Filter_By_Ticket_Creator") }}</label>
                        <select id="filter_by_creator" autocomplete="off" class="form-select">
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label b5-text">{{ trans("content.report_fields.filter_by_status") }}</label>
                        <select id="filter_by_status" autocomplete="off" class="form-select">
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-start mb-4 pb-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button id="btnClrFilterModal" type="button"
                        class="amg-btn amg-btn-ghost s2-text bg-black text-white hover-opacity-80 btn-clear-filter">
                        {{ trans('button.clear') }}
                    </button>
                    <button type="button" id="btnApplyFilterModal" class="amg-btn amg-btn-primary s2-text btn-filter btn-filter-apply"
                        style="min-width: 300px;">
                        {{ trans('user.user_filter.apply_filter') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('plugins/simple_pagination/pagination.css') !!}" rel="stylesheet" />
<style type="text/css">

        .gtable-cover {
            overflow-x: auto;
            max-width: 100%;
        }

        #lgTbl {
            min-width: 1800px;
            white-space: nowrap;
        }

        #lgTbl th:first-child,
        #lgTbl td:first-child {
            position: sticky;
            left: 0;
            z-index: 2;
            min-width: 20px;
        }

        #lgTbl thead th:first-child {
            z-index: 3;
            border-radius: 20px 0px 0px 0px;
        }

        .dot-loader {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            gap: 5px;
        }

        .dot-loader span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #0d6efd;
            animation: dotPulse 1.4s infinite ease-in-out both;
        }

        .dot-loader span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .dot-loader span:nth-child(2) {
            animation-delay: -0.16s;
        }

    /* Table horizontal scrolling */
    .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #92959b #eff2fa;
        scroll-behavior: smooth;
    }

    /* Custom scrollbar for WebKit browsers */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #eff2fa;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #92959b;
        border-radius: 10px;
        border: 2px solid #eff2fa;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }

    /* Table minimum width */
    .table-responsive table {
        min-width: 1200px;
        width: 100%;
    }

    /* Mobile optimization */
    @media (max-width: 768px) {
        .table-responsive table {
            min-width: 1000px;
        }
    }
    #short_items .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 10px;
        font-size: 12px;
    }

    #short_items .flex-grow-1 {
        flex: 1;
    }

    #short_items .like-radio {
        width: 16px;
        height: 16px;
        border: 2px solid #cbd5e1;
        border-radius: 50%;
        flex-shrink: 0;
        position: relative;
    }

    #short_items .dropdown-item.active {
        background: #fef2f2;
        color: #dc2626;
    }

    #short_items .dropdown-item.active .like-radio {
        border-color: currentColor;
    }

    #short_items .dropdown-item.active .like-radio::after {
        content: "";
        position: absolute;
        inset: 50%;
        width: 8px;
        height: 8px;
        background: currentColor;
        border-radius: 50%;
        transform: translate(-50%, -50%);
    }

    /* Sort Button */
    #short_wraper .header-icon-btn {
        display: flex;
        align-items: center;
        padding: 0;
        overflow: hidden;
    }

    #short_wraper .sort-action,
    #short_wraper .dropdown-action {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 34px;
    }

    #short_wraper .sort-action {
        padding: 0 8px;
        cursor: pointer;
        font-size: 18px
    }

    #short_wraper .dropdown-action {
        padding: 0 8px;
        border-left: 1px solid #d1d5db;
        cursor: pointer;
    }

    #short_wraper { 
        position: relative;
        display: inline-block;
    }

    #short_items.show {
        display: block;
        left: -58px;
        position: absolute;
    }

    #short_wraper .sort-action:hover,
    #short_wraper .dropdown-action:hover {
        background: rgba(0, 0, 0, .05);
    }

    /* Dark Mode */
    html[data-bs-theme="dark"] #short_items .dropdown-item.active {
        background: rgba(220, 38, 38, .25);
        color: #fff;
    }

    html[data-bs-theme="dark"] #short_wraper .dropdown-action {
        border-left-color: #4b5563;
    }

    html[data-bs-theme="dark"] #short_wraper .sort-action:hover,
    html[data-bs-theme="dark"] #short_wraper .dropdown-action:hover {
        background: rgba(255, 255, 255, .08);
    }
    .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 4px 4px;
        margin-top: 8px;
        border-top: 1px solid var(--bs-border-color, #dee2e6);
        flex-wrap: wrap;
        gap: 10px;
    }

      /* ========================================
           PAGINATION
        ======================================== */
        #pagebtns,
        .pagination-container {
            display: inline-block;
            margin: 0;
            padding: 0;
        }

        #pagebtns.simple-pagination,
        .pagination-container.simple-pagination {
            display: inline-block;
        }

        #pagebtns ul,
        .pagination-container ul {
            display: inline-flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 5px;
            flex-wrap: wrap;
            align-items: center;
        }

        #pagebtns ul li,
        .pagination-container ul li {
            display: inline-block;
            margin: 0;
            padding: 0;
        }

        /* ===== PAGE BUTTONS ===== */
        #pagebtns ul li a,
        #pagebtns ul li span,
        .pagination-container ul li a,
        .pagination-container ul li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 37px;
            height: 30px;
            padding: 6px 12px;
            border: 1px solid #D0D5DD !important;
            border-radius: 8px;
            background: #eff2fa;
            color: #344054 !important;
            font-size: 15px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            gap: 7px;
            white-space: nowrap;
            transition: all 0.2s ease;
            text-decoration: none;
            line-height: 1;
            box-shadow: none;
        }

        /* ===== HOVER EFFECT ===== */
        #pagebtns ul li a:hover:not(.disabled):not(.active),
        .pagination-container ul li a:hover:not(.disabled):not(.active) {
            background: #e3e7ec;
            border-color: #D0D5DD;
            color: #344054 !important;
            text-decoration: none;
        }

        /* ===== ACTIVE PAGE ===== */
        #pagebtns ul li.active a,
        #pagebtns ul li.active span,
        .pagination-container ul li.active a,
        .pagination-container ul li.active span {
            background: #8f949d;
            color: #fff !important;
            border-color: #8f949d !important;
            min-width: 42px;
            padding: 0 14px;
            cursor: default;
            font-weight: 600;
            z-index: 1;
        }

        /* ===== DISABLED STATE ===== */
        #pagebtns ul li.disabled a,
        #pagebtns ul li.disabled span,
        .pagination-container ul li.disabled a,
        .pagination-container ul li.disabled span {
            opacity: 0.65;
            cursor: not-allowed;
            pointer-events: none;
            background: #eff2fa;
            border-color: #D0D5DD !important;
            color: #6c757d !important;
        }

        /* ===== PREV/NEXT BUTTONS ===== */
        #pagebtns ul li a.prev,
        #pagebtns ul li a.next,
        #pagebtns ul li span.prev,
        #pagebtns ul li span.next,
        .pagination-container ul li a.prev,
        .pagination-container ul li a.next,
        .pagination-container ul li span.prev,
        .pagination-container ul li span.next {
            font-weight: 500;
            padding: 6px 14px;
            gap: 7px;
        }

        /* ===== ELLIPSIS (...) ===== */
        #pagebtns ul li .ellipse,
        .pagination-container ul li .ellipse {
            border: none !important;
            background: transparent !important;
            cursor: default;
            padding: 6px 8px !important;
            color: #6c757d;
            font-size: 15px;
            min-width: auto !important;
            border-radius: 0 !important;
            height: auto !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        #pagebtns ul li .ellipse.clickable,
        .pagination-container ul li .ellipse.clickable {
            cursor: pointer;
            color: #344054;
            padding: 6px 8px !important;
        }

        #pagebtns ul li .ellipse.clickable:hover,
        .pagination-container ul li .ellipse.clickable:hover {
            color: #0d6efd;
            background: transparent !important;
        }

        /* ===== CURRENT PAGE TEXT ===== */
        #pagebtns ul li .current,
        .pagination-container ul li .current {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 37px;
            height: 30px;
            padding: 6px 12px;
            border: 1px solid #D0D5DD !important;
            border-radius: 8px;
            background: #eff2fa;
            color: #344054 !important;
            font-size: 15px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            gap: 7px;
            white-space: nowrap;
            transition: all 0.2s ease;
            text-decoration: none;
            line-height: 1;
        }

        #pagebtns ul li.active .current,
        .pagination-container ul li.active .current {
            background: #8f949d;
            color: #fff !important;
            border-color: #8f949d !important;
            min-width: 42px;
            padding: 0 14px;
        }

        #pagebtns ul li.disabled .current,
        .pagination-container ul li.disabled .current {
            opacity: 0.65;
            cursor: not-allowed;
            pointer-events: none;
            background: #eff2fa;
            border-color: #D0D5DD !important;
            color: #6c757d !important;
        }

        /* ===== SVG ICONS IN PAGINATION ===== */
        #pagebtns ul li a svg,
        .pagination-container ul li a svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }
        .form-select {
            z-index: 999999999;
        }
</style>
@endpush

@push('scripts')

<script type="text/javascript" src="{!! CommonHelper::asset('js/reports/tickets/hand_tic_info.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('plugins/simple_pagination/pagination.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function() {
    var config = {};
    config.url = {};
    config.parent_id = "{{ isset($_GET['parent_id']) ? $_GET['parent_id'] : '' }}";
    config.sub_id = "{{ isset($_GET['sub_id']) ? $_GET['sub_id'] : '' }}";
    config.department_id = "{{ isset($_GET['department_id']) ? $_GET['department_id'] : '' }}";
    config.comman_filter = "{{ isset($_GET['department_filters']) ? $_GET['department_filters'] : (isset($_GET['handler_filters']) ? $_GET['handler_filters'] : '')}}";
    config.pcategory = "{{ isset($_GET['pcategory-on']) ? $_GET['pcategory-on'] :  '' }}";
    config.subcategory = "{{ isset($_GET['category-on']) ? $_GET['category-on'] : ''}}";
    config.url.view_ticket = "{{ url('ticket') }}";
    config.assigned_to = "{{ isset($_GET['assigned_to']) ? $_GET['assigned_to'] : '' }}";
    config.status_id = "{{ isset($_GET['status_id']) ? $_GET['status_id'] : '' }}";
    config.location_id = "{{ isset($_GET['location_id']) ? $_GET['location_id'] : '' }}";
    config.based_on = "{{ isset($_GET['based_on']) ? $_GET['based_on'] : ''  }}"
    config.daterange = "{{ isset($_GET['daterange']) ? $_GET['daterange'] : ''  }}"
    config.ticket_type = "{{ isset($_GET['ticket_type']) ? $_GET['ticket_type'] : '' }}";
    config.action_type = "{{ isset($_GET['action_type']) ? $_GET['action_type'] : '' }}";
    config.url.list_handler_tickets = "{{ url('reports/tickets/jx-hand_tic_info') }}";
    config.url.export_ticket = "{{ url('reports/export-hand_tic_info') }}";
    config.url.export_ticket_pdf = "{{ url('reports/export-hand_tic_info-pdf') }}";
    config.getCompanyByUserAccess = "{{ url('getUserByQuery') }}";
    config.sort_fields = {!! json_encode($sort_fields) !!};
    config.tbl_fields = {!! json_encode($tbl_fields) !!};
    config.sla = "{{ isset($_GET['sla']) ? $_GET['sla'] : '' }}";
    config.openTicket = "{{isset($_GET['openTicket']) ? $_GET['openTicket'] : ''}}";
    config.pendingTicket="{{isset($_GET['PendingTicketCount']) ? $_GET['PendingTicketCount'] : ''}}";
    config.openMoreThanDaysTickets="{{isset($_GET['openMoreThan7DaysTickets']) ? $_GET['openMoreThan7DaysTickets'] : ''}}";
    config.ClosedMoreThanDaysTickets="{{isset($_GET['ClosedMoreThan7DaysTickets']) ? $_GET['ClosedMoreThan7DaysTickets'] : ''}}";
    config.techboard = "{{ isset($_GET['techboard']) ? $_GET['techboard'] : '' }}";
    config.sort_dir = {id:10,dir:2};
    config.main_filter = "{{ $main_filter }}";
    config.token = "{{ csrf_token() }}";
    config.getUserByQuery = "{{ url('getUserByQuery') }}";
    config.department = {!! json_encode($vd->department) !!};
    config.status = {!! json_encode($vd->statuses) !!};
    config.locations = {!! json_encode($vd->location) !!};
    config.problem_category = {!! json_encode($vd->problem_category) !!};
    config.company = {!! json_encode($company) !!};
    config.url.departments_by_company = "{{ url('departments/by-company/privilage') }}";
    config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
    config.translations = {
        serach_option: '{{ trans('content.report_fields.serach_option') }}',
        No_Filter: '{{ trans('content.report_fields.No_Filter') }}',
        Filter_Based_on: '{{ trans('content.filter_heading.Filter_Based_on') }}',
        Filter_By_Ticket_Handler: '{{ trans('content.filter_heading.Filter_By_Ticket_Handler') }}',
        filter_by_location: '{{ trans('content.filter_heading.filter_by_location') }}',
        filter_by_department: '{{ trans('content.filter_heading.filter_by_department') }}'
    };
    new MyApp(config);
});
</script>
@endpush