{{--
/**
* ------------------------------------------------------------
* File: config.blade.php
* Module: User Locations
* UL/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #UL-002
* Created On: 2026-04-29
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Changes for the user locations using pace.js plugin instead of loader state
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('user_locations.view.header'))

{{-- adding this for pace.js plugin only applying in user locations module --}}
@section('body-class', 'user-locations-page')

@section('content')
    <main class="main-content" id="mainContent">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between px-4">
                <div class="mb-0">
                    <h3 class="h3-text mb-0">{{ trans('user_locations.view.page_heading') }}</h3>
                </div>
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-download btn-export-maintenance"
                    type="button" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('user_locations.view.download') }}">
                    <svg width="17" height="20" viewBox="0 0 17 20" fill="none">
                        <path
                            d="M16.2806 5.46938L11.0306 0.219375C10.9609 0.149749 10.8782 0.094539 10.7871 0.0568979C10.6961 0.0192569 10.5985 -7.72394e-05 10.5 2.31899e-07H1.5C1.10218 2.31899e-07 0.720644 0.158035 0.43934 0.43934C0.158035 0.720645 0 1.10218 0 1.5V18C0 18.3978 0.158035 18.7794 0.43934 19.0607C0.720644 19.342 1.10218 19.5 1.5 19.5H15C15.3978 19.5 15.7794 19.342 16.0607 19.0607C16.342 18.7794 16.5 18.3978 16.5 18V6C16.5001 5.90148 16.4807 5.80391 16.4431 5.71286C16.4055 5.62182 16.3503 5.53908 16.2806 5.46938ZM11.25 2.56031L13.9397 5.25H11.25V2.56031ZM15 18H1.5V1.5H9.75V6C9.75 6.19891 9.82902 6.38968 9.96967 6.53033C10.1103 6.67098 10.3011 6.75 10.5 6.75H15V18ZM11.0306 12.2194C11.1004 12.289 11.1557 12.3717 11.1934 12.4628C11.2312 12.5538 11.2506 12.6514 11.2506 12.75C11.2506 12.8486 11.2312 12.9462 11.1934 13.0372C11.1557 13.1283 11.1004 13.211 11.0306 13.2806L8.78063 15.5306C8.71097 15.6004 8.62825 15.6557 8.5372 15.6934C8.44616 15.7312 8.34856 15.7506 8.25 15.7506C8.15144 15.7506 8.05384 15.7312 7.96279 15.6934C7.87175 15.6557 7.78903 15.6004 7.71937 15.5306L5.46937 13.2806C5.32864 13.1399 5.24958 12.949 5.24958 12.75C5.24958 12.551 5.32864 12.3601 5.46937 12.2194C5.61011 12.0786 5.80098 11.9996 6 11.9996C6.19902 11.9996 6.38989 12.0786 6.53063 12.2194L7.5 13.1897V9C7.5 8.80109 7.57902 8.61032 7.71967 8.46967C7.86032 8.32902 8.05109 8.25 8.25 8.25C8.44891 8.25 8.63968 8.32902 8.78033 8.46967C8.92098 8.61032 9 8.80109 9 9V13.1897L9.96937 12.2194C10.039 12.1496 10.1217 12.0943 10.2128 12.0566C10.3038 12.0188 10.4014 11.9994 10.5 11.9994C10.5986 11.9994 10.6962 12.0188 10.7872 12.0566C10.8783 12.0943 10.961 12.1496 11.0306 12.2194Z"
                            fill="currentColor" />
                    </svg>
                </button>
        </div>

        {{-- using this id here since css is written acc. to #main-user-list-wrapper --}}
        <div class="tab-pane fade show active" id="main-user-list-wrapper">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                        <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                        <select id="showSelect"
                            class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                <option value="10" selected>{{ trans('user_locations.view.show') }} (10)</option>
                                <option value="25">{{ trans('user_locations.view.show') }} (25)</option>
                                <option value="50">{{ trans('user_locations.view.show') }} (50)</option>
                                <option value="100">{{ trans('user_locations.view.show') }} (100)</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>
                    <div style="width: 280px;">
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input" id="tableSearch"
                                    placeholder="{{ trans('user_locations.view.search') }}...">
                            </div>
                        </div>
                        <button class="amg-refresh-btn btn-reload-list">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('user_locations.view.refresh') }}</span>
                        </button>
                    </div>
                    {{-- <table id="mytable" class="table display">
                    <thead>
                        <tr>
                            <th><h4>{{ trans("user_locations.view.user_info") }}</h4></th>
                            <th><h4>{{ trans("user_locations.view.location") }}</h4></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table> --}}
                    <div class="permission-tab-wrapper">
                        <div class="container-fluid py-3">
                            <div class="row g-3">
                                {{-- Left: users list --}}
                                <div class="col-lg-3 col-md-4">
                                    <div class="card rounded-4 overflow-hidden">
                                        <div class="card-body p-0 show-permissions">
                                            <div id="usersList">
                                                <div class="nav flex-column nav-pills blue-pills mb-4 mb-md-0"
                                                    id="usersContainer" role="tablist" aria-orientation="vertical">
                                                </div>
                                                <div id="loadMoreTrigger" class="text-center p-2 d-none">Loading...</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- user locations --}}
                                <div class="col-lg-9 col-md-8">
                                    <div class="card rounded-4 d-none" id="locationsList">
                                        <div class="card-body">
                                            <div class="tab-content" id="v-pills-tabContent"></div>
                                        </div>
                                    </div>
                                </div>

                                <div id="no-data" class="text-center col-lg-12 col-md-12 d-none">
                                    <h2>No Data Found</h2>
                                </div>

                                <div id="loader" class="col-lg-12 col-md-12 text-center d-none">
                                    <button class="amg-btn amg-btn-primary amg-btn-loading">
                                        Processing
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var config = {};
            config.url = {};
            config.url.get_users = "{{ url('locations/config/ajax-user-privileges') }}";
            config.url.update_privilege = "{{ url('locations/config/update-privilege') }}";
            config.url.update = "{{ url('tickets/update-config') }}";
            config.url.export_excel = "{{ url('export-location-configuration-excel') }}";
            config.locations = {!! json_encode($vd['locations']) !!};
            config.token = "{{ csrf_token() }}";
            config.translations = {
                // serach_option: '{{ trans('content.service_ticket_fields.serach_option') }}',
                // Search: '{{ trans('content.scheduled_maintenance.Search') }}',
                // Refresh_List: '{{ trans('content.scheduled_maintenance.Refresh_List') }}',
                Download: '{{ trans('user_locations.config.download') }}',
                valid_search: '{{ trans('user_locations.config.valid_search') }}',
                something_went_wrong_details: "{{ trans('user_locations.config.something_went_wrong_details') }}",
                update_changes: '{{ trans('user_locations.config.update_changes') }}',
                check_all: '{{ trans('user_locations.config.check_all') }}',
                something_went_wrong: '{{ trans('user_locations.config.something_went_wrong') }}',
                copy_email: '{{ trans('user_locations.config.copy_email') }}',
                no_locations: '{{ trans('user_locations.config.no_locations') }}'
            };
            new TicketConfig(config);
        });
    </script>

    <script src="{!! CommonHelper::asset('nfy/plugins/pace/pace.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/support_validate.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/location/config.js') !!}"></script>
@endpush
