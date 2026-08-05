@extends('layouts.layout1')
@section('title', trans('content.archived.archived_ticket'))
@section('content')
    <div id="ticket-archived-list-wrapper">
        @if($hasArchivedTickets)
            <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
                <h3 class="h3-text mb-0">{{ trans("content.archived.archived_ticket") }}</h3>
                <div class="d-flex gap-8">
                    <button class="header-icon-btn header-icon-btn-sm btn-open-filter"  id="btnOpenFilter" type="button" data-bs-toggle="tooltip"
                        title="{{ trans('content.user_fields.Filter') }}">
                        <svg viewBox="0 0 20 18" fill="none">
                            <path
                                d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                                fill="currentColor" />
                        </svg>
                        <span class="b6-text opacity-50">{{ trans('content.user_fields.Filter') }}</span>
                        <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                    </button>

                    @if (Auth::user()->hasPermissionTo('TicketIncidentDownload'))
                    <button class="header-icon-btn-only header-icon-btn-only-sm btn-export-ticket-incident" type="button"
                        data-bs-toggle="tooltip" title="{{ trans('content.ticket_incident.Download') }}">
                        <svg viewBox="0 0 18 18" fill="none">
                            <path
                                d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                    @endif

                    @if (Auth::user()->hasPermissionTo('TicketIncidentAdd'))
                    <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-incident" type="button" data-bs-toggle="tooltip"
                        title="Create Ticket">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                fill="currentColor" />
                        </svg>
                        <span>Create Ticket</span>
                    </button>
                    @endif
                </div>

            </div>
            <main class="main-content" id="mainContent">
                <div class="container-fluid px-0">
                    <div class="card rounded-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div class="col-auto">
                                    <select class="amg-table-pagination-dropdown amgTablePageLenth incident-list-page-length">
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
                                        <input type="text" class="amg-list-searchbar__input user-list-search"
                                            placeholder="Search...">
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
                            <div class="row g-3 mt-2" id="ticket-data">
                                
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
                                                <div class="ticket-detail-box">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right Sidebar: Info & KB --}}
                                        <div class="col-12 col-lg-4">
                                            <div class="ticket-info-wrapper rounded border overflow-auto" style="max-height: 500px;">
                                                <div class="ticket-info"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- drawer -->
                            <div id="drawer">
                                <div class="drawer-header">
                                    <h4>Ticket Details</h4>
                                    <button class="close-drawer">&times;</button>
                                </div>

                                <div class="drawer-body">
                                </div>
                            </div>
                            <div id="drawer-overlay"></div>
                        </div>
                    </div>
                </div>
                @include('tickets.archived.archived-filter')
                @include('task-management.taskHistory')
            </main>

        @else
            <div class="d-flex flex-column align-items-center justify-content-center text-center py-5" style="min-height: 400px;">
                <i class="bi bi-archive" style="font-size: 48px; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">{{ 'No archived data found' }}</h5>
            </div>
        @endif
    </div>
@endsection

@push('css')
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>

        .currentColor {
            background-color: #D80505;
        }
        .tat-content .tat-time {
            font-size: 13px;
            font-weight: 600;
        }
        .tat-content i {
            font-size: 20px;
            color: white;
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

        .custom-accordion .accordion-button::after{
            display:none !important;
        }

        .custom-accordion .accordion-button{
            background:#fff;
            box-shadow:none !important;
            display:flex;
            align-items:center;
        }

        .custom-body{
            max-height:80px; /* by default thoda open */
            overflow:hidden;
            transition:all 0.3s ease;
        }

        .custom-body.open{
            max-height:1000px;
        }

        .accordion-icon{
            transition:0.3s ease;
        }

        .accordion-icon.rotate{
            transform:rotate(180deg);
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
        /* ===== TASK CARD ===== */
        .task-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin: 0 auto 30px;
            border: 1px solid #cfcfcf;
            max-width: 1100px;
            transition: all 0.3s ease;
        }

        .task-card.collapsed .task-section {
            display: none;
        }

        /* ===== HEADER ===== */
        .task-card-header {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .task-card-header h3 {
            width: 50%;
            display: flex;
            align-items: center;
        }

        .task-progress-right {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: 6px;
        }

        .task-card-header h4 {
            font-size: 16px;
            font-weight: 600;
            color: #4b4a4a;
            margin: 0;
        }

        .expand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
            margin-left: 6px;
        }

        .task-card:not(.collapsed) .expand-icon svg {
            transform: rotate(180deg);
        }

        .expand-icon svg {
            width: 16px;
            height: 16px;
            fill: #4b4a4a;
            transition: transform 0.3s ease;
        }

        /* ===== PROGRESS BAR ===== */
        .progress-bar-wrapper {
            margin: 12px 0 16px;
            width: 100%;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #ecfae9;
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-bar .fill {
            height: 100%;
            background: linear-gradient(90deg, #398d5f, #4caf50);
            border-radius: 6px;
            transition: width 0.6s ease;
        }

        /* ===== TASK SECTION ===== */
        .task-section {
            margin-top: 12px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== TABS ===== */
        .task-tabs-container {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            border-bottom: 2px solid #ddd;
        }

        .task-tabs {
            display: flex;
            flex-wrap: nowrap;
            padding: 0;
            margin: 5px;
            list-style: none;
            border-bottom: 0;
            gap: 10px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .task-tabs::-webkit-scrollbar {
            display: none;
        }

        .task-tab {
            margin-bottom: 0;
        }

        /* Bootstrap 5 nav-link override for task tabs */
        .task-tab-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid #e4e4e4;
            border-radius: 30px;
            background: #fff;
            color: #555;
            transition: all 0.25s ease;
            white-space: nowrap;
            border: none;
            outline: none;
        }

        .task-tab-link:hover {
            background: #f5f5f5;
        }

        .task-tab-link.active {
            background: #398d5f;
            color: #fff;
        }

        .task-tab-link.active i {
            color: #fff !important;
        }

        .task-tab-link:focus-visible {
            outline: 2px solid #398d5f;
            outline-offset: 2px;
        }

        /* ===== BUTTONS ===== */
        .tab-button {
            display: flex;
            justify-content: flex-end;
        }

        .task-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #666;
            font-size: 15px;
            text-decoration: none;
        }

        .btn-action:hover {
            background: #f8f9fa;
            border-color: #ccc;
            color: #333;
            transform: translateY(-1px);
        }

        /* ===== BOX CONTAINER ===== */
        .box-container {
            border: 1px solid #ebebeb;
            border-radius: 14px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-template-rows: auto auto;
            background: #fff;
        }

        .box {
            padding: 20px 16px;
        }

        .top-left {
            border-right: 1px solid #ebebeb;
            border-bottom: 1px solid #ebebeb;
        }

        .bottom-left {
            border-right: 1px solid #ebebeb;
        }

        .top-right {
            border-bottom: 1px solid #ebebeb;
        }

        /* ===== TASK DETAILS ===== */
        .task-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 6px 0;
            gap: 16px;
            flex-wrap: wrap;
        }

        .task-label {
            font-size: 12px;
            color: #888;
            margin: 0 0 4px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .task-name {
            font-size: 15px;
            font-weight: 600;
            color: #4b4a4a;
            margin: 4px 0 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .task-status {
            display: inline-block;
            border-radius: 20px;
            padding: 5px 16px;
            font-weight: 600;
            font-size: 12px;
            background: #dbffec;
            color: #0b8431;
        }

        .task-priority {
            display: inline-block;
            border-radius: 20px;
            padding: 5px 16px;
            font-weight: 600;
            font-size: 12px;
            background: #ffe5e6;
            color: #e3200b;
        }

        /* ===== ASSIGNEE ===== */
        .assignee-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .assignee-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }

        .assignee-name {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== CIRCULAR PROGRESS ===== */
        .task-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .circular-progress {
            width: 46px;
            height: 46px;
        }

        .circle-bg {
            fill: none;
            stroke: #e5e7eb;
            stroke-width: 3.8;
        }

        .circle {
            fill: none;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
            transition: stroke-dasharray 0.6s ease;
        }

        .progress-text {
            font-size: 12px;
            color: #666;
            margin-left: 8px;
            font-weight: 500;
        }

        /* ===== TIMELINE ===== */
        .timeline-vertical {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .timeline-vertical::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 9px;
            width: 2px;
            height: calc(100% - 16px);
            background: linear-gradient(180deg, #ffa4a4, #ffcccc);
            border-radius: 2px;
            z-index: 0;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .timeline-icon {
            width: 22px;
            height: 22px;
            background: #fff;
            border: 2px solid #ffa4a4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .timeline-icon svg {
            width: 12px;
            height: 12px;
        }

        .timeline-content {
            padding-left: 2px;
            padding-top: 1px;
        }

        .timeline-lebal {
            font-size: 11px;
            color: #888;
            margin: 0;
            font-weight: 500;
        }

        .timeline-date {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin: 2px 0 0;
        }

        .timeline-block {
            display: flex;
            flex-direction: column;
            position: relative;
            padding: 12px 0;
        }

        .timeline-block .icon {
            position: absolute;
            left: 0;
            top: 10px;
        }

        .timeline-block .label {
            font-size: 12px;
            color: #888;
            margin: 0;
            padding-left: 38px;
            font-weight: 500;
        }

        .timeline-block .date {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin: 4px 0 0;
            padding-left: 38px;
        }

        /* ===== COMMENT ===== */
        .tab-comment {
            margin-top: 16px;
            border-top: 1px solid #eee;
            padding-top: 16px;
        }

        /* ===== EMPTY STATE ===== */
        .task-empty-wrapper {
            text-align: center;
            margin-top: 20px;
            padding: 40px 20px;
        }

        .task-empty-img {
            height: 180px;
            width: auto;
            max-width: 100%;
        }

        .task-empty-text {
            margin-top: 16px;
            font-size: 16px;
            color: #666;
        }

        /* ===== UTILITIES ===== */
        .hide { display: none !important; }
        .no-bg { background: transparent !important; }

        /* ===== BADGE ===== */
        .badge-light {
            color: #fff;
            background-color: #cd3333;
            border-radius: 20px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 12px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .box-container {
                grid-template-columns: 1fr;
            }
            .top-left, .bottom-left, .top-right, .bottom-right {
                border-right: none !important;
                border-bottom: 1px solid #ebebeb;
            }
            .bottom-right {
                border-bottom: none;
            }
        }

        @media (max-width: 768px) {
            .task-card {
                padding: 16px;
                margin: 16px auto;
            }
            .task-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .task-details {
                flex-direction: column;
            }
            .col-md-5 {
                flex: 0 0 auto;
                width: 100%;
            }
            .tab-button {
                justify-content: center;
            }
        }

        /* ===== BOOTSTRAP 5 TAB PANE FIX ===== */
        .tab-content > .tab-pane {
            display: none;
        }
        .tab-content > .show {
            display: block;
        }
        .tab-content > .tab-pane.fade {
            opacity: 0;
            transition: opacity 0.15s linear;
        }
        .tab-content > .tab-pane.fade.show {
            opacity: 1;
        }
        #ticket-content {
            font-size: 12px
        }
        /* ===== DRAWER ===== */
        #drawer-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.35);
            z-index: 1040; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        #drawer-overlay.active { opacity: 1; pointer-events: auto; }

        #drawer {
            position: fixed; top: 0; right: -520px; width: 480px; height: 100vh;
            background: #ffffff; box-shadow: -5px 0 24px rgba(0,0,0,0.12);
            transition: right 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050; display: flex; flex-direction: column;
        }
        #drawer.open { right: 0; }

        .drawer-header {
            padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e5e7eb;
            display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;
        }
        .drawer-header h4 { font-size: 18px; font-weight: 600; color: #111827; margin: 0; }
        .close-drawer {
            background: none; border: none; font-size: 26px; color: #6b7280;
            cursor: pointer; line-height: 1; padding: 4px; border-radius: 6px; transition: all 0.2s;
        }
        .close-drawer:hover { background: #e5e7eb; color: #111827; }

        .drawer-body.history-timeline {
            flex: 1; overflow-y: auto; padding: 20px;
            scrollbar-width: thin; scrollbar-color: #d1d5db transparent;
        }
        .drawer-body.history-timeline::-webkit-scrollbar { width: 6px; }
        .drawer-body.history-timeline::-webkit-scrollbar-track { background: transparent; }
        .drawer-body.history-timeline::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        /* ===== TIMELINE ===== */
        .history-timeline-item {
            display: flex; gap: 16px; margin-bottom: 20px; position: relative;
        }
        .history-timeline-item:last-child { margin-bottom: 0; }

        .history-icon-container {
            display: flex; flex-direction: column; align-items: center; position: relative; flex-shrink: 0;
        }
        .history-timeline-icon {
            width: 36px; height: 36px; background: #ffffff; border: 1px solid #e5e7eb;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            z-index: 2; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .history-timeline-line {
            flex: 1; width: 2px; background: linear-gradient(to bottom, #e5e7eb 0%, transparent 100%);
            margin-top: 4px;
        }
        .history-timeline-item:last-child .history-timeline-line { display: none; }

        /* ===== DETAIL CARD ===== */
        .history-detail-info-container { flex: 1; min-width: 0; }
        .history-detail-info {
            background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 16px; transition: all 0.2s ease; cursor: default;
        }
        .history-detail-info:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

        /* ===== TIME ROW ===== */
        .history-detail-info-time-row {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;
        }
        .history-detail-info-time-text {
            display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6b7280;
        }
        .history-detail-info-time-text svg { width: 14px; height: 14px; flex-shrink: 0; }
        .history-detail-info-time-row-title { font-weight: 500; }

        .accordion-toggle {
            background: none; border: none; padding: 4px; cursor: pointer;
            display: inline-flex; align-items: center; border-radius: 4px; transition: background 0.2s;
        }
        .accordion-toggle:hover { background: #f3f4f6; }
        .history-detail-info-accrodian-icon {
            width: 18px; height: 18px; fill: #9ca3af; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .history-detail-info.open .history-detail-info-accrodian-icon { transform: rotate(180deg); }

        /* ===== MODIFIED STATUS ===== */
        .history-detail-info-modified-status {
            display: flex; align-items: center; gap: 6px; margin-bottom: 10px; flex-wrap: wrap;
        }
        .history-detail-info-modified-by { display: flex; align-items: center; gap: 8px; }
        .modfied-by-image img {
            width: 24px; height: 24px; border-radius: 50%; object-fit: cover;
            border: 1px solid #e5e7eb; background: #f9fafb;
        }
        .modfied-by-name { font-size: 13px; font-weight: 500; color: #374151; }
        .history-detail-info-modified-status-middot { color: #d1d5db; font-size: 16px; line-height: 1; }
        .history-detail-info-modified-status a {
            font-size: 13px; color: #6b7280; text-decoration: none; font-weight: 400; transition: color 0.2s;
        }
        .history-detail-info-modified-status a:hover { color: #2563eb; }

        /* ===== CHANGED CONTENT ===== */
        .history-detail-info-changed {
            font-size: 13px; color: #4b5563; background: #f9fafb; padding: 10px 12px;
            border-radius: 8px; border-left: 3px solid #3b82f6; line-height: 1.5;
        }
        .changed-from b { color: #111827; font-weight: 600; }

        /* ===== ACCORDION ===== */
        .accordion-content {
            max-height: 0; overflow: hidden; transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .history-detail-info.open .accordion-content { max-height: 300px; }
        .accordion-content-inner { padding-top: 12px; border-top: 1px dashed #e5e7eb; margin-top: 10px; }

        /* ===== TRIGGER BUTTON (DEMO) ===== */
        .demo-trigger {
            position: fixed; bottom: 20px; right: 20px; padding: 12px 20px;
            background: #111827; color: #fff; border: none; border-radius: 8px;
            font-weight: 500; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }
        .demo-trigger:hover { background: #374151; transform: translateY(-2px); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            #drawer { width: 100%; right: -100%; }
            .history-timeline-item { gap: 12px; }
            .history-detail-info { padding: 12px; }
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/tickets/archived/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/task_management/history.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.placeholder = {};
            config.title = {};
            config.basePath = "{{ asset('') }}";
            config.token = "{{ csrf_token() }}";
            config.url.archivedAjaxRemote = "{{ url('tickets/archived-ajax-remote') }}";
            config.url.getArchivedTicketDetails = "{{ url('tickets/get-archived-ticket-details') }}";
            config.url.getArchivedTicketHistory = "{{ url('tickets/get-archived-ticket-history') }}";
            config.url.getRelatedTask = "{{ url('task-management/getRelevantArchivedTasks') }}";
            config.url.get_task_timeline = "{{ url('task-management/get_archived_comment') }}";
            config.url.download_task_attachment = "{{ url('task-management/download_attachment') }}";
            config.url.view_task_attachment = "{{ url('task-management/view_attachment') }}";
            config.url.ArchivedTaskhistory = "{{ url('task-management/archivedTaskhistory') }}";
            config.url.departments_based_on_privilage = "{{ url('departments/by-company/privilage') }}";
            config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
            config.url.ticket_type = "{{ url('tickets/getTicketTypeByDepartment') }}";
            config.url.customFieldsForFilter = "{{ url('ticket/getCustomFieldsForFilter') }}";
            config.url.getCustomFieldsValueForFilter = "{{ url('ticket/getCustomFieldsValueForFilter') }}";
            config.url.getTasks = "{{ url('task-management/archivedAjaxList') }}";
            config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
            config.url.export_tickets ="{{ url('tickets/archived-export') }}";
            config.url.get_timeline_archived = "{{ url('tickets/archived-timeline') }}";
            config.url.attachment_download = "{{ url('ticket/attachment/download') }}";
            config.url.attachment_view = "{{ url('ticket/attachment/view') }}";
            config.url.article_view = "{{ url('knowledge_document/article/view') }}";
            config.url.view_form = "{{ url('requested_archived_form/view') }}";
            config.url.view_custom_form = "{{ url('requested_archived_custom_form/view') }}"; 
            config.url.view_status_form = "{{ url('status_requested_form/view') }}";
            config.url.archived_info = "{{ url('task-management/archivedTaskInfo') }}";
            config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
            config.url.getStatusByAjax= "{{ url('getStatusByAjax') }}",
            config.company_user_detail = {!! json_encode($dashboardCompanyId) !!};
            config.translations = {
                filter_by_task: '{{ trans('content.filter_heading.filter_by_task') }}',
                something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
                Spam_t: '{{ trans('content.service_ticket_fields.Spam') }}',
                Merge_Primary_t: '{{ trans('content.service_ticket_fields.Merge_Primary') }}',
                Creator_Info_t: '{{ trans('content.service_ticket_fields.Creator_Info') }}',
                Department_Info_t: '{{ trans('content.service_ticket_fields.Department_Info') }}',
                CreatedUpdated_At_t: '{{ trans('content.service_ticket_fields.CreatedUpdated_At') }}',
                Assgined_To_t: '{{ trans('content.service_ticket_fields.Assgined_To') }}',
                View_t: '{{ trans('content.service_ticket_fields.View') }}',
                Add_to_Merge_t: '{{ trans('content.service_ticket_fields.Add_to_Merge') }}',
                Make_Primary_t: '{{ trans('content.service_ticket_fields.Make_Primary') }}',
                Remove_t: '{{ trans('content.service_ticket_fields.Remove') }}',
                Primary_t: '{{ trans('content.service_ticket_fields.Primary') }}',
                No_Filter: '{{ trans('content.service_ticket_fields.No_Filter') }}',
                filter_by_status: '{{ trans('content.filter_heading.filter_by_status') }}',
                Filter_By_Priority: '{{ trans('content.filter_heading.Filter_By_Priority') }}',
                filter_by_department: '{{ trans('content.filter_heading.filter_by_department') }}',
                Filter_Based_on: '{{ trans('content.filter_heading.Filter_Based_on') }}',
                filter_by_merge: '{{ trans('content.filter_heading.filter_by_merge') }}',
                Filter_By_Problem_Category: '{{ trans('content.filter_heading.Filter_By_Problem_Category') }}',
                Filter_By_Sub_Category: '{{ trans('content.filter_heading.Filter_By_Sub_Category') }}',
                Filter_By_Ticket_Handler: '{{ trans('content.filter_heading.Filter_By_Ticket_Handler') }}',
                Filter_By_Ticket_Creator: '{{ trans('content.filter_heading.Filter_By_Ticket_Creator') }}',
                Select_the_User: '{{ trans('content.service_ticket_fields.Select_the_User') }}',
                select_device: '{{ trans('content.service_ticket_fields.select_device') }}',
                connect: '{{ trans('content.service_ticket_fields.connect') }}',
                enter_first_few_letter: '{{ trans('content.service_ticket_fields.enter_first_few_letter') }}',
                select_priority: '{{ trans('content.service_ticket_fields.select_priority') }}',
                No_Tickets_Found: '{{ trans('content.service_ticket_fields.No_Tickets_Found') }}',
                select_reply_to_account: '{{ trans('content.service_ticket_fields.select_reply_to_account') }}',
                New_Ticket: '{{ trans('content.service_ticket_fields.New_Ticket') }}',
                New_Schedular: '{{ trans('content.service_ticket_fields.New_Schedular') }}',
                Create: '{{ trans('content.service_ticket_fields.Create') }}',
                please_select_tickets: '{{ trans('content.service_ticket_fields.please_select_tickets') }}',
                atleast_2: '{{ trans('content.service_ticket_fields.atleast_2') }}',
                please_select_primary: '{{ trans('content.service_ticket_fields.please_select_primary') }}',
                please_enter_comments: '{{ trans('content.service_ticket_fields.please_enter_comments') }}',
                Select_Problem_Category: '{{ trans('content.service_ticket_fields.Select_Problem_Category') }}',
                Select_Sub_Category: '{{ trans('content.service_ticket_fields.Select_Sub_Category') }}',
                unable_to_add_ticket_merge: '{{ trans('content.service_ticket_fields.unable_to_add_ticket_merge') }}',
                ticket: '{{ trans('content.service_ticket_fields.ticket') }}',
                is_already_added_in_merge_list: '{{ trans('content.service_ticket_fields.is_already_added_in_merge_list') }}',
                has_been_added: '{{ trans('content.service_ticket_fields.has_been_added') }}',
                resolved: '{{ trans('content.service_ticket_fields.resolved') }}',
                maximum_10: '{{ trans('content.service_ticket_fields.maximum_10') }}',
                department_is_not_match: '{{ trans('content.service_ticket_fields.department_is_not_match') }}',
                unable_to_add_merge: '{{ trans('content.service_ticket_fields.unable_to_add_merge') }}',
                Select_User: '{{ trans('content.service_ticket_fields.Select_User') }}',
                Total_Tickets: '{{ trans('content.service_ticket_fields.Total_Tickets') }}',
                total_tickets: '{{ trans('content.service_ticket_fields.total_tickets') }}',
                total_assets: '{{ trans('content.service_ticket_fields.total_assets') }}',
                total_users: '{{ trans('content.service_ticket_fields.total_users') }}',
                assigned_to: '{{ trans('content.service_ticket_fields.assigned_to') }}',
                since: '{{ trans('content.service_ticket_fields.since') }}',
                no_of_ticket: '{{ trans('content.service_ticket_fields.no_of_ticket') }}',
                creator_info: '{{ trans('content.service_ticket_fields.creator_info') }}',
                department: '{{ trans('content.service_ticket_fields.department') }}',
                open_tickets: '{{ trans('content.service_ticket_fields.open_tickets') }}',
                last_feedback: '{{ trans('content.service_ticket_fields.last_feedback') }}',
                Status: '{{ trans('content.service_ticket_fields.Status') }}',
                priority: '{{ trans('content.service_ticket_fields.priority') }}',
                created_date: '{{ trans('content.service_ticket_fields.created_date') }}',
                updated_date: '{{ trans('content.service_ticket_fields.updated_date') }}',
                knowledge_based: '{{ trans('content.service_ticket_fields.knowledge_based') }}',
                ping_it_top: '{{ trans('content.service_ticket_fields.ping_it_top') }}',
                Feedback_Received: '{{ trans('content.service_ticket_fields.Feedback_Received') }}',
                Ticket_Details: '{{ trans('content.service_ticket_fields.Ticket_Details') }}',
                delete: '{{ trans('content.service_ticket_fields.delete') }}',
                merge: '{{ trans('content.service_ticket_fields.merge') }}',
                Transfer: '{{ trans('content.service_ticket_fields.Transfer') }}',
                View: '{{ trans('content.service_ticket_fields.View') }}',
                Update: '{{ trans('content.service_ticket_fields.Update') }}',
                Subject: '{{ trans('content.service_ticket_fields.Subject') }}',
                ticket_id: '{{ trans('content.service_ticket_fields.ticket_id') }}',
                Company: '{{ trans('content.service_ticket_fields.Company') }}',
                Related_Device: '{{ trans('content.service_ticket_fields.Related_Device') }}',
                Prob_Category: '{{ trans('content.service_ticket_fields.Prob_Category') }}',
                Prob_Subcategory: '{{ trans('content.service_ticket_fields.Prob_Subcategory') }}',
                are_you_delete: '{{ trans('content.service_ticket_fields.are_you_delete') }}',
                are_you_resolve: '{{ trans('content.service_ticket_fields.are_you_resolve') }}',
                Available: '{{ trans('content.service_ticket_fields.Available') }}',
                Available_Records: '{{ trans('content.service_ticket_fields.Available_Records') }}',
                Add_to_Spam_List: '{{ trans('content.service_ticket_fields.Add_to_Spam_List') }}',
                Mark_Spam: '{{ trans('content.service_ticket_fields.Mark_Spam') }}',
                are_you_pick: '{{ trans('content.service_ticket_fields.are_you_pick') }}',
                Assigned_Ticket_To: '{{ trans('content.service_ticket_fields.Assigned_Ticket_To') }}',
                Assigned_Ticket_From: '{{ trans('content.service_ticket_fields.Assigned_Ticket_From') }}',
                Changes_Done_By: '{{ trans('content.service_ticket_fields.Changes_Done_By') }}',
                Changes_Status_To: '{{ trans('content.service_ticket_fields.Changes_Status_To') }}',
                Changes_Status_From: '{{ trans('content.service_ticket_fields.Changes_Status_From') }}',
                Ticket_Marked_As_Spam: '{{ trans('content.service_ticket_fields.Ticket_Marked_As_Spam') }}',
                Ticket_Removed_From_Spam_List: '{{ trans('content.service_ticket_fields.Ticket_Removed_From_Spam_List') }}',
                Ticket_Is_Merged_with: '{{ trans('content.service_ticket_fields.Ticket_Is_Merged_with') }}',
                Primary_Ticket_And_Merged_with: '{{ trans('content.service_ticket_fields.Primary_Ticket_And_Merged_with') }}',
                Marked_this_as: '{{ trans('content.service_ticket_fields.Marked_this_as') }}',
                also_Commented_on_ticket: '{{ trans('content.service_ticket_fields.also_Commented_on_ticket') }}',
                Commented_on_ticket: '{{ trans('content.service_ticket_fields.Commented_on_ticket') }}',
                Changes_Priority_To: '{{ trans('content.service_ticket_fields.Changes_Priority_To') }}',
                Changes_Priority_From: '{{ trans('content.service_ticket_fields.Changes_Priority_From') }}',
                Changed_tats: '{{ trans('content.service_ticket_fields.Changed_tats') }}',
                To: '{{ trans('content.service_ticket_fields.to') }}',
                From: '{{ trans('content.service_ticket_fields.from') }}',
                Ticket_Transfer_Department: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Department') }}',
                Ticket_Transfer_Department_From: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Department_From') }}',
                Ticket_Transfer_Category_Changes_To: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Category_Changes_To') }}',
                Ticket_Transfer_Category_Changes_From: '{{ trans('content.service_ticket_fields.Ticket_Transfer_Category_Changes_From') }}',
                Ticket_Transfer_subCategory_Changes_To: '{{ trans('content.service_ticket_fields.Ticket_Transfer_subCategory_Changes_To') }}',
                Ticket_Transfer_subCategory_Changes_From: '{{ trans('content.service_ticket_fields.Ticket_Transfer_subCategory_Changes_From') }}',
                Ticket_Edit_Category_Changes_To: '{{ trans('content.service_ticket_fields.Ticket_Edit_Category_Changes_To') }}',
                Ticket_Edit_Category_Changes_From: '{{ trans('content.service_ticket_fields.Ticket_Edit_Category_Changes_From') }}',
                Ticket_Edit_subCategory_Changes_To: '{{ trans('content.service_ticket_fields.Ticket_Edit_subCategory_Changes_To') }}',
                Ticket_Edit_subCategory_Changes_From: '{{ trans('content.service_ticket_fields.Ticket_Edit_subCategory_Changes_From') }}',
                New_ticket_created: '{{ trans('content.service_ticket_fields.New_ticket_created') }}',
                select_ticket_type: '{{ trans('content.service_ticket_fields.select_ticket_type') }}',
                Ticket_History_Not_Available: '{{ trans('content.service_ticket_fields.Ticket_History_Not_Available') }}',
                are_you_star: '{{ trans('content.service_ticket_fields.are_you_star') }}',
                star: '{{ trans('content.service_ticket_fields.star') }}',
                tags: '{{ trans('content.service_ticket_fields.tags') }}',
                convert_kd: '{{ trans('content.service_ticket_fields.convert_kd') }}',
                created_via: '{{ trans('content.service_ticket_fields.Created_By') }}',
                filter_by_tag: '{{ trans('content.filter_heading.filter_by_tag') }}',
                filter_created_via: '{{ trans('content.filter_heading.filter_created_via') }}',
                filter_by_location_id: '{{ trans('content.filter_heading.filter_by_location_id') }}',
                closed_date: '{{ trans('content.service_ticket_fields.closed_date') }}',
                tat_expire: '{{ trans('content.service_ticket_fields.tat_expire') }}',
                feedback: '{{ trans('content.service_ticket_fields.feedback') }}',
                feedback_is: '{{ trans('content.service_ticket_fields.feedback_is') }}',
                filter_by_base_location_id: '{{ trans('content.filter_heading.filter_by_base_location_id') }}',
                filter_by_ticket_or_service_request: '{{ trans('content.filter_heading.filter_by_ticket_or_service_request') }}',
                Filter_By_Creator_Logger: '{{ trans('content.filter_heading.Filter_By_Creator_Logger') }}',
                filter_by_feedback: '{{ trans('content.filter_heading.filter_by_feedback') }}',
                Filter: '{{ trans('content.user_fields.Filter') }}',
                comment_summer: '{{ trans('content.service_ticket_fields.share_comment') }}',
                edit_t: '{{ trans('content.service_ticket_fields.edit_ticket') }}',
                ticket_type: '{{ trans('content.service_ticket_fields.ticket_type') }}',
                upload_file: '{{ trans('content.service_ticket_fields.upload_file') }}',
                delete_record: '{{ trans('translation.custom_tab_fields.delete_record') }}',
                edit: '{{ trans('translation.custom_tab_fields.edit') }}',
                customFieldSet: '{{ trans('content.filter_heading.filter_by_custom_field') }}',
                customField: '{{ trans('content.filter_heading.filter_by_custom_field') }}',
                New_ticket_created_to: '{{ trans('content.service_ticket_fields.New_ticket_created_to') }}',
                Enter_starting: '{{ trans('content.service_ticket_fields.Enter_starting') }}',
                please_select: '{{ trans('content.service_ticket_fields.please_select') }}',
                atleast_delete: '{{ trans('content.service_ticket_fields.atleast_delete') }}',
                atleast_resolve: '{{ trans('content.service_ticket_fields.atleast_resolve') }}',
                atleast_assign: '{{ trans('content.service_ticket_fields.atleast_assign') }}',
                Remove_Star: '{{ trans('content.service_ticket_fields.Remove_Star') }}',
                add_star_msg: '{{ trans('content.service_ticket_fields.add_star_msg') }}',
                add_remove_msg: '{{ trans('content.service_ticket_fields.add_remove_msg') }}',
                Ticket_History: '{{ trans('content.service_ticket_fields.Tkt_History') }}',
                tasks: '{{ trans('content.service_ticket_fields.tasks') }}',
                add_task: '{{ trans('content.service_ticket_fields.add_task') }}',
                upload_note: "{{ trans('content.service_ticket_fields.upload_note') }}",
                Add_Attachment: "{{ trans('content.service_ticket_fields.Add_Attachment') }}",
                upload_maximum_limit: "{{ trans('content.service_ticket_fields.upload_maximum_limit') }}",
                file_name: "{{ trans('content.service_ticket_fields.file_name') }}",
                comment: "{{ trans('content.service_ticket_fields.Comment') }}",
                search_placeholder: '{{ trans('content.task_management.press_enter_with_search_text') }}',
                search: '{{ trans('content.task_management.Search') }}',
                refresh: '{{ trans('content.task_management.Refresh_List') }}',
            };
            config.sort_dir = {
                id: 1,
                dir: 2
            };

            config.sort_fields = {!! json_encode($sort_fields) !!};
            config.locations = {!! json_encode($locations) !!};
            config.base_locations = {!! json_encode($base_locations) !!};
            config.created_via_filter = {!! json_encode($created_via_filter) !!};
            config.tags = {!! json_encode($tags) !!};
            config.ticket_handlers = {!! json_encode($ticket_handlers) !!};
            config.status_filter = {!! json_encode($status_filter) !!};
            config.priorities = {!! json_encode($priorities) !!};
            config.permissions = {!! json_encode($permissionArray) !!};
            config.url.image = "{{ asset('images/notask.png') }}";
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
            new ArchivedTicketList(config);
            new MyApp(config);
        });
    </script>
@endpush
