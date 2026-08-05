{{-- @page-meta
{
  "page_no": "SRQ-01",
  "file": "request-list.blade.php",
  "versions": [
    {
      "version": "1.",
      "writer": "Priya Maru",
      "from": "2026-05-14",
      "reviewer": null,
      "description": "Service Requests list make it responsive"
    }
  ]
}
--}}

@extends('layouts.layout1')
@section('title', $title)
@section('content')

{{-- ══════════════════════════════════════════════════════
     ① SECTION HEADER
     .header-actions-wrapper → index.js sidebar shift
     ══════════════════════════════════════════════════════ --}}
<div id="sr-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ $title }}</h3>
        <div class="srq-toolbar">
            {{-- Filter --}}
            <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip"
                title="{{ trans('service_ticket.filter') }}">
                <svg viewBox="0 0 20 18" fill="none">
                    <path
                        d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                        fill="currentColor" />
                </svg>
                <span class="b6-text opacity-50">{{ trans('service_ticket.filter') }}</span>
                <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
            </button>

            {{-- Sort dropdown --}}
            <div data-bs-toggle="tooltip" title="{{ trans('service_ticket.sort') }}">
                <div class="header-icon-btn dropdown sort-buttons" id="srqSortDrop">
                    <span class="sortbtns dir-sort" data-id="7" data-dir="desc">
                        <svg id="sortAscSvg" class="hide" width="18px" height="18px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="Edit / Sort_Ascending">
                            <path id="Vector" d="M4 17H16M4 12H13M4 7H10M18 13V5M18 5L21 8M18 5L15 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                        </svg>
                        <svg id="sortDescSvg" width="18px" height="18px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="Edit / Sort_Descending">
                            <path id="Vector" d="M4 17H10M4 12H13M18 11V19M18 19L21 16M18 19L15 16M4 7H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                        </svg>
                    </span>
                    <span class="sortbtns dropdown-toggle">
                        <i class="caret" style="color:#808080"></i>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-right list-group sort-fields p-0"></ul>
                </div>
            </div>
            
            {{-- Rocket/AI --}}
            @if(in_array('ServiceRequestBulkApproval', $permissionArray) && $main_filter != 'myRequest')
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-blk-decision" type="button" data-bs-toggle="tooltip" title="{{ trans('service_ticket.bulk_decision') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                        <path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                        <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                    </svg>
                </button>
            @endif

            {{-- Download --}}
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-download" type="button"
                data-bs-toggle="tooltip" title="{{ trans('service_ticket.download') }}">
                <svg viewBox="0 0 18 18" fill="none">
                    <path
                        d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                        fill="currentColor" />
                </svg>
            </button>

            {{-- Add Ticket --}}
             <button class="amg-btn amg-btn-primary amg-btn-sm" id="btnCreateTicket" type="button" data-bs-toggle="tooltip"
                title="{{ trans('service_ticket.add_ticket') }}">
                <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                    <path
                        d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                        fill="currentColor" />
                </svg>
                <span>{{ trans('service_ticket.add_ticket') }}</span>
            </button>
        </div>
    </div>
    {{-- ══════════════════════════════════════════════════════
     ② MAIN CONTENT
     .main-content → index.js margin-left shift
     ══════════════════════════════════════════════════════ --}}
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        @if ($main_filter != 'myRequest')
                            <label class="amg-select-all-checkbox">
                                <input type="checkbox" id="selectAll">
                                <span>{{ trans('service_ticket.select_all') }}</span>
                            </label>
                        @endif
                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown amgTablePageLenth" id="pageLimiter">
                                <option value="10" selected>Show (10)</option>
                                <option value="25">Show (25)</option>
                                <option value="50">Show (50)</option>
                                <option value="100">Show (100)</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="16" height="16" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input sr-list-search" placeholder="{{ trans('service_ticket.search') }}">
                            </div>
                        </div>

                        <button class="amg-refresh-btn btn-reload-list btn-reload-list" title="{{ trans('service_ticket.refresh') }}">
                            <svg class="amg-refresh-btn__icon" width="16" height="16" viewBox="0 0 20 16"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('service_ticket.refresh') }}</span>
                        </button>
                    </div>
                    <div class="srq-body mt-3"></div>
                    <div class="table-footer">
                        <div id="page-btm-summary" class="page-summary"></div>
                        <div id="pagebtns" class="pagination-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>  
    @include('tickets.service-request.sr-filter-modal')
    @include('tickets.ticket-list.create-ticket')
    @include("tickets.fields_info")
    @include("tickets.form_modal")
    @include("tickets.service-request.bulk_decision_modal")
    @include("tickets.kd_suggestion")
</div>
@endsection
@push('css')
    <link href="{!! CommonHelper::asset('plugins/simple_pagination/pagination.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <style>
        #sr-list-wrapper .error {
            display: block;
            margin-top: 5px;
            color: #dc3545;
            font-size: 0.875em;
        }
        /* ── Header right: toolbar ────────────────────────────── */
        .srq-toolbar {
            display    : flex;
            align-items: center;
            gap        : 8px;
            flex-wrap  : nowrap;
            flex-shrink: 0;
        }

        /* Search input */
        .srq-search {
            display    : flex;
            align-items: center;
            gap        : 6px;
            background : var(--app-surface, var(--bs-body-bg));
            border     : 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding    : 5px 12px;
            width      : 180px;
            transition : border-color .18s;
        }
        .srq-search:focus-within { border-color: #93c5fd; }
        .srq-search svg { width:14px; height:14px; color:var(--bs-secondary-color); flex-shrink:0; }
        .srq-search input {
            border:none; outline:none; background:transparent;
            font-size:12.5px; color:var(--bs-body-color); width:100%;
            font-family:inherit;
        }
        .srq-search input::placeholder { color:var(--bs-secondary-color); opacity:.7; }

        /* Toolbar icon buttons (Filter, Sort, Select All, etc.) */
        .srq-tb-btn {
            display    : inline-flex;
            align-items: center;
            gap        : 5px;
            background : var(--app-surface, var(--bs-body-bg));
            border     : 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding    : 5px 10px;
            font-size  : 12.5px;
            font-family: inherit;
            color      : var(--bs-body-color) !important;
            cursor     : pointer;
            white-space: nowrap;
            transition : background .15s;
        }
        .srq-tb-btn:hover { background: var(--bs-tertiary-bg); }
        .srq-tb-btn svg   { width:14px; height:14px; color:var(--bs-secondary-color); flex-shrink:0; }

        /* Icon-only toolbar buttons */
        .srq-tb-icon {
            display    : inline-flex;
            align-items: center;
            justify-content: center;
            background : var(--app-surface, var(--bs-body-bg));
            border     : 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding    : 6px 8px;
            cursor     : pointer;
            transition : background .15s;
        }
        .srq-tb-icon:hover { background: var(--bs-tertiary-bg); }
        .srq-tb-icon svg   { width:14px; height:14px; color:var(--bs-secondary-color); display:block; }

        /* Add Ticket button — red with + icon */
        .srq-btn-add {
            display    : inline-flex;
            align-items: center;
            gap        : 6px;
            background : #ef4444;
            color      : #fff !important;
            border     : none;
            border-radius: 8px;
            padding    : 7px 16px;
            font-size  : 13px;
            font-weight: 500;
            font-family: inherit;
            cursor     : pointer;
            white-space: nowrap;
            transition : background .15s;
        }
        .srq-btn-add:hover { background:#dc2626; }
        .srq-btn-add svg   { width:14px; height:14px; flex-shrink:0; }

        /* ── Request row card ─────────────────────────────────── */
        .srq-row-card {
            background: var(--app-surface, var(--bs-body-bg));
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            margin-bottom: 8px;
            display: flex;
            overflow: hidden;
            transition: box-shadow .18s;
            box-shadow: 0px 0px 6px 0px #0000001F;
        }
        .srq-row-card:hover {
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;    
        }

        [data-bs-theme="dark"] .srq-row-card {
            border    : 1px solid #2a2a2d;
            box-shadow: 0px 0px 6px 0px #0000001F;
            background: #2a2a2a !important;
        }

        [data-bs-theme="dark"] .srq-row-card:hover {
            box-shadow: 0 12px 35px rgba(29, 8, 8, 0.85);;
        }

        /* Left section of row */
        .srq-row-left {
            flex: 1; 
            min-width: 0; 
            padding: 10px 14px;
            border-right: 1px solid var(--bs-border-color);
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        [data-bs-theme="dark"] .srq-row-left {
            border-right: 1px solid rgba(99,149,235,0.2);
        }

        /* Row title line */
        .srq-row-title-line {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .srq-row-title {
            font-size  : 13.5px;
            font-weight: 600;
            color      : var(--bs-body-color);
            white-space: nowrap;
            overflow   : hidden;
            text-overflow: ellipsis;
        }

        /* Ticket ID badge — blue outlined pill */
        .srq-id-badge {
            display      : inline-flex;
            align-items  : center;
            font-size    : 11px;
            font-weight  : 500;
            color        : #2563eb !important;
            background   : #eff6ff;
            border       : 1px solid #bfdbfe;
            border-radius: 5px;
            padding      : 1px 8px;
            white-space  : nowrap;
        }
        [data-bs-theme="dark"] .srq-id-badge { background:#1e2d4a; color:#93c5fd !important; border-color:#1d4ed8; }

        /* Checkbox */
        .srq-checkbox {
            width:16px; height:16px; border-radius:4px;
            border:2px solid var(--bs-border-color);
            flex-shrink:0; cursor:pointer; accent-color:#ef4444;
        }

        /* Row meta line */
       .srq-row-meta {
            display: flex;
            flex-wrap: wrap; /* Important */
            gap: 10px;
            align-items: center;
        }
        .srq-meta-item {
            display      : flex;
            align-items  : center;
            gap: 4px;
            min-width: 0;
            font-size    : 12px;
            color        : var(--bs-secondary-color);
            white-space  : nowrap;
            flex-shrink  : 0;
            padding      : 0 12px;
            border-right : 2px solid var(--bs-border-color);
        }
        .srq-meta-item:first-child { padding-left: 0; }
        .srq-meta-item svg { width:12px; height:12px; flex-shrink:0; color:var(--bs-secondary-color); }

        [data-bs-theme="dark"] .srq-meta-item {
            border-right: 2px solid rgba(255,255,255,0.1);
        }

        /* Avatar in meta */
        .srq-av {
            width        : 20px; height:20px; border-radius:50%;
            display      : inline-flex; align-items:center; justify-content:center;
            font-size    : 7px; font-weight:700; color:#fff !important;
            flex-shrink  : 0;
            background   : linear-gradient(135deg,#6366f1,#4338ca);
        }

        /* Approval text */
        .srq-approval {
            font-size    : 12px;
            color        : var(--bs-secondary-color);
            padding      : 0 12px;
            border-right : 2px solid var(--bs-border-color);
            white-space  : nowrap;
            flex-shrink  : 0;
        }
        .srq-approval span { color:var(--bs-body-color); font-weight:500; }

        [data-bs-theme="dark"] .srq-approval {
            border-right: 2px solid rgba(255,255,255,0.1);
        }

        /* View Details button */
        .srq-btn-view {
            display    : inline-flex;
            align-items: center;
            background : #ef4444;
            color      : #fff !important;
            border     : none;
            border-radius: 6px;
            padding    : 6px 14px;
            font-size  : 12px;
            font-weight: 500;
            font-family: inherit;
            cursor     : pointer;
            white-space: nowrap;
            transition : background .15s;
            flex-shrink: 0;
            margin-left: 12px;
        }
        .srq-btn-view:hover { background:#dc2626; }

        /* Right section of row */
       .srq-row-right {
            width: 310px; 
            flex-shrink: 0; 
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
        }
        [data-bs-theme="dark"] .srq-row-right {
            background: rgba(255,255,255,.03);
        }

        .srq-row-right-item {
            display    : flex;
            align-items: center;
            gap        : 6px;
            font-size  : 12px;
            flex-wrap  : nowrap;
            overflow   : hidden;
        }
        .srq-right-label {
            display    : flex;
            align-items: center;
            gap        : 4px;
            color      : var(--bs-secondary-color);
            min-width  : 105px;
            flex-shrink: 0;
            font-size  : 12px;
            white-space: nowrap;
        }
        .srq-right-label svg { width:12px; height:12px; color:var(--bs-secondary-color); flex-shrink:0; }
        .srq-right-val {
            color      : var(--bs-body-color);
            font-size  : 12px;
            white-space: nowrap;
            overflow   : hidden;
            text-overflow: ellipsis;
        }

        /* Status badge — green outlined pill */
        .srq-status-badge {
            display      : inline-flex;
            align-items  : center;
            font-size    : 11px;
            font-weight  : 500;
            color        : #16a34a !important;
            background   : #f0fdf4;
            border       : 1px solid #bbf7d0;
            border-radius: 5px;
            padding      : 2px 10px;
            white-space  : nowrap;
        }
        [data-bs-theme="dark"] .srq-status-badge { background:#052e16; color:#4ade80 !important; border-color:#166534; }

        /* ── Footer bar — fixed at bottom ────────────────────── */
        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 4px 4px 4px;
            margin-top: 8px;
            border-top: 1px solid var(--bs-border-color);
            flex-wrap: wrap;
            gap: 10px;
        }

        .srq-record-count,
        #page-btm-summary,
        .page-summary {
            font-size: 12.5px;
            color: var(--bs-secondary-color, #6c757d);
            white-space: nowrap;
        }

        #pagebtns,
        .pagination-container {
            display: inline-block;
            margin: 0;
            padding: 0;
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
        }

        #pagebtns ul li a:hover:not(.disabled):not(.active),
        .pagination-container ul li a:hover:not(.disabled):not(.active) {
            background: #e3e7ec;
        }

        #pagebtns ul li.active a,
        #pagebtns ul li.active span,
        .pagination-container ul li.active a,
        .pagination-container ul li.active span {
            background: #8f949d;
            color: #fff !important;
            border-color: #8f949d !important;
            min-width: 42px;
            padding: 0 14px;
        }

        #pagebtns ul li.disabled a,
        #pagebtns ul li.disabled span,
        .pagination-container ul li.disabled a,
        .pagination-container ul li.disabled span {
            opacity: 0.65;
            cursor: not-allowed;
            pointer-events: none;
        }

        #pagebtns ul li a svg,
        .pagination-container ul li a svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }
        /* ── Dropdown wrapper ─────────────────────────────────── */
        .srq-dropdown { position:relative; display:inline-flex; }

        .srq-dropdown-menu {
            display      : none;
            position     : absolute;
            top          : calc(100% + 6px);
            left         : 0;
            min-width    : 180px;
            background   : var(--app-surface, var(--bs-body-bg));
            border       : 1px solid var(--bs-border-color);
            border-radius: 10px;
            box-shadow   : 0 8px 24px rgba(0,0,0,.12);
            z-index      : 1055;
            overflow     : hidden;
            animation    : srqDropIn .15s ease;
        }
        [data-bs-theme="dark"] .srq-dropdown-menu { box-shadow:0 8px 24px rgba(0,0,0,.4); }
        .srq-dropdown-menu.srq-open { display:block; }

        @keyframes srqDropIn {
            from { opacity:0; transform:translateY(-6px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .srq-drop-header {
            font-size     : 10.5px;
            font-weight   : 600;
            color         : var(--bs-secondary-color);
            text-transform: uppercase;
            letter-spacing: .06em;
            padding       : 10px 14px 5px;
        }
        .srq-drop-item {
            display    : flex;
            align-items: center;
            justify-content: space-between;
            padding    : 8px 14px;
            font-size  : 12.5px;
            color      : var(--bs-body-color) !important;
            cursor     : pointer;
            border     : none;
            background : none;
            width      : 100%;
            text-align : left;
            font-family: inherit;
            transition : background .12s;
            gap        : 8px;
        }
        .srq-drop-item:hover   { background: var(--bs-tertiary-bg); }
        .srq-drop-item.srq-active { color:#2563eb !important; font-weight:500; }
        .srq-drop-item-left    { display:flex; align-items:center; gap:8px; }
        .srq-drop-item svg     { width:13px; height:13px; flex-shrink:0; color:var(--bs-secondary-color); }
        .srq-drop-item.srq-active svg { color:#2563eb; }
        .srq-drop-tick         { width:14px; height:14px; color:#2563eb; flex-shrink:0; }
        .srq-drop-divider      { height:1px; background:var(--bs-border-color); margin:4px 0; }

        @media (max-width: 991px) {

            .card-body > .d-flex.align-items-center.gap-2.mb-1{
                flex-wrap: wrap;
                gap: 10px !important;
            }

            .amg-select-all-checkbox{
                order: 1;
            }

            .col-auto{
                order: 2;
            }

            .flex-grow-1{
                display: none;
            }

            .amg-list-searchbar,
            .amg-list-searchbar__input {
                width: 100%;
            }

            .btn-reload-list {
                order: 4;
                margin-left: auto;
            }
        }
        @media (max-width: 767px) {

            .card-body > .d-flex.align-items-center.gap-2.mb-1 {
                display: grid !important;
                grid-template-columns: 1fr;
                align-items: stretch !important;
            }

            .amg-select-all-checkbox {
                width: fit-content;
            }

            .col-auto,
            #pageLimiter,
            .amg-list-searchbar {
                width: 100%;
            }

            .btn-reload-list{
                width: 100%;
                justify-content: center;
                margin-left: 0;
            }

            .btn-reload-list span {
                display: inline-block !important;
            }

            .srq-body {
                padding: 10px;
            }

            .srq-row-card {
                flex-direction: column;
            }

            .srq-row-left {
                border-right: none;
                border-bottom: 1px solid var(--bs-border-color);
            }

            .srq-row-right {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .srq-row-meta {
                flex-wrap: wrap;
                gap: 8px;
            }
        }

        @media (max-width: 576px) {

            .card-body {
                padding: 12px;
            }

            .amg-list-searchbar__input {
                font-size: 13px;
            }

            .btn-reload-list,
            .amg-table-pagination-dropdown {
                height: 40px;
            }

            .srq-row-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .srq-row-title-line {
                flex-wrap: wrap;
            }

            .srq-meta-item {
                width: 100%;
            }
        }
        @media (max-width: 1400px) {

            .srq-row-meta {
                flex-wrap: wrap;
            }

            .srq-meta-item {
                min-width: 0;
            }
        }
        .amg-select-all-checkbox {
            height: 30px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff !important;
            cursor: pointer;
            user-select: none;
            font-size: 13px;
            font-weight: 400;
            color: #00000070;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, .06);
            transition: background .15s;
        }
        [data-bs-theme="dark"] body .amg-select-all-checkbox {
            background   : #2a2a2a !important;
            border       : 1px solid #2a2a2a  !important;
            box-shadow   : none !important;
            height       : -webkit-fill-available
        }
        .amg-select-all-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #ef4444;
            cursor: pointer;
            margin: 0;
        }
        html[data-bs-theme="dark"] body label.amg-select-all-checkbox span {
            color: #959595 !important;
        }
        .dot-loader{
            display:flex;
            justify-content:center;
            align-items:center;
            gap:12px;
            padding:35px 0;
        }

        .dot-loader span{
            width:13px;
            height:13px;
            border-radius:50%;
            background:#2f80ed;
            display:block;
            animation:dotPulse 0.6s infinite alternate ease-in-out;
            box-shadow:0 2px 8px rgba(47,128,237,0.35);
        }

        .dot-loader span:nth-child(2){
            animation-delay:0.2s;
        }

        .dot-loader span:nth-child(3){
            animation-delay:0.4s;
        }

        @keyframes dotPulse{
            from{
                transform:scale(0.8);
                opacity:0.5;
            }
            to{
                transform:scale(1.2);
                opacity:1;
            }
        }
    
        /* selected sort field */
        .sort-fields .list-group-item.selected .like-radio{
            background: #0d6efd;
            border-color: #0d6efd;
        }

        .sort-fields .list-group-item {
            position: relative;
            align-items: center;
            gap: 4px;
            display: flex;
            padding: var(--bs-list-group-item-padding-y) var(--bs-list-group-item-padding-x);
            color: var(--bs-list-group-color);
            text-decoration: none;
            background-color: var(--bs-list-group-bg);
            border: var(--bs-list-group-border-width) solid var(--bs-list-group-border-color);
        }

        .like-radio{
            width: 14px;
            height: 14px;
            border: 2px solid #b5b5b5;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            flex-shrink: 0;
        }

        .like-radio::after{
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    
       .sort-buttons .dropdown-menu {
            display: none;
            position: absolute;
            z-index: 9999;
        }
        .sort-buttons.open .dropdown-menu {
            display: block;
        }
        .srq-tooltip-text {
            max-width: 150px; 
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            vertical-align: middle;
        }

    </style>
@endpush

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/simple_pagination/pagination.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/create_ticket.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/customfield.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script src="{{ CommonHelper::asset('js/drag-drap.js') }}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/form/depends_render.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/serviceRequest/request.js') !!}"></script>
    @if(in_array(config('app.client'), ["ltts", "grdemo", "rolepermission"]))
           <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/customform/customform.js') !!}"></script>
    @endif
    @if(in_array(config('app.client'), ["ltsct"]))
        <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/customform/ltsct_customform.js') !!}"></script>
    @endif
    <script type="text/javascript">
        $(document).ready(function() {
            var config = new Object;
            config.url = new Object;
            config.created_by = "{{ isset($_GET['created_by']) ? $_GET['created_by'] : '' }}";
			config.pab_id = "{{ isset($_GET['pab_id']) ? $_GET['pab_id'] : '' }}";
			config.approval_id = "{{ isset($_GET['approval_id']) ? $_GET['approval_id'] : '' }}";
            config.main_filter = "{{ $main_filter }}";
            config.token = "{{ csrf_token() }}";
            config.statuses = {!! json_encode($vd->statuses) !!};
            config.pabs = {!! json_encode($vd->pabs) !!};
            config.sort_fields = {!! json_encode($sort_fields) !!};
            config.company_defulte = {!! json_encode($company_id) !!};
            config.company_user_detail = {!! json_encode($userDatail) !!};
            config.user = {!! json_encode(array_merge(Auth::user()->only(["id", "first_name", "last_name", "username", "company_id", "location", "displayName", "employee_num"]), ['seat_no' => Auth::user()->userDetails->seat_no ?? null])) !!};
            config.client = "{{ config('app.client') }}";
            config.priorities = {!! json_encode($priorities) !!};
			config.created_via = {!! json_encode($created_via) !!};
            config.auth= {!! json_encode(Auth::user()->only("id")) !!};
            config.user.action_controls = {!! json_encode($action_controls) !!};
            config.tkt_config = {!! json_encode($tkt_config) !!};
            config.permissions = {!! json_encode($permissionArray) !!};
            config.user.limits = "{{ Auth::user()->hasPermission("service_tickets") }}";
            config.isTechnician = "{{ isset($isTechnician) ? $isTechnician : false }}";
            config.sort_dir = {
                id: 1,
                dir: 2
            };
            config.url.base_url = "{{ url('') }}";
            config.url.requests = "{{ url('tickets/requestAjaxList') }}";
            config.url.departments_by_company = "{{ url('departments/by-company') }}";
            config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
            config.url.getApproverSR = "{{ url('getApproverSR') }}";
            config.url.export_request = "{{ url('tickets/export-request') }}";
            config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
            config.url.departments_with_company = "{{ url('tickets/departments') }}";
            config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
            config.url.getUserDeviceByAjax = "{{ url('getUserDeviceForDropDown') }}"
            config.url.getTagDetails= "{{ url('ticket/getTagDetails') }}";
            config.url.getUserCCByAjax = "{{ url('getUserCCByQuery') }}";
            config.url.create_ticket = "{{ url('tickets/create') }}";
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
			config.url.getQueryLicense = "{{ url('getByCustomDropDown/getLicense') }}";
			config.url.getQueryProject = "{{ url('getByCustomDropDown/getProject') }}";
			config.url.getQueryPurchase = "{{ url('getByCustomDropDown/getPurchase') }}";
			config.url.getQuerySupplier = "{{ url('getByCustomDropDown/getSupplier') }}";
			config.url.getQueryContract = "{{ url('getByCustomDropDown/getContract') }}";
            config.url.requested_form = "{{ url('requested_form') }}";
            config.url.service_request_form = "{{ url('tickets/serviceRequestForm') }}";
            config.url.ticket_getcustomview ="{{ url('tickets/ticket_getcustomview') }}";
            config.url.fetch_active_ticket = "{{ url('tickets/fetch-active-access') }}";
            config.url.fetchccEmailID = "{{ url('tickets/fetch-cc-users-email-id') }}";
            config.url.requestInfo = "{{ url('tickets/requestInfo') }}";
            config.url.user_basic_info = "{{ url('user/basic-info') }}";
            config.url.departments_service_request = "{{ url('departments/by-service-request') }}";
            config.url.getEnabledAccountsForOptions ="{{ url('getEnabledAccountsForOptions') }}";
            config.url.getLocationByAjax = "{{ url('getLocationByQuery') }}";
            config.url.getUserByQueryForCustomForm = "{{ url('getUserByQueryForCustomForm') }}";
            config.url.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
            config.url.getCategoryByAjax = "{{ url('getCategoryByQuery') }}";
            config.url.getDataForCustomFormBaseOnType = "{{ url('getDataForCustomFormBaseOnType') }}";
            config.url.fetchAvailable = "{{ url('fetchAvailableItem') }}";
            config.url.getUser = "{{ url('user/getUserForEmpdetail') }}";
            config.url.bulkDecision = "{{ url('tickets/bulk-decision') }}";
            config.url.getCompanyWiseLocation= "{{ url('get-company-wise-location') }}";
            config.url.create_ticket_by_user = "{{ url('tickets/create-by-user') }}";
            config.url.requested_custom_form = "{{ url('requested_form/custom_form/edit') }}";
            config.url.requested_custom_formqty = "{{ url('requested_form/custom_form/edit_qty') }}";
            config.url.getRelatedDocuments = "{{ url('kd/getRelatedDocuments') }}";
            config.url.articleImagePath = "{{ url('uploads/article') }}";
            config.url.view_article = "{{ url('knowledge_document/article/view') }}";
            config.translations = {
				no_filter: '{{ trans('service_ticket.no_filter') }}',
                select_approver:'{{ trans('service_ticket.filter_heading.select_approver') }}',
                view_details:'{{ trans('service_ticket.button.view_details') }}',
			    authority_board:'{{ trans('service_ticket.authority_board') }}',
                status:'{{ trans('service_ticket.status') }}',
                last_update:'{{ trans('service_ticket.last_update') }}',
                no_request_found:'{{ trans('service_ticket.no_request_found') }}',
                something_went_wrong:'{{ trans('service_ticket.something_went_wrong') }}',
                filter_by_status:'{{ trans('service_ticket.filter_heading.filter_by_status') }}',
                filter_by_department:'{{ trans('service_ticket.filter_heading.filter_by_department') }}',
                filter_by_problem_category:'{{ trans('service_ticket.filter_heading.filter_by_problem_category') }}',
                filter_by_sub_category:'{{ trans('service_ticket.filter_heading.filter_by_sub_category') }}',
                filter_by_authority_board:'{{ trans('service_ticket.filter_heading.filter_by_authority_board') }}',
                filter_by_date:'{{ trans('service_ticket.filter_heading.filter_by_date') }}',
                filter_by_approval_mode:'{{ trans('service_ticket.filter_heading.filter_by_approval_mode') }}',
                filter_by_current_authority_board:'{{ trans('service_ticket.filter_heading.filter_by_current_authority_board') }}',
                filter_by_vip_tickets : '{{ trans('service_ticket.filter_heading.filter_by_vip_tickets') }}',
                comment_summer:'{{ trans('content.service_ticket_fields.share_comment') }}',
				service_request_atleast: '{{ trans('content.service_ticket_fields.service_request_atleast') }}',
                employee_code: '{{ trans('ticket.create_ticket.employee_code') }}',
                designation: '{{ trans('ticket.create_ticket.designation') }}',
                location: '{{ trans('ticket.create_ticket.location') }}',
                base_location: '{{ trans('ticket.create_ticket.base_location') }}',
                ext_user_company: '{{ trans('ticket.create_ticket.ext_user_company') }}',
                mobile: '{{ trans('ticket.create_ticket.mobile') }}',
                email: '{{ trans('ticket.create_ticket.email') }}',
                company: '{{ trans('ticket.create_ticket.company') }}',
                select_department: '{{ trans('ticket.ticket_list.select_department') }}',
                select_problem_category: '{{ trans('ticket.ticket_list.select_problem_category') }}',
                select_company: '{{ trans('ticket.ticket_list.select_company') }}',
                select_location: '{{ trans('ticket.ticket_list.select_location') }}',
                select_priority: '{{ trans('ticket.ticket_list.select_priority') }}',
                select_reply_to_account: '{{ trans('ticket.ticket_list.select_reply_to_account') }}',
                select_tags: '{{ trans('ticket.ticket_list.select_tags') }}',
                add_cc: '{{ trans('ticket.ticket_list.add_cc') }}',
                please_enter_a_valid_email_address: '{{ trans('ticket.ticket_list.please_enter_a_valid_email_address') }}',
                select_sub_category: '{{ trans('ticket.ticket_list.select_sub_category') }}',
                notes: '{{ trans('ticket.create_ticket.notes') }}',
                creator: '{{ trans('service_ticket.creator') }}',
                department: '{{ trans('service_ticket.department') }}',
                created_at: '{{ trans('service_ticket.created_at') }}',
                company_name: '{{ trans('service_ticket.company_name') }}',
                select_device: '{{ trans('ticket.ticket_list.select_device') }}',
			};
            new MyApp(config);	
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