{{-- @page-meta
{
"page_no": "TKT01L-01",
"file": "my-tickets.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Shivam Kumar",
        "from": "2026-05",
        "reviewer": null,
        "description": "Initial setup – My Tickets full card layout"
        }
        {
        "version": "1.1",
        "writer": "Peiya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Ticket listing Task Add , Task list and progress of completed task"
        }
    ]
}
--}}
@extends('layouts.layout1')
@section('title', $title )
@section('content')
    <div id="ticket-list-page">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <div class="d-flex align-items-center gap-2">
                <h3 class="h3-text mb-0" data-bs-toggle="tooltip" title="{{ $title }}">{{ Str::limit($title, 13, '...') }}</h3>
                <div class="position-relative">
                    <span id="customStatusCount" class="custom-status-badge d-none">
                        0
                    </span>
                    <select id="tktPagecustomStatus" class="userModulePageLenth amg-table-pagination-dropdown">
                        <option value="null" selected>{{trans('ticket.ticket_menu.all_tickets')}}</option>
                    </select>
                </div>
                <button id="addCustomStatusBtn" class="header-icon-btn header-icon-btn-sm" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('ticket.add-status') }}">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M6 1V11M1 6H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    <span class="b6-text fw-normal">{{ trans('ticket.add-status') }}</span>
                </button>
            </div>
            <div class="d-flex align-items-center gap-1">
                <input type="text" class="form-control" id="ticket-search-input" placeholder="{{trans('ticket.ticket_list.search_ticket')}}">
                <button class="header-icon-btn header-icon-btn-sm" id="btnOpenFilter" type="button" data-bs-toggle="tooltip"
                    title="{{ trans('ticket.filter') }}">
                    <svg viewBox="0 0 20 18" fill="none">
                        <path
                            d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                            fill="currentColor" />
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
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-reload-list" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('ticket.refresh') }}">
                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                            fill="currentColor"></path>
                    </svg>
                </button>
                @can("TicketDownload")
                    <button class="header-icon-btn-only header-icon-btn-only-sm btn-download" type="button"
                        data-bs-toggle="tooltip" title="{{ trans('ticket.download') }}">
                        <svg viewBox="0 0 18 18" fill="none">
                            <path
                                d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.61011 11.921 8.80098 12.0006 9 12.0006C9.19902 12.0006 9.38989 11.921 9.53063 11.7806L13.2806 8.03063C13.421 7.88989 13.5004 7.69902 13.5004 7.5C13.5004 7.30098 13.421 7.11011 13.2806 6.96937C13.1399 6.82864 12.949 6.74958 12.75 6.74958C12.551 6.74958 12.3601 6.82864 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                @endcan
                <button class="amg-btn amg-btn-primary amg-btn-sm btn-create-ticket" id="btnCreateTicket" type="button">
                    <svg width="14" height="14" viewBox="0 0 19 19" fill="none">
                        <path
                            d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                            fill="currentColor" />
                    </svg>
                    <span>{{ trans('ticket.create-ticket') }}</span>
                </button>
            </div>
        </div>

        <main class="main-content" id="mainContent">
            <div class="container-fluid px-3 mt-2">
                <div>
                    <div class="tkt-toolbar" id="toolbar">
                        <label class="tkt-select-all">
                            <input class="form-check-input" type="checkbox" id="tktSelectAll">
                            <span>{{ trans('ticket.select-all') }}</span>
                        </label>
                        <select id="tktPageLen" class="amg-table-pagination-dropdown">
                            <option value="10" selected>{{ trans('ticket.show') }} (10)</option>
                            <option value="25">{{ trans('ticket.show') }} (25)</option>
                            <option value="50">{{ trans('ticket.show') }} (50)</option>
                            <option value="100">{{ trans('ticket.show') }} (100)</option>
                        </select>
                        <div class="tkt-legend">
                            <div class="tkt-legend-item">
                                <span class="tkt-legend-dot" style="background:#9333ea;"></span>
                                <div class="tkt-legend-popover">
                                    <span class="tkt-legend-pop-title" style="color:#9333ea;">{{ trans('ticket.new-ticket') }}</span>
                                    <p>{{ trans('ticket.legend_new_ticket_desc') }}</p>
                        </div>
                            </div>

                            <div class="tkt-legend-item">
                                <span class="tkt-legend-dot" style="background:#2563eb;"></span>
                                <div class="tkt-legend-popover">
                                    <span class="tkt-legend-pop-title" style="color:#2563eb;">{{ trans('ticket.handler-updated') }}</span>
                                    <p>Updated by the assigned handler.</p>
                                </div>
                            </div>

                            <div class="tkt-legend-item">
                                <span class="tkt-legend-dot" style="background:#f59e0b;"></span>
                                <div class="tkt-legend-popover">
                                    <span class="tkt-legend-pop-title" style="color:#f59e0b;">{{ trans('ticket.guest-updated') }}</span>
                                   <p>Guest has replied to the ticket.</p>
                                </div>
                            </div>

                            <div class="tkt-legend-item">
                                <span class="tkt-legend-dot" style="background:#ef4444;"></span>
                                <div class="tkt-legend-popover">
                                    <span class="tkt-legend-pop-title" style="color:#ef4444;">{{ trans('ticket.reopened') }}</span>
                                    <p>Previously closed ticket reopened.</p>
                                </div>
                            </div>

                            <div class="tkt-legend-item">
                                <span class="tkt-legend-dot" style="background:#10b981;"></span>
                                <div class="tkt-legend-popover">
                                    <span class="tkt-legend-pop-title" style="color:#10b981;">{{ trans('ticket.resolved/closed') }}</span>
                                     <p>Ticket has been resolved or closed.</p>
                                </div>
                            </div>

                            <div class="tkt-legend-item">
                                <span class="tkt-legend-dot" style="background:#9ca3af;"></span>
                                <div class="tkt-legend-popover">
                                    <span class="tkt-legend-pop-title" style="color:#9ca3af;">{{ trans('ticket.span') }}</span>
                                    <p>Marked as spam and excluded.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex-grow-1"></div>
                        @php
                            $action_company_id = ($dashboardCompanyId == 0) ? Auth::user()->company_id : $dashboardCompanyId;
                            $companyControls = $action_controls[$action_company_id] ?? [];
                        @endphp
                        @if(($isTechnician || $is_admin) && (($companyControls['ctrl_delete'] ?? 0) == 1 || ($companyControls['merge_privilege'] ?? 0) == 1 )  && ($main_filter != "archived" && $main_filter != "closed"))

                        <div class="bulk-action-wrapper">
                            <div class="bulk-action-trigger">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <circle cx="5"  cy="12" r="1.5" fill="currentColor"/>
                                    <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                                    <circle cx="19" cy="12" r="1.5" fill="currentColor"/>
                                </svg>
                            </div>

                            <div class="bulk-action-popover">
                            @if(($companyControls['ctrl_delete'] ?? 0) == 1)    
                                    <button id="bulk-delete" class="btn btn-sm d-inline-flex align-items-center gap-1 bukl-action-buttons" data-bs-toggle="tooltip" title="{{ trans('ticket.bulk_delete') }}">
                                    <svg width="14" height="15" viewBox="0 0 14 15" fill="none"><path d="M12.8159 2.23184H10.0298V1.67388C10.0298 1.22994 9.85373 0.80418 9.54023 0.490267C9.22674 0.176355 8.80155 0 8.3582 0L5.01492 0C4.57158 0 4.14639 0.176355 3.83289 0.490267C3.5194 0.80418 3.34328 1.22994 3.34328 1.67388V2.23184H0.557214C0.409431 2.23184 0.267702 2.29062 0.163204 2.39526C0.0587064 2.4999 0 2.64182 0 2.7898C0 2.93778 0.0587064 3.07969 0.163204 3.18433C0.267702 3.28897 0.409431 3.34776 0.557214 3.34776H1.11443V13.391C1.11443 13.687 1.23184 13.9708 1.44084 14.1801C1.64983 14.3894 1.93329 14.5069 2.22885 14.5069H11.1443C11.4398 14.5069 11.7233 14.3894 11.9323 14.1801C12.1413 13.9708 12.2587 13.687 12.2587 13.391V3.34776H12.8159C12.9637 3.34776 13.1054 3.28897 13.2099 3.18433C13.3144 3.07969 13.3731 2.93778 13.3731 2.7898C13.3731 2.64182 13.3144 2.4999 13.2099 2.39526C13.1054 2.29062 12.9637 2.23184 12.8159 2.23184ZM4.45771 1.67388C4.45771 1.5259 4.51642 1.38398 4.62091 1.27934C4.72541 1.1747 4.86714 1.11592 5.01492 1.11592H8.3582C8.50599 1.11592 8.64772 1.1747 8.75222 1.27934C8.85671 1.38398 8.91542 1.5259 8.91542 1.67388V2.23184H4.45771V1.67388ZM11.1443 13.391H2.22885V3.34776H11.1443V13.391ZM5.57214 6.13755V10.6012C5.57214 10.7492 5.51343 10.8911 5.40893 10.9958C5.30443 11.1004 5.16271 11.1592 5.01492 11.1592C4.86714 11.1592 4.72541 11.1004 4.62091 10.9958C4.51642 10.8911 4.45771 10.7492 4.45771 10.6012V6.13755C4.45771 5.98957 4.51642 5.84765 4.62091 5.74301C4.72541 5.63838 4.86714 5.57959 5.01492 5.57959C5.16271 5.57959 5.30443 5.63838 5.40893 5.74301C5.51343 5.84765 5.57214 5.98957 5.57214 6.13755ZM8.91542 6.13755V10.6012C8.91542 10.7492 8.85671 10.8911 8.75222 10.9958C8.64772 11.1004 8.50599 11.1592 8.3582 11.1592C8.21042 11.1592 8.06869 11.1004 7.96419 10.9958C7.8597 10.8911 7.80099 10.7492 7.80099 10.6012V6.13755C7.80099 5.98957 7.8597 5.84765 7.96419 5.74301C8.06869 5.63838 8.21042 5.57959 8.3582 5.57959C8.50599 5.57959 8.64772 5.63838 8.75222 5.74301C8.85671 5.84765 8.91542 5.98957 8.91542 6.13755Z" fill="currentColor"></path></svg>
                                </button>
                            @endif

                            @if($isTechnician)  
                                @if($main_filter == "assigned")
                                        <button id="bulk-resolve" data-bs-toggle="tooltip" title="{{ trans('ticket.bulk_resolve') }}" class="btn btn-sm d-inline-flex align-items-center gap-1 bukl-action-buttons">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                @endif
                                    <button id="bulk-assign" data-bs-toggle="tooltip" title="{{ trans('ticket.bulk_assign') }}" class="btn btn-sm d-inline-flex align-items-center gap-1 bukl-action-buttons">
                                        <i class="bi bi-journal-check"></i>
                                    </button>
                                @endif

                            @if(isset($companyControls['merge_privilege']) && $companyControls['merge_privilege'] == 1)
                                <button id="bulk-merge" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ trans('ticket.bulk_merge') }}" class="js-act-show-merge btn btn-sm d-inline-flex align-items-center gap-1 bukl-action-buttons">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="21" width="7" height="5" rx="0.6" transform="rotate(-90 2 21)" stroke="currentColor" stroke-width="1.5"></rect>
                                        <rect x="17" y="15.5" width="7" height="5" rx="0.6" transform="rotate(-90 17 15.5)" stroke="currentColor" stroke-width="1.5"></rect>
                                        <rect x="2" y="10" width="7" height="5" rx="0.6" transform="rotate(-90 2 10)" stroke="currentColor" stroke-width="1.5"></rect>
                                        <path d="M7 17.5H10.5C11.6046 17.5 12.5 16.6046 12.5 15.5V8.5C12.5 7.39543 11.6046 6.5 10.5 6.5H7" stroke="currentColor" stroke-width="1.5"></path>
                                        <path d="M12.5 12H17" stroke="currentColor" stroke-width="1.5"></path>
                                    </svg>
                                </button>
                            @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    </div>
                <div id="list-card" class="d-flex flex-column">

                </div>
                <div class="d-flex align-items-center justify-content-between mt-2 mb-5 flex-wrap gap-2">
                    <div>
                        <span class="b5-text text-muted">{{ trans('ticket.available_records') }} 0</span>
                    </div>
                    <nav>
                        <ul id="pagebtns" class="pagination mb-0 tkt-pagination d-flex align-items-center gap-1">
                            <!-- Pagination buttons will be dynamically generated -->
                        </ul>
                    </nav>
                </div>
            </div>
        </main>
        @include('tickets.ticket-list.add-status')
        @include('tickets.ticket-list.edit-ticket')
        @include('tickets.ticket-list.ticket-transfar')
        @include('tickets.ticket-list.ticket-history')
        @include('tickets.ticket-list.update-status')
        @include('tickets.ticket-list.filter')
        @include("tickets.ticket-list.modal_delete")
        @include('tickets.ticket-list.create-ticket')
        @include("tickets.fields_info")
        @include("tickets.form_modal")
        @include("tickets.status_form_modal")
        @include("tickets.ticket-list.sentiment_analysis")
        @include("tickets.ticket-list.add-task-modal")
        @include("tickets.ticket-list.task_detail")
        @include("tickets.modal_description")
        @include("tickets.ticket-list.bulk_resolved")
        @include("tickets.ticket-list.bulk_assign_modal")
        @include("tickets.ticket-list.merge_modal")
        @include("tickets.ticket-list.article_convert")
        @include("tickets.kd_suggestion")
    </div>
@endsection
@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/simple_pagination/pagination.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('plugins/flatpicker/css/flatpicker.min.css') !!}" rel="stylesheet" />
    <style>

    #bulkAssignMdl .select2-container{
        z-index: 9999 !important;
        
    }

    .text-danger{
        color: #f12f35 !important;
    }

    #bulkAssignMdl .modal-dialog{
    max-width: 900px;
    }

    #bulkAssignMdl .modal-content{
        height: 90vh;
        display: flex;
        flex-direction: column;
    }

    #bulkAssignMdl .modal-header{
        flex: 0 0 auto;
    }

    #bulkAssignMdl .modal-body{
        flex: 1 1 auto;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #bulkAssignMdl .modal-footer{
        flex: 0 0 auto;
    }

    #bulkAssignMdl .table-responsive{
        overflow-x: auto;
    }

    #bulkAssignMdl .select2-container{
        width:100% !important;
    }

    .select2-container--open{
        z-index:999999 !important;
    }
        .bukl-action-buttons{
            height:30px;
            border:1px solid #e5e7eb;
            border-radius:7px;
            font-size:12px;
            color:#6b7280;
            background:#fff;
        }

        [data-bs-theme="dark"] .bukl-action-buttons {
            border: 1px solid var(--bs-border-color);
            color: var(--bs-body-color);
            background-color: var(--bs-body-bg);
        }

        .label-merged {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            background: #fff3cd;
            border: 1px solid #ffe69c;
            color: #997404;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.4;
            vertical-align: middle;
            transition: all 0.2s ease;
        }

        .label-merged a {
            color: #fd7e14;
            text-decoration: none;
            font-weight: 700;
        }

        .label-merged a:hover {
            text-decoration: underline;
        }

        /* Bootstrap 5 Dark Mode */
        [data-bs-theme="dark"] .label-merged {
            background: rgba(255, 193, 7, 0.12);
            border-color: rgba(255, 193, 7, 0.25);
            color: #ffd666;
        }

        [data-bs-theme="dark"] .label-merged a {
            color: #ffb84d;
        }
        .merge-row {
            background: #fff;
            transition: all .2s ease;
        }

        .merge-row:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        .merge-row .badge {
            font-size: 12px;
        }
        .custom-status-badge {
            position: absolute;
            top: -6px;
            right: -2px;
            min-width: 16px;
            height: 16px;
            padding: 0 3px;
            border-radius: 10px;
            background: #ff6692;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            line-height: 16px;
            text-align: center;
            z-index: 999;
        }
        html[data-bs-theme="dark"] .custom-status-badge{
            background: #525252;
            color: #fff;
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

        /* #ticket-transfer-mdl-frm .note-editor {
            padding-inline: 0px;
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
    
        #ticket-transfer-mdl-frm .note-editor.note-frame,
        #ticket-transfer-mdl-frm .note-editor.note-frame.fullscreen {
        border: 1.5px solid #DFDFE1 !important;
        border-radius: 5px !important;
        overflow: hidden;
        box-shadow: none !important;
        transition: border-color .2s, box-shadow .2s;
        }
        #ticket-transfer-mdl-frm .note-editor.note-frame:focus-within {
        border-color: var(--brand) !important;
        box-shadow: 0 0 0 3px var(--brand-light) !important;
        }

        #ticket-transfer-mdl-frm .note-editor.note-airframe .note-placeholder, .note-editor.note-frame .note-placeholder {
            padding: 14px 16px;
            font-weight: 400;
        }
        #ticket-transfer-mdl-frm .note-btn-group.btn-group.note-font {
            padding-right: 10px;
            border-right: 1px solid #DFDFE1;
            border-radius: 0px;
        }

        #ticket-transfer-mdl-frm .note-editor.note-frame:focus-within {
            border-color: #e2e8f0 !important;  
            box-shadow: none !important;        
            outline: none !important;
        }

        #ticket-transfer-mdl-frm .note-editable:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    
        #ticket-transfer-mdl-frm .note-toolbar {
        border-bottom: 1.5px solid #DFDFE1 !important;
        padding: .45rem .6rem !important;
        display: flex;
        align-items: center;
        gap: .15rem;
        flex-wrap: wrap;
        }
    
        #ticket-transfer-mdl-frm .note-toolbar .note-btn {
        background: transparent !important;
        border: none !important;
        border-radius: 6px !important;
        padding: .28rem .42rem !important;
        color: #64748b !important;
        font-size: .8rem !important;
        transition: background .15s, color .15s;
        line-height: 1;
        }
        #ticket-transfer-mdl-frm .note-toolbar .note-btn:hover,
        #ticket-transfer-mdl-frm .note-toolbar .note-btn.active {
        background: var(--brand-light) !important;
        color: var(--brand) !important;
        }
        #ticket-transfer-mdl-frm .note-toolbar .note-btn-group {
        display: flex;
        align-items: center;
        }
    
        #ticket-transfer-mdl-frm .note-toolbar .note-btn-group + .note-btn-group::before {
        content: '';
        display: inline-block;
        width: 1px;
        height: 18px;
        background: var(--border);
        margin: 0 .35rem;
        align-self: center;
        }
    
        #ticket-transfer-mdl-frm .note-style .dropdown-toggle {
        font-weight: 700 !important;
        font-size: .875rem !important;
        letter-spacing: -.01em;
        min-width: 44px;
        }
    
        #ticket-transfer-mdl-frm .note-editable {
        height: 100px  !important;
        padding: .85rem 1rem !important;
        font-size: .9rem !important;
        color: var(--text) !important;
        background: var(--white) !important;
        caret-color: var(--brand);
        }
        #ticket-transfer-mdl-frm .note-editable:empty::before {
        content: attr(data-placeholder);
        color: var(--muted);
        pointer-events: none;
        }
    
        #ticket-transfer-mdl-frm .note-statusbar {
        background: #fafafa !important;
        border-top: 1px solid var(--border) !important;
        } */
    
        #ticket-transfer-mdl-frm .action-row {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        margin-top: 1.25rem;
        }
    
        #ticket-transfer-mdl-frm .btn-cancel {
        padding: .5rem 1.25rem;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: transparent;
        color: #64748b;
        font-size: .875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background .15s, border-color .15s;
        }
        #ticket-transfer-mdl-frm .btn-cancel:hover { background: var(--bg); border-color: #cbd5e1; }
    
        #ticket-transfer-mdl-frm .btn-submit {
        padding: .5rem 1.5rem;
        border-radius: 8px;
        border: none;
        background: var(--brand);
        color: #fff;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, transform .1s;
        }
        #ticket-transfer-mdl-frm .btn-submit:hover  { background: #6d28d9; }
        #ticket-transfer-mdl-frm .btn-submit:active { transform: scale(.97); }
        .tkt-hst-info-card {
            background: #EFF2FA;
            border-radius: 8px;
        }

        [data-bs-theme=dark] .tkt-hst-info-card {
            background: #1D1D1D;
            border: 1px solid #2A2A2D;
        }

        .tkt-hst-badge {
            width: 36px;
            height: 36px;
            font-size: 8px;
            line-height: 1.3;
        }

        .tkt-hst-badge.green {
            background: #186B43;
        }

        .tkt-hst-badge.purple {
            background: #5C00E5;
        }

        .tkt-hst-connector-line {
            width: 1px;
            background: #DFDFE1;
            flex: 1;
            min-height: 36px;
            align-self: center;
        }

        [data-bs-theme=dark] .tkt-hst-connector-line {
            background: #2A2A2D;
        }

        .tkt-hst-timeline-wrap {
            border-top: 1px solid #F0F0F0;
            max-height: 250px;
            overflow-y: auto;
        }

        [data-bs-theme=dark] .tkt-hst-timeline-wrap {
            border-top: 1px solid #2A2A2D;
        }

        .tkt-hst-card {
            border: 1px solid #F0F0F0;
            border-left: 6px solid #7F7F7F;
            border-radius: 6px;
            padding: 10px 14px;
        }

        [data-bs-theme=dark] .tkt-hst-card {
            border-color: #2A2A2D;
            border-left-color: #7F7F7F;
        }

        .tkt-hst-avatar {
            width: 20px;
            height: 20px;
            background: #e0e0e0;
        }

        .tkt-hst-timeline-item:last-child {
            margin-bottom: 0px;
        }

        .tkt-hst-timeline-item:last-child .tkt-hst-connector-line {
            display: none;
        }

        .truncate-text {
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    /* =========================
       SENTIMENT MODAL
       Bootstrap 5 Light/Dark
    ========================= */

    .sentiment-modal {
        --sm-bg: var(--bs-body-bg);
        --sm-card: var(--bs-tertiary-bg);
        --sm-border: var(--bs-border-color);
        --sm-text: var(--bs-body-color);
        --sm-muted: var(--bs-secondary-color);

        border: 0;
        border-radius: 18px;
        background: var(--sm-bg);
        color: var(--sm-text);
        overflow: hidden;

        box-shadow:
            0 10px 30px rgba(0, 0, 0, .12),
            0 3px 10px rgba(0, 0, 0, .05);

        transition: all .25s ease;
    }

    /* DARK MODE */
    [data-bs-theme="dark"] .sentiment-modal {
        box-shadow:
            0 12px 35px rgba(0, 0, 0, .45),
            0 3px 12px rgba(0, 0, 0, .2);
    }

    /* MODAL SPACING */
    .sentiment-modal .modal-header,
    .sentiment-modal .modal-body,
    .sentiment-modal .modal-footer {
        border: 0;
        padding-inline: 1.25rem;
    }

    .sentiment-modal .modal-header {
        padding-top: 1.15rem;
        padding-bottom: .6rem;
    }

    .sentiment-modal .modal-body {
        padding-bottom: 1rem;
    }

    .sentiment-modal .modal-footer {
        padding-top: 0;
        padding-bottom: 1.2rem;
    }

    /* =========================
       TITLE
    ========================= */

    .sentiment-title {
        width: 100%;
        text-align: center;
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -.3px;
    }

    /* =========================
       STATUS
    ========================= */

    .sentiment-status {
        text-align: center;
        margin-bottom: 1rem;
    }

    .emoji-wrap {
        width: 62px;
        height: 62px;
        margin: auto;
        border-radius: 50%;

        display: grid;
        place-items: center;

        font-size: 2rem;

        background: rgba(var(--bs-danger-rgb), .12);

        transition: .25s ease;
    }

    .negative-text {
        color: var(--bs-danger);
        font-size: 1.08rem;
        font-weight: 700;
        margin-top: .8rem;
    }

    .sentiment-sub-text {
        color: var(--sm-muted);
        font-size: .82rem;
        margin: .3rem 0 0;
        line-height: 1.45;
    }

    /* =========================
       COMMON CARD
    ========================= */

    .score-card,
    .explanation-card,
    .tone-card {
        background: var(--sm-card);
        border: 1px solid var(--sm-border);
        border-radius: 16px;
        padding: 1rem;

        transition: all .2s ease;
    }

    .score-card,
    .explanation-card {
        margin-bottom: .95rem;
    }

    /* =========================
       SCORE
    ========================= */

    .score-label,
    .score-footer,
    .progress-labels,
    .tone-title {
        font-size: .72rem;
        color: var(--sm-muted);
    }

    .score-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-top: .2rem;

        color: var(--bs-danger);

        transition: .25s ease;
    }

    .score-badge {
        padding: .38rem .8rem;
        border-radius: 50rem;

        font-size: .72rem;
        font-weight: 700;

        transition: .25s ease;
    }

    /* BADGE STATES */

    .score-badge.positive {
        background: rgba(var(--bs-success-rgb), .14);
        color: var(--bs-success);
    }

    .score-badge.neutral {
        background: rgba(var(--bs-warning-rgb), .14);
        color: var(--bs-warning);
    }

    .score-badge.negative {
        background: rgba(var(--bs-danger-rgb), .14);
        color: var(--bs-danger);
    }

    /* =========================
       PROGRESS
    ========================= */

    .progress-wrapper {
        margin-top: .2rem;
    }

    .sentiment-progress {
        height: 12px;
        border-radius: 50rem;

        position: relative;
        overflow: hidden;

        margin: .85rem 0 .4rem;

        background: linear-gradient(90deg,
                #ef4444 0%,
                #f97316 25%,
                #facc15 50%,
                #84cc16 75%,
                #22c55e 100%);
    }

    .progress-indicator {
        position: absolute;
        top: 50%;
        left: 50%;

        width: 16px;
        height: 16px;

        border-radius: 50%;

        background: #111;
        border: 3px solid #fff;

        transform: translate(-50%, -50%);

        box-shadow: 0 2px 6px rgba(0, 0, 0, .2);

        transition: left .3s ease;
    }

    [data-bs-theme="dark"] .progress-indicator {
        background: #fff;
        border-color: #111;
    }

    .progress-labels {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .negative-label {
        color: var(--bs-danger);
    }

    .neutral-label {
        color: var(--bs-warning);
    }

    .positive-label {
        color: var(--bs-success);
    }

    /* =========================
       FOOTER
    ========================= */

    .score-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-top: .9rem;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .footer-status {
        font-weight: 600;
        color: var(--sm-text);
    }

    /* =========================
       EXPLANATION
    ========================= */

    .section-title {
        display: flex;
        align-items: center;
        gap: .75rem;

        font-size: .92rem;
        font-weight: 700;

        margin-bottom: .7rem;
    }

    .icon-circle,
    .tone-icon {
        width: 36px;
        height: 36px;

        border-radius: 10px;

        display: grid;
        place-items: center;

        flex-shrink: 0;
    }

    .icon-circle {
        background: rgba(var(--bs-info-rgb), .15);
        color: var(--bs-info);
    }

    .tone-icon {
        background: rgba(var(--bs-warning-rgb), .15);
        color: var(--bs-warning);
    }

    .explanation-content,
    .tone-description {
        font-size: .84rem;
        line-height: 1.6;
        color: var(--sm-text);
    }

    /* =========================
       TONE CARD
    ========================= */

    .tone-card {
        display: flex;
        align-items: center;
        gap: .8rem;
    }

    .tone-content {
        flex: 1;
    }

    .tone-title {
        margin-bottom: .2rem;
        font-weight: 600;
    }

    /* =========================
       BUTTON
    ========================= */

    .sentiment-close-btn {
        min-width: 115px;

        border: 0;
        border-radius: 12px;

        padding: .58rem 1rem;

        font-size: .83rem;
        font-weight: 600;

        background: var(--bs-dark);
        color: #fff;

        transition: .2s ease;
    }

    .sentiment-close-btn:hover {
        opacity: .92;
        transform: translateY(-1px);
    }

    [data-bs-theme="dark"] .sentiment-close-btn {
        background: var(--bs-light);
        color: var(--bs-dark);
    }

    /* =========================
       MOBILE
    ========================= */

    @media (max-width: 576px) {

        .sentiment-modal .modal-header,
        .sentiment-modal .modal-body,
        .sentiment-modal .modal-footer {
            padding-inline: 1rem;
        }

        .sentiment-title {
            font-size: 1.08rem;
        }

        .emoji-wrap {
            width: 56px;
            height: 56px;
            font-size: 1.7rem;
        }

        .score-value {
            font-size: 1.55rem;
        }

        .score-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .tone-card {
            align-items: flex-start;
        }
    }
    
    .attach-zone {
        border: 1.5px dashed #ccc;
        align-items: center;
        border-radius: 5px;
        padding: 12px 15px;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        background: #fff;
    }

    [data-bs-theme=dark] .attach-zone {
        border: 1.5px dashed #2A2A2D;
        background: #191919;
    }

    .attach-zone:hover {
        border-color: #378ADD;
        background: #f0f7ff;
    }
    [data-bs-theme=dark] .attach-zone:hover {
        border-color: #3f3f42;
        background: #303030;
    }

    .attach-zone.drag-over {
        border-color: #378ADD;
        background: #e6f1fb;
    }

    .attach-zone p ,
    .attach-hint,
    [data-bs-theme=dark]  .file-item .fname,
    [data-bs-theme=dark] #update-status-mdl-frm .note-toolbar .note-btn
    {
        color: #7F7F7F !important;
    }


    .file-list {
        margin-top: 8px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-inline: 0px
    }

    .file-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        padding: 5px 8px;
        background: #f4f4f4;
        border-radius: 4px;
        color: #666;
    }

    [data-bs-theme=dark] .file-item {
        background: #303030;
        color: #7F7F7F;
    }

    .file-item i {
        font-size: 14px;
    }

    .file-item .fname {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #222;
    }

    .file-item .rm {
        border: none;
        background: none;
        cursor: pointer;
        color: #aaa;
        padding: 2px;
        border-radius: 4px;
        line-height: 1;
    }

    .file-item .rm:hover {
        color: #E24B4A;
        background: #fde8e8;
    }

    [data-bs-theme=dark] .file-item .rm:hover {
        color: #fff;
        background: #3a3a3a;
    }

    /* update status message modal */

    #update-status-mdl-frm .note-editor {
        padding-inline: 0px;
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }

    #update-status-mdl-frm .note-editor.note-frame,
    #update-status-mdl-frm .note-editor.note-frame.fullscreen {
        border: 1.5px solid #DFDFE1 !important;
        border-radius: 5px !important;
        overflow: hidden;
        box-shadow: none !important;
        transition: border-color .2s, box-shadow .2s;
    }
    #update-status-mdl-frm .note-statusbar {
        display: none;
    }

    [data-bs-theme=dark] #update-status-mdl-frm .note-editor.note-frame,
    [data-bs-theme=dark] #update-status-mdl-frm .note-editor.note-frame.fullscreen {
        border: 1.5px solid #2A2A2D !important;
    }

    #update-status-mdl-frm .note-editor.note-frame:focus-within {
        border-color: var(--brand) !important;
        box-shadow: 0 0 0 3px var(--brand-light) !important;
    }

    #update-status-mdl-frm .note-editor.note-airframe .note-placeholder,
    .note-editor.note-frame .note-placeholder {
        padding: 14px 16px;
        font-weight: 400;
    }

    #update-status-mdl-frm .note-btn-group.btn-group.note-font {
        padding-right: 10px;
        border-right: 1px solid #2A2A2D;
        border-radius: 0px;
    }
    [data-bs-theme=dark] #update-status-mdl-frm .note-btn-group.btn-group.note-font {
        border-right-color: 1px solid #2A2A2D;
    }

    #update-status-mdl-frm .note-editor.note-frame:focus-within {
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
        outline: none !important;
    }

    #update-status-mdl-frm .note-editable:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    #update-status-mdl-frm .note-toolbar {
        border-bottom: 1.5px solid #DFDFE1 !important;
        padding: .45rem .6rem !important;
        display: flex;
        align-items: center;
        gap: .15rem;
        flex-wrap: wrap;
    }
    [data-bs-theme=dark] #update-status-mdl-frm .note-toolbar {
        border-bottom: 1.5px solid #2A2A2D !important;
    }

    #update-status-mdl-frm .note-toolbar .note-btn {
        background: transparent !important;
        border: none !important;
        border-radius: 6px !important;
        padding: .28rem .42rem !important;
        color: #64748b !important;
        font-size: .8rem !important;
        transition: background .15s, color .15s;
        line-height: 1;
    }

    #update-status-mdl-frm .note-toolbar .note-btn:hover,
    #update-status-mdl-frm .note-toolbar .note-btn.active {
        background: var(--brand-light) !important;
        color: var(--brand) !important;
    }

    #update-status-mdl-frm .note-toolbar .note-btn-group {
        display: flex;
        align-items: center;
    }

    #update-status-mdl-frm .note-toolbar .note-btn-group+.note-btn-group::before {
        content: '';
        display: inline-block;
        width: 1px;
        height: 18px;
        background: var(--border);
        margin: 0 .35rem;
        align-self: center;
    }

    #update-status-mdl-frm .note-style .dropdown-toggle {
        font-weight: 700 !important;
        font-size: .875rem !important;
        letter-spacing: -.01em;
        min-width: 44px;
    }

    #update-status-mdl-frm .note-editable {
        height: 100px !important;
        padding: .85rem 1rem !important;
        font-size: .9rem !important;
        color: var(--text) !important;
        background: var(--white) !important;
        caret-color: var(--brand);
    }

    #update-status-mdl-frm .note-editable:empty::before {
        content: attr(data-placeholder);
        color: var(--muted);
        pointer-events: none;
    }

    #update-status-mdl-frm .note-statusbar {
        background: #fafafa !important;
        border-top: 1px solid var(--border) !important;
    }

    #update-status-mdl-frm .action-row {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        margin-top: 1.25rem;
    }

    #update-status-mdl-frm .btn-cancel {
        padding: .5rem 1.25rem;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: transparent;
        color: #64748b;
        font-size: .875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background .15s, border-color .15s;
    }

    #update-status-mdl-frm .btn-cancel:hover {
        background: var(--bg);
        border-color: #cbd5e1;
    }

    #update-status-mdl-frm .btn-submit {
        padding: .5rem 1.5rem;
        border-radius: 8px;
        border: none;
        background: var(--brand);
        color: #fff;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, transform .1s;
    }

    #update-status-mdl-frm .btn-submit:hover {
        background: #6d28d9;
    }

    #update-status-mdl-frm .btn-submit:active {
        transform: scale(.97);
    }
    .overflow-auto {
        scrollbar-width: thin; /* Firefox ke liye */
    }

    .tkt-more-info-trigger {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        z-index: 10;
        cursor: default;
    }

    .tkt-more-info-popover {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        z-index: 999;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 14px;
        min-width: 200px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        white-space: nowrap;
    }

    .tkt-more-info-trigger:hover .tkt-more-info-popover {
        display: block;
    }


    [data-bs-theme=dark] .tkt-more-info-popover {
        background: #191919;
        border: 1px solid #2A2A2D;
    }

    .tkt-more-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 4px 0;
        font-size: 12px;
    }

    .tkt-more-info-row + .tkt-more-info-row {
        border-top: 1px solid #f3f4f6;
    }
    [data-bs-theme=dark] .tkt-more-info-row + .tkt-more-info-row {
        border-top: 1px solid #2A2A2D;
    }

    .tkt-more-info-label {
        color: #9ca3af;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        min-width: 56px;
    }

    @media (prefers-color-scheme: dark) {
        .tkt-more-info-popover {
            background: #1e1e2e;
            border-color: #374151;
            box-shadow: 0 4px 16px rgba(0,0,0,0.40);
        }
        .tkt-more-info-row + .tkt-more-info-row {
            border-top-color: #374151;
        }
    }

    #ticket-list-page .tkt-card-outer .tkt-body {
        overflow: visible;
    }

    #ticket-list-page .tkt-card-outer .tkt-body .px-3 {
        overflow: visible;
    }

    #ticket-list-page .tkt-card-outer .tkt-body .px-3 > div {
        overflow: visible;
    }

    .tkt-more-info-popover {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        z-index: 9999;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 14px;
        min-width: 200px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        white-space: nowrap;
        pointer-events: none;
    }
    
    /* css for legends */
    .tkt-legend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f9fafb;
        border: 1px solid #00000024;
        border-radius: 8px;
        padding: 4px 6px;
        flex-wrap: wrap;
        max-width: 100%;
    }

    [data-bs-theme=dark] .tkt-legend {
        background: #191919;
        border: 1px solid #2A2A2D;
    }

    .tkt-legend-item {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        cursor: default;
        transition: background 0.15s;
    }

    .tkt-legend-item:hover {
        background: #fff;
        box-shadow: 0 0 0 1px #e5e7eb;
    }

    [data-bs-theme=dark] .tkt-legend-item:hover {
        background: #272727;
        box-shadow: 0 0 0 1px #151414;
    }

    .tkt-legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
        display: inline-block;
        transition: transform 0.15s;
    }

    .tkt-legend-item:hover .tkt-legend-dot {
        transform: scale(1.3);
    }

    .tkt-legend-popover {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        width: 200px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        white-space: normal;
        pointer-events: none;
        text-align: left;
    }

    [data-bs-theme=dark] .tkt-legend-popover {
        background: #191919;
        border: 1px solid #2A2A2D;
    }
    
    @media (max-width: 576px) {
        .tkt-legend {
            gap: 2px;
            padding: 3px 6px;
        }

        .tkt-legend-dot {
            width: 10px;
            height: 10px;
        }

        .tkt-legend-item {
            width: 24px;
            height: 24px;
        }

        .tkt-legend-popover {
            left: 0;
            right: auto;
            transform: none;
            width: 180px;
        }

        .tkt-legend-popover::before {
            left: 10px;
            transform: rotate(45deg);
        }
    }

    [data-bs-theme=dark] .tkt-legend-popover::before {
        background: #191919;
        border-left: 1px solid #2A2A2D;
        border-top: 1px solid #2A2A2D;
    }

    .tkt-legend-item:hover .tkt-legend-popover {
        display: block;
    }

    .tkt-legend-pop-title {
        display: block;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .tkt-legend-popover p {
        margin: 0;
        font-size: 11.5px;
        color: #6b7280;
        line-height: 1.5;
    }
   
    .bulk-action-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .bulk-action-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        cursor: pointer;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
        flex-shrink: 0;
    }
    [data-bs-theme=dark] .bulk-action-trigger {
        border: 1px solid #2A2A2D;
        background: #2a2a2a;
    }

    .bulk-action-wrapper:hover .bulk-action-trigger {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #111827;
    }

    [data-bs-theme=dark] .bulk-action-wrapper:hover .bulk-action-trigger {
        background: #393939;
        border-color: #2A2A2D;
        color: #fff;
    }

    .bulk-action-popover {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        z-index: 9999;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 6px;
        gap: 4px;
        flex-direction: row;
        align-items: center;
        box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        white-space: nowrap;
        min-width: max-content;
    }

    [data-bs-theme=dark] .bulk-action-popover {
        border: 1px solid #2A2A2D;
        background: #191919;
    }

    .bulk-action-popover::before {
        content: '';
        position: absolute;
        top: -5px;
        right: 10px;
        transform: rotate(45deg);
        width: 8px;
        height: 8px;
        background: #fff;
        border-left: 1px solid #e5e7eb;
        border-top: 1px solid #e5e7eb;
    }

    [data-bs-theme=dark] .bulk-action-popover::before {
        background: #191919;
        border-left: 1px solid #2A2A2D;
        border-top: 1px solid #2A2A2D;
    }

    .bulk-action-wrapper:hover .bulk-action-popover {
        display: inline-flex;
    }

    .bulk-action-popover .bukl-action-buttons {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
        flex-shrink: 0;
    }
    [data-bs-theme=dark] .bulk-action-popover .bukl-action-buttons {
        border: 1px solid #2A2A2D;
        background: #2a2a2a;
    }

   .tkt-card-outer a:has(.tkt-title) {
        text-decoration: none;
    }

    .tkt-card-outer .tkt-title {
        color: #515151;
        transition: color 0.15s ease;
    }

    .tkt-card-outer a:hover .tkt-title {
        color: #374151;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    [data-bs-theme=dark] .tkt-card-outer .tkt-title {
        color: #f3f4f6;
    }

    [data-bs-theme=dark] .tkt-card-outer a:hover .tkt-title {
        color: #e5e7eb;
        text-decoration-color: #4b5563;
    }
    #ticket-list-page .card-loader-wrapper{
        position: absolute;
        top:0;
        z-index:999;
        height: 100px;
        display:flex;
        left: 40%;
      
    }

    /* erro */
    .error {
        display: block;
        margin-top: 5px;
        color: #dc3545;
        font-size: 0.875em;
    }

    .bulk-action-wrapper.active .bulk-action-popover{
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .bulk-action-wrapper.active .bulk-action-popover{
            display: flex;
        }

    </style>
@endpush

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('js/common/showdown.min.js') !!}"></script>
    <script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/tickets/ticket-list/ticket.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/simple_pagination/pagination.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/tickets/create_ticket.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/tickets/ticket-list/custom-status.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/customfield.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/form/depends_render.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/flatpicker/js/flatpicker.js') !!}"></script>
    @if(in_array(config('app.client'), ["ltts", "grdemo", "rolepermission"]))
        <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/customform/customform.js') !!}"></script>
    @endif
    @if(in_array(config('app.client'), ["ltsct"]))
        <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/customform/ltsct_customform.js') !!}"></script>
    @endif
    <script>


        $(document).ready(function () {
            var config = {};
            config.csrf = '{{ csrf_token() }}';
            config.ai_enabled = "{{ config('app.ai_enabled') != "" ? 1 : 0 }}";
            config.aiEnabled = "{{ config('app.gemini_ai_key') != "" ? 1 : 0 }}";
            config.ai_enabled = "{{ config('app.ai_enabled') != "" ? 1 : 0 }}";
            config.aiEnabled = "{{ config('app.gemini_ai_key') != "" ? 1 : 0 }}";
            config.main_filter = "{{ $main_filter }}";
            config.sort_dir = { id: 7, dir: 2 };
            config.dashboardCompanyId = "{{ $dashboardCompanyId }}";
            config.client = "{{ config('app.client') }}";
            config.priorities = {!! json_encode($priorities) !!};
            config.user = {!! json_encode(array_merge(Auth::user()->only(["id", "first_name", "last_name", "username", "company_id", "location", "displayName", "employee_num"]), ['seat_no' => Auth::user()->userDetails->seat_no ?? null])) !!};
            config.auth = {!! json_encode(Auth::user()->only("id")) !!};
            config.user.limits = "{{ Auth::user()->hasPermission("service_tickets") }}";
            config.user.download_limits = "{{ Auth::user()->hasPermissionTo('TicketDownload') ? 1 : 0 }}";
            config.user.action_controls = {!! json_encode($action_controls) !!};
            config.permissions = {!! json_encode($permissionArray) !!};
            config.device = {!! json_encode($device) !!};
            config.tech_id = {!! json_encode($techId) !!};
            config.statuses = {!! json_encode($statuses) !!};
            config.statuses1 = {!! json_encode($statuses1) !!};
            config.company_defulte = {!! json_encode($company_id) !!};
            config.company_user_detail = {!! json_encode($userDatail) !!};
            config.tkt_config = {!! json_encode($tkt_config) !!};
            config.created_via = {!! json_encode($created_via) !!};
            config.sort_fields = {!! json_encode($sort_fields) !!};
            config.ticket_handlers = {!! json_encode($ticket_handlers) !!};
            config.created_via_filter = {!! json_encode($created_via_filter) !!};
            config.locations = {!! json_encode($locations) !!};
            config.base_locations = {!! json_encode($base_locations) !!};
            config.tags = {!! json_encode($tags) !!};
            config.companies = {!! json_encode($companies) !!};
            config.taskModule = {{ config('services.task_module.enabled') ? 1 : 0 }};
            config.isTechnician = "{{ isset($isTechnician) ? $isTechnician : false }}";
            config.tag_id = "{{ isset($_GET['tag_id']) ? $_GET['tag_id'] : '' }}";
            config.redirect_status = "{{ $redirect_status }}";
            config.dashboard_filter = "{{ request('dashboard_filter') }}";
            config.dashboard_type = "{{ request('dashboard_type') }}";
            config.dashboard_status = "{{ request('dashboard_status') }}";
            config.dashboard_date = "{{ request('dashboard_date') }}";
            config.dashboard_department_id = "{{ request('dashboard_department_id') }}";
            config.dashboard_date_range = "{{ request('dashboard_date_range') }}";
            config.dashboard_ticket_type = "{{ request('dashboard_ticket_type') }}";
            config.dashboard_priority_id = "{{ request('dashboard_priority_id') }}";
            config.dashboard_source_id = "{{ request('dashboard_source_id') }}";
            config.dashboard_city_id = "{{ request('dashboard_city_id') }}";

            config.url = {
                getStatusByAjax: "{{ url('getStatusByAjax') }}",
                getEnabledAccountsForOptions: "{{ url('getEnabledAccountsForOptions') }}",
                tabs: "{{ url('tabs') }}",
                tabList: "{{ url('tabList') }}",
                editTab: "{{ url('editTab') }}",
                tabDelete: "{{ url('tabDelete') }}",
                updateTab: "{{ url('updateTab') }}",
                fetchccEmailID: "{{ url('tickets/fetch-cc-users-email-id') }}",
                getStatusTabs: "{{ url('tickets/get-status-tabs') }}",
                get_company_by_user_access: "{{ url('getCompanyByUserAccess') }}",
                departments_with_company: "{{ url('tickets/departments') }}",
                problem_categories_by_company: "{{ url('tickets/problem-categories/by-dept') }}",
                getUserByAjax: "{{ url('getUserByQuery') }}",
                getUserDeviceByAjax: "{{ url('getUserDeviceForDropDown') }}",
                getTagDetails: "{{ url('ticket/getTagDetails') }}",
                getUserCCByAjax: "{{ url('getUserCCByQuery') }}",
                addAttachment: "{{ url('ticket/attachment/add') }}",
                removeAttachment: "{{ url('ticket/attachment/remove') }}",
                create_ticket: "{{ url('tickets/create') }}",
                user_basic_info: "{{ url('user/basic-info') }}",
                getCompanyWiseLocation: "{{ url('get-company-wise-location') }}",
                getCompanyWiseLocation: "{{ url('get-company-wise-location') }}",
                getInternalPlaceByAjax: "{{ url('getInternalPlaceByAjax') }}",
                jx_tickets: "{{ $list_url }}",
                base_url: "{{ url('') }}",
            };
            config.url.getCustomFieldNote = "{{ url('getCustomFieldNote') }}";
            config.url.getDepartmentCustomFields = "{{ url('ticket/getDepartmentCustomFields') }}";
            config.url.getQueryComponent = "{{ url('getByCustomDropDown/getComponent') }}";
			config.url.getByQueryDevice = "{{ url('getByCustomDropDown/getDevice') }}";
			config.url.getQueryLocation = "{{ url('getByCustomDropDown/getLocation') }}";
			config.url.getQueryTicket = "{{ url('getByCustomDropDown/getTicket') }}";
			config.url.getQueryUser = "{{ url('getByCustomDropDown/getUser') }}";
			config.url.getQueryPlace = "{{ url('getByCustomDropDown/getPlace') }}";
			config.url.getQueryManufacture = "{{ url('getByCustomDropDown/getManufacture') }}";
			config.url.getQueryModel = "{{ url('getByCustomDropDown/getModel') }}";
			config.url.getQueryTicketProcureRequest = "{{ url('getByCustomDropDown/getTicketProcureRequest') }}";
			config.url.getQueryRecord = "{{ url('getByCustomDropDown/getRecord') }}";
			config.url.getQueryTask = "{{ url('getByCustomDropDown/getTask') }}";
            config.url.getTasks = "{{ url('task-management/ajaxList') }}";
			config.url.getQueryLicense = "{{ url('getByCustomDropDown/getLicense') }}";
			config.url.getQueryProject = "{{ url('getByCustomDropDown/getProject') }}";
			config.url.getQueryPurchase = "{{ url('getByCustomDropDown/getPurchase') }}";
			config.url.getQueryDepartment = "{{ url('getDepartmentsWithCompanyByQuery') }}";
			config.url.getQuerySupplier = "{{ url('getByCustomDropDown/getSupplier') }}";
			config.url.getQueryContract = "{{ url('getByCustomDropDown/getContract') }}";
            config.url.requested_form = "{{ url('requested_form') }}";
            config.url.service_request_form = "{{ url('tickets/serviceRequestForm') }}";
            config.url.ticket_getcustomview ="{{ url('tickets/ticket_getcustomview') }}";
            config.url.fetch_active_ticket = "{{ url('tickets/fetch-active-access') }}";
            config.url.getLocationByAjax = "{{ url('getLocationByQuery') }}";
            config.url.getUserByQueryForCustomForm = "{{ url('getUserByQueryForCustomForm') }}";
            config.url.getCategoryByAjax = "{{ url('getCategoryByQuery') }}";
            config.url.getDataForCustomFormBaseOnType = "{{ url('getDataForCustomFormBaseOnType') }}";
            config.url.fetchAvailable = "{{ url('fetchAvailableItem') }}";
            config.url.getUser = "{{ url('user/getUserForEmpdetail') }}";
            config.url.departments_based_on_privilage = "{{ url('departments/by-company/privilage') }}";
            config.url.service_types_by_company = "{{ url('tickets/service-types/by-dept') }}";
            config.url.getDeviceFilterByAjax = "{{ url('getDeviceForTicketDropDown') }}";
            config.token = "{{ csrf_token() }}";
            config.url.current_page = "{{ url('ticket/') }}";
            config.url.view_ticket = "{{ url('ticket') }}";
            config.url.getDepartmentAndCategories = "{{ url('task-management/get-department-category')}}";
            config.url.sub_category = "{{ url('tickets/fetch-sub-category') }}";
            config.url.addTask = "{{ url('task-management/ajaxAddTask') }}";
            config.url.getRelatedTask = "{{ url('tickets/get-relevant-tasks') }}";
            config.url.create_ticket_by_user = "{{ url('tickets/create-by-user') }}";
            config.url.getTechCurrentStatusById = "{{url('technician/get-tech-curren-status-id')}}";
            config.url.get_data_for_transfer = "{{ url('ticket/get-data-for-transfer') }}";
            config.url.get_users_to_assign_by_dep_by_availability = "{{ url('ticket/get_users_to_assign_by_dep_by_availability') }}";
            config.url.editTicket = "{{ url('ticket/edit') }}";
            config.url.transfer = "{{ url('ticket/transfer') }}";
            config.url.getCustomFieldsValueForFilter = "{{ url('ticket/getCustomFieldsValueForFilter') }}";
            config.url.customFieldsForFilter = "{{ url('ticket/getCustomFieldsForFilter') }}";
            config.url.get_users_to_assign_by_dep = "{{ url('ticket/get_users_to_assign_by_dep') }}";
            config.url.staring = "{{ url('ticket/staring') }}";
            config.url.delete = "{{ url('ticket/delete') }}";
            config.url.delete_multiple = "{{ url('ticket/delete_multiple_ticket') }}";
            config.url.get_tickets = "{{ url('tickets/list/jx-ticket-detail') }}";
            config.url.ticket_history = "{{ url('ticket/ticket_history') }}";
            config.url.user_info = "{{ url('user/info') }}";
            config.url.task_info = "{{url('task-management/info')}}";
            config.url.device_info = "{{ url('device/info') }}";
            config.url.getSentiment = "{{ url('ticket-getSentiment') }}";
            config.url.updateTicketsStatusDropDownUpdateAsPerTicketType = "{{ route('getStatusByTicketType') }}";
            config.url.getStatusByAjaxForUpdateStatus = "{{ url('get-status-update') }}";
            config.url.getStatusApproval = "{{ url('get-status-approval') }}";
            config.url.service_request_form = "{{ url('tickets/serviceRequestForm') }}";
            config.url.getFormByTicketProblemCategory = "{{ url('get-status-form') }}";
            config.url.update_status = "{{ url('ticket/update_status') }}";
            config.url.resolved_multiple ="{{ url('ticket/resolved_multiple_ticket') }}";
            config.url.bulkAssignList = "{{ url('tickets/ajaxBulkAssignTicketList') }}";
            config.url.userTicketHandlerList = "{{ url('user-ticket-handler-list') }}";
            config.url.bulkAssignTicket = "{{ url('tickets/bulkAssign') }}";
            config.url.merge_tickets = "{{ url('tickets/merge_tickets') }}";
            config.url.get_timeline = "{{ url('ticket/get_timeline') }}";
            config.url.get_articles = "{{ url('tickets/list/jx-article-detail') }}";
            config.url.getRelatedDocuments = "{{ url('kd/getRelatedDocuments') }}";
            config.url.articleImagePath = "{{ url('uploads/article') }}";
            config.url.view_article = "{{ url('knowledge_document/article/view') }}";
            config.url.export_tickets = "{{ url('tickets/export') }}";
            config.url.feedbackImageBase = "{{ asset('images/emo/') }}";
            config.url.requestInfo = "{{ url('tickets/requestInfo') }}";
            config.translations = {
                deleteListItem: "{{ __('ticket.delete-list-item') }}",
                somethingWentWrong: "{{ __('ticket.something_went_wrong') }}",
                creator: "{{ __('ticket.ticket_list.creator') }}",
                department: "{{ __('ticket.create_ticket.department') }}",
                priority: "{{ __('ticket.create_ticket.priority') }}",
                ticket_type: "{{ __('ticket.ticket_list.ticket_type') }}",
                created_at: "{{ __('ticket.ticket_list.created_at') }}",
                updated_at: "{{ __('ticket.ticket_list.updated_at') }}",
                assign_to: "{{ __('ticket.ticket_list.assign_to') }}",
                assign_at: "{{ __('ticket.ticket_list.assign_at') }}",
                transfer: "{{ __('ticket.ticket_list.transfer') }}",
                edit: "{{ __('ticket.ticket_list.edit') }}",
                ai: "{{ __('ticket.ticket_list.ai') }}",
                analysis: "{{ __('ticket.ticket_list.analysis') }}",
                sentiment_analysis: "{{ __('ticket.update_status.sentiment_analysis') }}",
                sentiment_analysis: "{{ __('ticket.update_status.sentiment_analysis') }}",
                view: "{{ __('ticket.ticket_list.view') }}",
                update_status: "{{ __('ticket.ticket_list.update_status') }}",
                status: "{{ __('ticket.status') }}",
                delete: "{{ __('ticket.ticket_list.delete') }}",
                star: "{{ __('ticket.ticket_list.star') }}",
                history: "{{ __('ticket.ticket_list.history') }}",
                select_status: "{{ __('ticket.ticket_list.select_status') }}",
                filter_by_creator_logger: "{{ __('ticket.ticket_list.filter_by_creator_logger') }}",
                filter_by_department: "{{ __('ticket.ticket_list.filter_by_department') }}",
                filter_by_problem_category: "{{ __('ticket.ticket_list.filter_by_problem_category') }}",
                filter_by_sub_category: "{{ __('ticket.ticket_list.filter_by_sub_category') }}",
                filter_by_priority: "{{ __('ticket.ticket_list.filter_by_priority') }}",
                filter_by_tag: "{{ __('ticket.ticket_list.filter_by_tag') }}",
                filter_created_via: "{{ __('ticket.ticket_list.filter_created_via') }}",
                filter_by_location: "{{ __('ticket.ticket_list.filter_by_location') }}",
                filter_by_base_location: "{{ __('ticket.ticket_list.filter_by_base_location') }}",
                filter_by_ticket_or_service_request: "{{ __('ticket.ticket_list.filter_by_ticket_or_service_request') }}",
                filter_by_vip_tickets: "{{ __('ticket.ticket_list.filter_by_vip_tickets') }}",
                filter_by_sla_breached: "{{ __('ticket.ticket_list.filter_by_sla_breached') }}",
                filter_by_feedback: "{{ __('ticket.ticket_list.filter_by_feedback') }}",
                select_ticket_type: "{{ __('ticket.ticket_list.select_ticket_type') }}",
                filter_by_merge: "{{ __('ticket.ticket_list.filter_by_merge') }}",
                custom_field: "{{ __('ticket.ticket_list.custom_field') }}",
                custom_field_value: "{{ __('ticket.ticket_list.custom_field_value') }}",
                filter_by_star: "{{ __('ticket.ticket_list.filter_by_star') }}",
                filter_based_on: "{{ __('ticket.ticket_list.filter_based_on') }}",
                filter_by_ticket_handler: "{{ __('ticket.ticket_list.filter_by_ticket_handler') }}",
                filter_by_task: "{{ __('ticket.ticket_list.filter_by_task') }}",
                select_the_user: "{{ __('ticket.ticket_list.select_the_user') }}",
                select_device: "{{ __('ticket.ticket_list.select_device') }}",
                custom_field_set: "{{ __('ticket.ticket_list.custom_field_set') }}",
                enter_your_message: "{{ __('ticket.create_ticket.enter_your_message') }}",
                no_filter: "{{ __('ticket.tkt_filters.no_filter') }}",
                select_department: "{{ __('ticket.ticket_list.select_department') }}",
                notes: "{{ __('ticket.create_ticket.notes') }}",
                select_problem_category: "{{ __('ticket.ticket_list.select_problem_category') }}",
                select_company: "{{ __('ticket.ticket_list.select_company') }}",
                select_location: "{{ __('ticket.ticket_list.select_location') }}",
                select_priority: "{{ __('ticket.ticket_list.select_priority') }}",
                select_reply_to_account: "{{ __('ticket.ticket_list.select_reply_to_account') }}",
                select_tags: "{{ __('ticket.ticket_list.select_tags') }}",
                add_cc: "{{ __('ticket.ticket_list.add_cc') }}",
                please_enter_a_valid_email_address: "{{ __('ticket.ticket_list.please_enter_a_valid_email_address') }}",
                select_sub_category: "{{ __('ticket.ticket_list.select_sub_category') }}",
                employee_code: "{{ __('ticket.create_ticket.employee_code') }}",
                designation: "{{ __('ticket.create_ticket.designation') }}",
                location: "{{ __('ticket.create_ticket.location') }}",
                base_location: "{{ __('ticket.create_ticket.base_location') }}",
                ext_user_company: "{{ __('ticket.create_ticket.ext_user_company') }}",
                mobile: "{{ __('ticket.create_ticket.mobile') }}",
                email: "{{ __('ticket.create_ticket.email') }}",
                company: "{{ __('ticket.create_ticket.company') }}",
                enter_starting: "{{ __('ticket.create_ticket.enter_starting') }}",
                previous: "{{ __('ticket.update_status.previous') }}",
                next: "{{ __('ticket.update_status.next') }}",
                portal: "{{ __('ticket.update_status.portal') }}",
                mobile_app: "{{ __('ticket.update_status.mobile_app') }}",
                email: "{{ __('ticket.update_status.email') }}",
                whatsApp: "{{ __('ticket.update_status.whatsApp') }}",
                bot: "{{ __('ticket.update_status.bot') }}",
                call: "{{ __('ticket.update_status.call') }}",
                tecnician_is_available_or_not: "{{ __('ticket.transfer_ticket.tecnician_is_available_or_not') }}",
                tecnician_is_available: "{{ __('ticket.transfer_ticket.tecnician_is_available') }}",
                unable_to_load_form: "{{ __('ticket.transfer_ticket.unable_to_load_form') }}",
                transfer_ticket: "{{ __('ticket.transfer_ticket.transfer_ticket') }}",
                add_star: "{{ __('ticket.update_status.add_star') }}",
                are_you_sure_you_want: "{{ __('ticket.update_status.are_you_sure_you_want') }}",
                are_you_sure_you_want_to_marked_starred: "{{ __('ticket.update_status.are_you_sure_you_want_to_marked_starred') }}",
                are_you_delete: "{{ trans('ticket.are_you_delete') }}",
                atleast_resolve: "{{ trans('ticket.atleast_resolve')}}",
                atleast_assign: "{{ trans('ticket.atleast_assign')}}",
                convert_kd_by_ai: "{{ trans('ticket.convert_kd_by_ai')}}",
                convert_kd: "{{ trans('ticket.convert_kd')}}",
                confirm_ticket:"{{trans('ticket.create_ticket.confirm_ticket')}}",
                create:"{{trans('button.create')}}",
                close: "{{trans('button.close')}}",
                select_device:"{{trans('ticket.ticket_detail.select_device')}}",
                tat: "{{ trans('ticket.ticket_detail.tat') }}",
                tat:"{{trans('ticket.create_ticket.tat')}}",
                via: "{{trans('ticket.ticket_list.via')}}",
                location:"{{trans('ticket.ticket_list.location')}}",
                create_ticket:"{{trans('ticket.create_ticket.create_ticket')}}",
                add_view_tab:"{{trans('ticket.custom-status-tab')}}",
                edit_tab_name:"{{trans('ticket.edit-tab-name')}}",
            };
            @if (isset($_REQUEST['redirect']))
                config.dashboardTicketCreateRedirect = true;
            @else
                config.dashboardTicketCreateRedirect = false;
            @endif
            @if (isset($_REQUEST['filter']))
                config.filterFromReport = "{{$_REQUEST['filter']}}";
            @else
                config.filterFromReport = null;
            @endif
            new Ticket(config);
            new CreateTicket(config);
            @if(in_array(config('app.client'), ["ltts", "grdemo", "rolepermission"]))
                    new CustomForm(config);
            @endif
            @if(in_array(config('app.client'), ["ltsct"]))
                new CustomLTSCTForm(config);
            @endif

           
        });
    </script>
@endpush