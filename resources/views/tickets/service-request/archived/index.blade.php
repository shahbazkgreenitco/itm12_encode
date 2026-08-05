@extends('layouts.layout1')
@section('title', trans("content.archived.archived_request"))
@section('content')
    <div id="request-archived-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans("content.archived.archived_request") }}</h3>
            <div class="d-flex gap-8">
                <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip"
                    title="{{ trans('content.user_fields.Filter') }}">
                    <svg viewBox="0 0 20 18" fill="none">
                        <path
                            d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                            fill="currentColor" />
                    </svg>
                    <span class="b6-text opacity-50">{{ trans('content.user_fields.Filter') }}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>
                {{-- order section pending here --}}
                <div data-bs-toggle="tooltip" title="{{ trans('service_ticket.sort') }}">
                    <div class="header-icon-btn dropdown sort-buttons">
                        <span class="sortbtns dir-sort" data-id="7" data-dir="desc">
                            <svg id="sortAscSvg" class="hide" width="18px" height="18px" viewBox="0 0 24 24"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="Edit / Sort_Ascending">
                                    <path id="Vector" d="M4 17H16M4 12H13M4 7H10M18 13V5M18 5L21 8M18 5L15 8"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </g>
                            </svg>
                            <svg id="sortDescSvg" width="18px" height="18px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="Edit / Sort_Descending">
                                    <path id="Vector" d="M4 17H10M4 12H13M18 11V19M18 19L21 16M18 19L15 16M4 7H16"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </g>
                            </svg>
                        </span>
                        <span class="sortbtns dropdown-toggle">
                            <i class="caret" style="color:#808080"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right list-group sort-fields p-0"></ul>
                    </div>
                </div>
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-export-sr-archive" type="button" data-bs-toggle="tooltip"
                    title="{{ trans('content.ticket_incident.Download') }}">
                    <svg viewBox="0 0 18 18" fill="none">
                        <path
                            d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                            fill="currentColor" />
                    </svg>
                </button>
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">
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
                                    <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20"
                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <input type="text" class="amg-list-searchbar__input search" placeholder="Search...">
                                </div>
                            </div>

                            <button class="amg-refresh-btn btn-reload-list btn-reload-list btn-reload" title="{{ trans('content.ticket_incident.Refresh_List') }}">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                        fill="currentColor"></path>
                                </svg>
                                <span>{{ trans('header.header.refresh') }}</span>
                            </button>
                        </div>
                        <div class="row g-3 mt-1" id="ticket-data">         
                            {{-- Left Column: Ticket List --}}
                            <div class="col-12 col-lg-3">
                                <div id="api_loader">
                                    <svg id="loader" class="fa-spin" stroke="currentColor" fill="currentColor" width="120" height="120" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                                        <g>
                                            <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                                c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                                c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                                c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                                c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                            </path>
                                        </g>
                                    </svg>
                                </div>
                                <div class="ticket-list-wrapper rounded border overflow-auto" style="height:500px">
                                    <div id="lg" class="ticket-list p-2">
                                        {{-- Add more dynamic rows here --}}
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Ticket Detail & Sidebar --}}
                            <div class="col-12 col-lg-9">
                                <div id="detail_api_loader">
                                    <svg id="loader" class="fa-spin" stroke="currentColor" fill="currentColor" width="120" height="120" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                                        <g>
                                            <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                                c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                                c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                                c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                                c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                            </path>
                                        </g>
                                    </svg>
                                </div>
                                
                                <div class="row g-3">
                                    {{-- Detail Section --}}
                                    <div class="col-12 col-lg-8">
                                        <div class="ticket-detail-wrapper rounded border overflow-auto" style="height:500px">
                                            <div class="ticket-detail-box p-4">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right Sidebar: Info & KD --}}
                                    <div class="col-12 col-lg-4">
                                        <div class="ticket-info-wrapper rounded border overflow-auto" style="max-height: 500px;">
                                            <div class="ticket-info"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="no-data" class="d-none"></div>
                        <div class="row margin">
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                <div id="page-btm-summary" style="margin-top: 9px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('tickets.service-request.sr-filter-modal')
        @include('tickets.ticket-list.ticket-history')
    </div>
@endsection
@push('css')
    <link href="{!! CommonHelper::asset('plugins/swipebox/css/swipebox.min.css') !!}" rel="stylesheet" />
    <style>
           
            .currentColor {
                background-color: #D80505;
            }
            .tat-content .tat-time {
                font-size: 13px;
                font-weight: 600;
            }
            
            .carousel-inner .item .card {
                position: relative;
                height: 280px;
                border-radius: 20px;
                flex-shrink: 0;
                background-size: cover;
                background-position: center;
                display: flex;
                align-items: flex-end;
                padding: 20px;
                margin-right: 4px;
            }

            .carousel-inner .item .card .card-content {
                position: relative;
                z-index: 2;
            }

            .carousel-inner .item .card .big-kd {
                position: absolute;
                top: 100px;
                right: -15px;
                font-size: 100px;
                font-weight: bold;
                line-height: 0.8;
                color: transparent;
                opacity: 0.8;
                transform: rotate(270deg);
                z-index: 2;
                -webkit-text-stroke: 1px white;
                font-family: 'Inter', sans-serif;
            }

            .carousel-inner .item .card .title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
                color: white;
            }

            .carousel-inner .item .card .tags {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .carousel-inner .item .card .tag {
                border: 1px solid #fff;
                padding: 2px 10px;
                border-radius: 20px;
                color: white;
                background: transparent;
                font-size: 10px;
            }

            .carousel-inner .item .card .arro_back {
                padding: 4px 4px;
                height: 60px;
                width: 60px;
                position: absolute;
                right: -15px;
                bottom: -10px;
                z-index: 3;
                border-top-left-radius: 20px;
                background: var(--app-bg);
            }

            .carousel-inner .item .card .arro_back::after {
                width: 2.125rem;
                height: 2.125rem;
                background-color: transparent;
                content: "";
                top: -34px;
                right: 14.5px;
                border-bottom-right-radius: 11px;
                position: absolute;
                box-shadow: 0.375rem 0.375rem var(--app-bg);
            }

            .carousel-inner .item .card .arro_back::before {
                width: 2.125rem;
                height: 2.125rem;
                background-color: transparent;
                content: "";
                bottom: 9.5px;
                right: 60.4px;
                border-bottom-right-radius: 11px;
                position: absolute;
                box-shadow: 0.375rem 0.375rem var(--app-bg);
            }
            .carousel-control .icon-prev,
            .carousel-control .icon-next {
                color: #b0abab;
            }
            .card-panel {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 14px;
                box-shadow: var(--shadow);
                padding: 1.25rem;
                margin-bottom: 1.25rem;
            }
            .timeline-entry.tiny-view-on .tml-content-container,
            .timeline-entry.tiny-view-on .attachment-container {
                display: none;
            }

            .custom-body{
                max-height:80px; /* by default thoda open */
                overflow:hidden;
                transition:all 0.3s ease;
            }

            .custom-body.open{
                max-height:1000px;
            }

            #api_loader,
            #detail_api_loader {
                display: none;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 9999;
                background: rgba(0, 0, 0, 0.3);
            }

            #api_loader.active,
            #detail_api_loader.active{
                align-items: center;
                justify-content: center;
                display: flex;
                opacity: 1;
                transition: opacity 0.3s;
            }
        

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-8px); }
                to { opacity: 1; transform: translateY(0); }
            }
            /* ===== BADGE ===== */
            .badge-light {
                color: #fff;
                background-color: #cd3333;
                border-radius: 20px;
                padding: 4px 10px;
                font-weight: 600;
                font-size: 12px;
            }
            
        .ticket-list-wrapper {
            height: 500px;
            border: 1px solid #cfcfcf;
            border-radius: 10px;
            background: #ffffff;
            overflow: hidden; 
        }
        .ticket-list {
            height: 100%;
            overflow-y: auto;
            padding-right: 6px;
            scrollbar-width: thin; 
        }

        [data-bs-theme="dark"] .ticket-list {
            background-color: #141414;
        }

        .ticket-list::-webkit-scrollbar {
            width: 6px;
        }
        .ticket-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .ticket-card {
            padding: 5px 5px;
            border-bottom: 1px solid #cfcfcf;
            cursor: pointer;
            transition: 'background 0.2s';
        }

        .ticket-card:last-child {
            border-bottom: none;
        }

        .ticket-card.active {
            background: #FFF0F0 !important;
        }

        [data-bs-theme="dark"] .ticket-card.active {
            background-color: #2A2A2A !important;
        }

        .tat_expiry {
            font-size: 12px;
        }

        .tat-box {
            width: 50px;
            height: 70px;
            border-radius: 3px;
            text-align: center;
            color: #fff;
            padding-top: 14px;
            position: relative;
            margin: 0;
        }

        .tat-content {
            padding: 0;
        }

        .tat-content i {
            font-size: 20px;
            display: block;
            margin-bottom: 4px;
        }

        .date,
        .dep {
            font-size: 11px;
            color: #888;
            white-space: nowrap;
            font-weight: 400;
        }

        .ticket-detail-wrapper {
            height: 500px;
            border: 1px solid #cfcfcf;
            border-radius: 10px;
            background: #ffffff;
            overflow: hidden;
            position: relative;
        }

        .ticket-detail-box {
            height: 100%;
            padding: 18px;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-gutter: stable;
            box-sizing: border-box;
        }
        [data-bs-theme="dark"] .ticket-detail-box {
            background-color: #141414;
        }

        .ticket-info-wrapper {
            height: 500px;
            border: 1px solid #cfcfcf;
            border-radius: 10px;
            background: #ffffff;
            overflow: hidden;
            position: relative;
        }

        .ticket-info {
            height: 100%;
            overflow-y: auto;
            padding: 12px;
            scrollbar-width: thin;
            scrollbar-gutter: stable;
            box-sizing: border-box;
        }

        [data-bs-theme="dark"] .ticket-info {
            background-color: #141414;
        }

        .ticket-detail-box::-webkit-scrollbar,
        .ticket-info::-webkit-scrollbar {
            width: 6px;
        }

        .ticket-detail-box::-webkit-scrollbar-thumb,
        .ticket-info::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .ticket-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .ticket-title {
            margin: 0 0 10px 0;
            font-size: 20px;
            font-weight: bold;
            color: #343536;
        }

        .td-left {
            flex: 1;
        }

        .td-left .meta-item {
            display: inline-block;
            font-size: 13px;
            margin-right: 16px;
            padding-right: 16px;
            margin-bottom: 5px;
            color: #666;
            position: relative;
        }

        /* Vertical divider */
        .td-left .meta-item:not(:last-child)::after {
            content: "";
            position: absolute;
            right: 0;
            top: 50%;
            width: 1px;
            height: 14px;
            background: #ccc;
            transform: translateY(-50%);
        }

        .meta-item.medium {
            color: green;
            font-weight: 600;
        }

        .td-right {
            margin-top: 0;
            display: flex;
            align-items: center;
        }

        .td-right .tag-btn {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 4px;
            margin: 5px 10px 5px 0;
            background: #fff1ef;
            border: 1px solid #ffd7d0;
        }

        .ticket-detail-box .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 25px;
            height: 25px;
            border-radius: 40px;
            background: #fff1ef;
            border: 1px solid #ffd7d0;
            margin-left: 6px;
        }

        .icon-btn i {
            color: #141414;
        }
        [data-bs-theme="dark"] .icon-btn i {
            background-color: #fff;
        }

        .divider {
            margin: 15px -20px 20px -20px;
            border: none;
            border-top: 1px solid #cfcfcf;
        }

        .ticket-comment{
            border: 1px solid #cfcfcf;
            padding: 18px;
            border-radius: 14px;
            margin-top: 20px;
            background: #fff;
        }

        [data-bs-theme="dark"] .ticket-comment {
            background-color: #141414;
        }

        .toggle-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f3f4f6;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-icon i {
            font-size: 16px;
            transition: transform 0.3s ease;
        }

        .created-updated {
            font-size: 12px;
            margin-bottom: 10px;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .info-tag {
            display: inline-block;
            color: #444;
            font-size: 11px;
            font-weight: 600;
        }

        .m-t-10 {
            margin-top: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 3px 0;
        }

        .d-title {
            color: #333;
            font-weight: 600;
        }

        .d-value {
            color: #555;
        }

        .box-title {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 10px;
            color: #000000;
        }

        .kb-image-box {
            background: #f7f9fb;
            padding: 6px;
            border-radius: 12px;
            overflow: hidden;
        }

        .kb-img {
            border-radius: 10px;
            width: 100%;
        }

        .kb-text {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }

        .ticket-toolbar {
            display: flex;
            align-items: center;
            padding: 4px 10px;
            background: linear-gradient(90deg, #f6ecff, #fdeeee);
            border-bottom: 1px solid #eee;
            justify-content: space-between;
            gap: 16px;
            padding: 8px 12px;
            flex-wrap: wrap;
        }

        .ticket-toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            background: linear-gradient(90deg, #f6ecff, #fdeeee);
            border-bottom: 1px solid #eee;
            justify-content: flex-end;
        }

        .toolbar-select {
            padding: 6px 12px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #ddd;
            font-size: 14px;
            cursor: pointer;
        }

        .toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 14px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #e5e5e5;
            cursor: pointer;
            transition: all .2s ease;
        }

        .toolbar-btn:hover {
            background: #f5f5f5;
        }

        /* ICON ONLY BUTTONS */
        .icon-square {
            width: 34px;
            height: 34px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background: #fff;
            cursor: pointer;
            transition: .2s;
            padding: inherit;
        }

        .icon-square:hover {
            background: #f5f5f5;
        }

        /* SVG inside buttons */
        .toolbar-btn svg,
        .create-ticket-btn svg,
        .icon-square svg {
            width: 18px;
            height: 18px;
            display: block;
        }

        .toolbar-search {
            padding: 6px 14px;
            width: 240px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #fff;
            font-size: 14px;
            max-width: 100%;
        }

        @media (max-width: 768px) {
            .toolbar-search {
                width: 100%;
            }

            .searchbox_cover {
                width: 100%;
            }

            .ticket-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .ticket-toolbar h2 {
                font-size: 16px;
                text-align: center;
            }

            .ticket-toolbar-right {
                justify-content: space-between;
                gap: 6px;
                overflow-x: auto;
                white-space: nowrap;
                scrollbar-width: thin;
            }

            .ticket-toolbar-right>* {
                flex: 0 0 auto;
            }

            /* Buttons smaller */
            .toolbar-btn {
                padding: 6px 10px;
                font-size: 13px;
            }

            .icon-square {
                width: 32px;
                height: 32px;
            }
        }

        .status-tags {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 12px 20px;
        }

        .status-tag {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #fff;
            border: 1px solid #efefef;
            font-size: 13px;
            border-radius: 15px;
        }

        .status-color {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        #api_loader,
        #detail_api_loader {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.3);
        }

        #api_loader.active,
        #detail_api_loader.active{
            align-items: center;
            justify-content: center;
            display: flex;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .timeline:before {
            left: 65px;
        }

        /* Flashing Effect */
        @keyframes pulsate {
            0% {
                box-shadow: 0 0 5px rgba(255, 0, 127, 0.5);
            }

            50% {
                box-shadow: 0 0 15px rgba(255, 0, 127, 0.8);
            }

            100% {
                box-shadow: 0 0 5px rgba(255, 0, 127, 0.5);
            }
        }

        @keyframes flashText {
            from {
                color: white;
            }

            to {
                color: yellow;
            }
        }

        /* Flashing Effect */
        @keyframes pulsate {
            0% {
                box-shadow: 0 0 5px rgba(255, 0, 127, 0.5);
            }

            50% {
                box-shadow: 0 0 15px rgba(255, 0, 127, 0.8);
            }

            100% {
                box-shadow: 0 0 5px rgba(255, 0, 127, 0.5);
            }
        }

        /* Hover Effect */
        .ai-assist-btn:hover {
            background: linear-gradient(45deg, #ff7300, #ff007f);
            transform: scale(1.05);
        }

        /* Text Animation */
        .ai-assist-text {
            animation: flashText 1s infinite alternate;
        }

        @keyframes flashText {
            from {
                color: white;
            }

            to {
                color: yellow;
            }
        }

        .timeline:after {
            background-color: #bec6ce;
            content: "";
            display: none;
            position: absolute;
        }


        .count-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            z-index: 1;
        }

        #ticket_timeline .card {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15) !important;
            border-radius: 20px;
            max-width: 100%;
            height: 130px;
            overflow: hidden;
            transition: height 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        #ticket_timeline .card-body {
            flex-grow: 1;
            overflow: hidden;
        }

        #ticket_timeline .card-footer {
            padding: 8px 0px;
            border-top: 1px solid #dee2e6;
        }

        .load-comment {
            display: flex;
            justify-content: center;
        }

        .load-more {
            width: 40px;
            height: 40px;
            background: #ffffff;
            border: 1px solid #CD33334D;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rounded-circle {
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #bcbcbc;
        }

        #ticket_timeline .tri-view i,
        #ticket_timeline .tri-download i {
            cursor: pointer;
            font-size: 14px;
            color: #777;
        }

        #ticket_timeline .tri-view i:hover,
        #ticket_timeline .tri-download i:hover {
            color: #0056b3;
        }

        .custom-accordion .accordion-button::after{
            display:none !important;
        }

        .custom-accordion .accordion-button{
            background:#fff;
            box-shadow:none !important;
            display:flex;
            align-items:center;
            width:100%;
            border:none;
        }

        .custom-accordion .accordion-item{
            border:1px solid #ddd;
            border-radius:10px;
            overflow:hidden;
        }

        .custom-body{
            /* max-height:0; */
            overflow:hidden;
            transition:max-height 0.4s ease;
            padding:0 1rem;
        }

        .custom-body.open{
            max-height:1000px;
            padding:0.75rem 1rem;
        }

        .accordion-icon{
            transition:transform 0.3s ease;
        }

        .accordion-icon.rotate{
            transform:rotate(180deg);
        }

        [data-bs-theme="dark"] .accordion-header button {
            background-color: #141414;
        }

        #loader {
            color: #da1a1a;
        }

        .tl-note-background {
            background: #d7d4a9  !important;
        }

        .ticket-detail-box .icons {
            position: absolute;
            width: 124px;
            top: 54px;
            height: 34px;
        }

        .ticket-detail-box .icons span {
            padding: 6px 10px;
            background: #000;
            border-radius: 3px;
            margin-left: 15px;
            cursor: pointer;
            color: #fff;
            display: inline-block;
        }

        [data-bs-theme="dark"] .ticket-detail-header .td-right span {
            background-color: #141414;
        }
        .load-more-loader {
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }
        .info-section {
            background: white;
            border-radius: 8px;
            padding: 10px 15px;
        }

        [data-bs-theme="dark"] .info-section {
            background-color: #141414;
        }
        .info-section .info-label {
            font-weight: 500;
            color: #000;
        }
        .info-section .info-value {
            display: flex;
            align-items: center;
            gap: 3px;
            color: #555;
        }
        .info-section .avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            object-fit: cover;
        }
        .info-section .user-name {
            max-width: 110px;     
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .approval-heading {
            font-weight: 600;
            margin-bottom: 10px;
        }
        [data-bs-theme="dark"] .approval-heading {
            background-color: #141414;
        }

        .well {
            background-color: #e5ebf1; */
            border-color: #dbe3ec;
            border-radius: 0;
            box-shadow: none;
            min-height: 20px;
            padding: 19px;
            margin-bottom: 20px;
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.05)
        }

        .pad-ver {
            padding-top: 15px;
            padding-bottom: 15px;
        }

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
    </style>
@endpush
@push('scripts')
    <script src="{!! CommonHelper::asset('js/tickets/archived_tickets/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/swipebox/js/jquery.swipebox.min.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.token = "{{ csrf_token() }}";
           
            config.url.archivedAjaxRemote = "{{ url('tickets/archived-requests/ajax-list') }}";
            config.url.getArchivedRequestHistory = "{{ url('tickets/archived-requests/history') }}";
            config.url.getArchivedRequestDetails = "{{ url('tickets/archived-requests/details') }}";
            config.url.get_timeline_archived = "{{ url('tickets/archived-timeline') }}";
            config.url.attachment_download = "{{ url('ticket/attachment/download') }}";
            config.url.attachment_view = "{{ url('ticket/attachment/view') }}";
            config.url.departments_service_request = "{{ url('departments/by-service-request') }}";
            config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
            config.url.getApproverSR = "{{ url('getApproverSR') }}";
            config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
            config.url.export_request = "{{ url('tickets/archived-requests/export') }}";
            config.url.view_form = "{{ url('requested_archived_form/view') }}";
            config.url.view_custom_form = "{{ url('requested_archived_custom_form/view') }}"; 
            config.url.user_url = "{{ url('user/info/') }}";
            config.url.nodataImage= "{{ asset('imgs/notdatafound.png') }}";

            config.translations = {
                Available_Records: '{{ trans('content.service_ticket_fields.Available_Records') }}',
                Filter_By_Problem_Category: '{{ trans('content.filter_heading.Filter_By_Problem_Category') }}',
				Filter_By_Sub_Category: '{{ trans('content.filter_heading.Filter_By_Sub_Category') }}',
                No_Filter: '{{ trans('content.procurement_fields.No_Filter') }}',
                Available: '{{ trans('content.service_ticket_fields.Available') }}',
                No_Tickets_Found: '{{ trans('content.service_ticket_fields.No_Tickets_Found') }}',
                Select_User: '{{ trans('content.service_ticket_fields.Select_User') }}',
            }
            config.sort_dir = {
                id: 1,
                dir: 2
            };
            config.pabs = {!! json_encode($pabs) !!};
            config.priorities = {!! json_encode($priorities) !!};
            config.statuses = {!! json_encode($statuses) !!};
            config.sort_fields = {!! json_encode($sort_fields) !!};
            config.isSuperUser = {!! json_encode(Auth::user()->isSuperUser()) !!};            
            config.user = {!! json_encode(
                array_merge(
                    Auth::user()->only([
                        'id',
                        'first_name',
                        'last_name',
                        'username',
                        'company_id',
                        'location',
                        'displayName',
                        'employee_num',
                    ]),
                ),
            ) !!};
            new ArchivedServiceRequestList(config);
        });
    </script>
@endpush