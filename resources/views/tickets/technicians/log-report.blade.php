@extends('layouts.layout1')
@section('title', trans("technician_status.tech_log_report"))

@section('content')
<div id="tech-log-report-wrapper">
    {{-- Header with actions --}}
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">
            {{ trans("technician_status.tech_log_report") }}
        </h3>

        <div class="d-flex gap-8">
            <button class="header-icon-btn header-icon-btn-sm btn-toggle-all" data-bs-toggle="tooltip" type="button" title="{{ trans('technician_status.expand_all') }}">
                <i class="bi bi-arrows-expand"></i>
            </button>

            {{-- FILTER BUTTON (opens modal) --}}
            <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="modal" data-bs-target="#filterModal" title="{{ trans('technician_status.filter') }}">
                <svg viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z" fill="currentColor"/>
                </svg>
                <span class="b5-text opacity-50">{{ trans('technician_status.filter') }}</span>
                <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
            </button>

            {{-- Optional: export button --}}
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-download-report" data-bs-toggle="tooltip" type="button" title="{{ trans('user.user_toolbar.download') }}">
                <svg viewBox="0 0 18 18" fill="none">
                        <path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.61011 11.921 8.80098 12.0006 9 12.0006C9.19902 12.0006 9.38989 11.921 9.53063 11.7806L13.2806 8.03063C13.421 7.88989 13.5004 7.69902 13.5004 7.5C13.5004 7.30098 13.421 7.11011 13.2806 6.96937C13.1399 6.82864 12.949 6.74958 12.75 6.74958C12.551 6.74958 12.3601 6.82864 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path>
                    </svg>
            </button>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">
                    {{-- Top Controls --}}
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        {{-- Page length --}}
                        <div class="col-auto">
                            <select class="userModulePageLenth user-list-page-length amg-table-pagination-dropdown" id="logPageLength">
                                <option value="10">{{ trans('technician_status.show_10') }}</option>
                                <option value="25">{{ trans('technician_status.show_25') }}</option>
                                <option value="50">{{ trans('technician_status.show_50') }}</option>
                                <option value="100">{{ trans('technician_status.show_100') }}</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>
                        {{-- Search --}}
                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon btn-searchbox" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                                <input type="text" id="techLogSearch" class="amg-list-searchbar__input plain-search" placeholder="{{ trans('technician_status.press_enter_with_Search') }}">
                            </div>
                        </div>

                        {{-- Hidden input to store date range (used by table reload) --}}
                        <input type="hidden" name="daterange" id="daterange">

                        {{-- Refresh button --}}
                        <div>
                            <button class="amg-refresh-btn btn-reload-list" id="btnTechFilter">
                                <i class="bi bi-arrow-clockwise amg-refresh-btn__icon"></i>
                                <span>{{ trans('technician_status.Refresh_List') }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Technician Info Card (compact) --}}
                    <div class="tech-info-card mb-2 p-2">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                    </svg>
                                    <span class="text-muted small">{{ trans('technician_status.name') }}:</span>
                                    <strong>{{ $technician->getGuranteedNameText() }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center gap-2">
                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings" viewBox="0 0 16 16">
                                        <path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
                                        <path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z"/>
                                    </svg>
                                    <span class="text-muted small">{{ trans('technician_status.department_name') }}:</span>
                                    <strong>{{ $department }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-activity" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2"/>
                                    </svg>
                                    <span class="text-muted small">{{ trans('technician_status.status') }}:</span>
                                    <span id="liveStatus" class="tech-status-pill tech-status-pill--{{ $technician->is_logged_in ? 'active' : 'inactive' }}">
                                        <span class="tech-status-icon">
                                            <span class="status-dot {{ $technician->is_logged_in ? 'active-dot' : 'inactive-dot' }}"></span>
                                        </span>
                                        <span class="status-text">{{ $technician->is_logged_in ? trans('technician_status.active') : trans('technician_status.inactive') }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-check" viewBox="0 0 16 16">
                                        <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z"/>
                                        <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686"/>
                                    </svg>
                                                                            <span class="text-muted small">{{ trans('technician_status.email') }}:</span>
                                    <strong>{{ $email }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="list-view-panel">
                        <div class="table-responsive">
                            <table id="mainTable" class="table display app-data-table">
                                <thead>
                                    <tr>
                                        <th width="5%"></th>
                                        <th><h4 class="b2-text">{{ trans('technician_status.date') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('technician_status.start_time') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('technician_status.end_time') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('technician_status.total_work') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('technician_status.total_break') }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans('technician_status.total_duration') }}</h4></th>
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

{{-- ============================================= --}}
{{--  NEW FILTER MODAL (only date range picker)   --}}
{{-- ============================================= --}}
<div class="amg-modal modal fade"
     id="filterModal"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded-5 bg-white">

            {{-- Modal Header --}}
            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title px-4">
                    {{ trans("technician_status.filter_by_date_range") }}
                </h3>
                <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                    <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none">
                        <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body: Only Date Range Picker --}}
            <div class="modal-body">
                <div class="row g-3 px-4">
                    <div class="col-12">
                        <label class="form-label b1-text" for="modalDateRange">
                            {{ trans("technician_status.select_date_range") }}
                        </label>
                        <div id="modalDateRange" class="form-control">
                            <span class="text-muted">{{ trans("technician_status.select_date_range") }}</span> &nbsp;
                            <i class="bi bi-calendar-range"></i>
                            <input type="hidden" name="daterange_modal" id="daterange_modal">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer justify-content-start mb-4 pb-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button type="button" id="modalFilterSubmit" class="amg-btn amg-btn-primary text-white s2-text btn-filter" style="min-width: 200px;">
                        {{ trans("technician_status.apply_filter") }}
                    </button>
                    <button type="button" id="modalFilterClear" class="amg-btn amg-btn-ghost s2-text bg-black text-white hover-opacity-80 btn-clear-filter" style="min-width: 200px;">
                        {{ trans("technician_status.clear_filter") }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="amg-modal modal fade"
     id="exportColumnsModal"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded-5 bg-white">

            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title px-4">
                    {{ trans("technician_status.select_columns_to_export") }}
                </h3>
                <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                    <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none">
                        <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="px-4">
                    <div class="tech-export-range mb-3">
                        <span class="text-muted small">{{ trans("technician_status.selected_range") }}:</span>
                        <strong id="exportRangeText">{{ trans("technician_status.default_export_note") }}</strong>
                    </div>
                    <div class="tech-export-columns">
                        <div class="form-check">
                            <input class="form-check-input tech-export-column" type="checkbox" value="date" id="exportColumnDate" checked>
                            <label class="form-check-label" for="exportColumnDate">{{ trans("technician_status.date") }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input tech-export-column" type="checkbox" value="start_time_format" id="exportColumnStart" checked>
                            <label class="form-check-label" for="exportColumnStart">{{ trans("technician_status.start_time") }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input tech-export-column" type="checkbox" value="end_time_format" id="exportColumnEnd" checked>
                            <label class="form-check-label" for="exportColumnEnd">{{ trans("technician_status.end_time") }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input tech-export-column" type="checkbox" value="work" id="exportColumnWork" checked>
                            <label class="form-check-label" for="exportColumnWork">{{ trans("technician_status.total_work") }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input tech-export-column" type="checkbox" value="break" id="exportColumnBreak" checked>
                            <label class="form-check-label" for="exportColumnBreak">{{ trans("technician_status.total_break") }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input tech-export-column" type="checkbox" value="total" id="exportColumnTotal" checked>
                            <label class="form-check-label" for="exportColumnTotal">{{ trans("technician_status.total_duration") }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-start mb-4 pb-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button type="button" id="exportColumnsSubmit" class="amg-btn amg-btn-primary text-white s2-text" style="min-width: 200px;">
                        {{ trans("technician_status.download_excel") }}
                    </button>
                    <button type="button" class="amg-btn amg-btn-ghost s2-text bg-black text-white hover-opacity-80" data-bs-dismiss="modal" style="min-width: 200px;">
                        {{ trans("technician_status.cancel") }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('assets/libs/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
{{-- Additional minimal overrides for consistency --}}
<style>
    #tech-log-report-wrapper .tech-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        border: 1px solid transparent;
    }
    #tech-log-report-wrapper .tech-status-pill--active {
        color: #16a34a;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    #tech-log-report-wrapper .tech-status-pill--inactive {
        color: #dc2626;
        background: #fef2f2;
        border-color: #fecaca;
    }
    [data-bs-theme="dark"] #tech-log-report-wrapper .tech-status-pill--break{
        background: transparent;
    }

    [data-bs-theme="dark"] #tech-log-report-wrapper .tech-status-pill--inactive {
        background: transparent;
    }

    #tech-log-report-wrapper .tech-status-pill--break {
        color: #d97706;
        background: #fffbeb;
        border-color: #fde68a;
    }

  
    .status-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .active-dot { background-color: #5bd810; }
    .inactive-dot { background-color: #f74c3f; }
    .break-dot { background-color: #f8a01a; }

    /* Child row inner table styling */
    .inner-container {
        padding: 20px;
        /* background: #f9fafb; */
        border-left: 4px solid #dc2626;
        margin: 10px 0;
        border-radius: 8px;
    }
    .inner-table {
        width: 100%;
        /* background: white; */
        border-radius: 8px;
        overflow: hidden;
    }
    .inner-table th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 10px 12px;
    }
    .inner-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .show-more {
        display: inline-block;
        border: 0;
        background: #dc2626;
        color: white;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .show-more:hover {
        background: #b91c1c;
        transform: translateY(-1px);
    }
    .amg-status-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 38px;
        padding: 0 14px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: white;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.18s;
    }
    .amg-status-action-btn:hover {
        background: #f5f5f5;
        border-color: #cc3333;
    }
    .tech-log-expand-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        /* background: #fff; */
        /* color: #64748b; */
    }
    .tech-log-expand-btn:hover {
        color: #cc3333;
        border-color: #cc3333;
    }
    .tech-export-columns {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        text-align: left;
        font-size: 14px;
        margin-top: 10px;
    }
</style>
@endpush

@push('scripts')
@php
    $technicianLogTranslations = [
        'select_date_range' => trans('technician_status.select_date_range'),
        'apply' => trans('technician_status.apply'),
        'cancel' => trans('technician_status.cancel'),
        'clear_filter' => trans('technician_status.clear_filter'),
        'filter' => trans('technician_status.filter'),
        'filter_by_date_range' => trans('technician_status.filter_by_date_range'),
        'apply_filter' => trans('technician_status.apply_filter'),
        'Search' => trans('technician_status.Search'),
        'press_enter_with_Search' => trans('technician_status.press_enter_with_Search'),
        'date' => trans('technician_status.date'),
        'start_time' => trans('technician_status.start_time'),
        'end_time' => trans('technician_status.end_time'),
        'total_work' => trans('technician_status.total_work'),
        'total_break' => trans('technician_status.total_break'),
        'total_duration' => trans('technician_status.total_duration'),
        'activity' => trans('technician_status.activity'),
        'reason' => trans('technician_status.reason'),
        'start' => trans('technician_status.start'),
        'end' => trans('technician_status.end'),
        'total' => trans('technician_status.total'),
        'work' => trans('technician_status.work'),
        'break' => trans('technician_status.break'),
        'active' => trans('technician_status.active'),
        'inactive' => trans('technician_status.inactive'),
        'na' => trans('technician_status.na'),
        'show_more' => trans('technician_status.show_more'),
        'show_less' => trans('technician_status.show_less'),
        'expand' => trans('technician_status.expand'),
        'collapse' => trans('technician_status.collapse'),
        'expand_all' => trans('technician_status.expand_all'),
        'collapse_all' => trans('technician_status.collapse_all'),
        'download_report' => trans('technician_status.download_report'),
        'download_excel' => trans('technician_status.download_excel'),
        'export_activity_report' => trans('technician_status.export_activity_report'),
        'selected_range' => trans('technician_status.selected_range'),
        'note' => trans('technician_status.note'),
        'default_export_note' => trans('technician_status.default_export_note'),
        'select_columns_to_export' => trans('technician_status.select_columns_to_export'),
        'select_at_least_one_column' => trans('technician_status.select_at_least_one_column'),
        'error' => trans('technician_status.error'),
        'no_records_found' => trans('technician_status.no_records_found'),
        'no_matching_records' => trans('technician_status.no_matching_records'),
        'processing' => trans('technician_status.processing'),
        'today' => trans('technician_status.today'),
        'yesterday' => trans('technician_status.yesterday'),
        'last_7_days' => trans('technician_status.last_7_days'),
        'last_30_days' => trans('technician_status.last_30_days'),
        'this_month' => trans('technician_status.this_month'),
        'last_month' => trans('technician_status.last_month'),
        'hours' => trans('technician_status.hours'),
        'minutes' => trans('technician_status.minutes'),
        'minute_short' => trans('technician_status.minute_short'),
    ];
@endphp
<script src="{!! CommonHelper::asset('assets/js\extra-libs/moment/moment.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('assets/libs/daterangepicker/daterangepicker.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/live-status/technician-log-report.js') !!}"></script>
<script>
    $(document).ready(function () {
        // Pass configuration to your existing MyApp class
        const config = {
            urls: {
                logs: "{{ route('getUserlogs',['id'=>$technician->id]) }}",
                status: "{{ route('getTechCurrentStatus',['id'=>$technician->id]) }}",
                exportReport: "{{ route('downloadTechLogs',['id'=>$technician->id]) }}"
            },
            technicianId: "{{ $technician->id }}",
            translations: @json($technicianLogTranslations)
        };
        new MyApp(config);
    });
</script>
@endpush
