@extends('layouts.layout1')
@section('title', trans("technician_status.tech_live_status"))

@section('content')

<div id="main-live-status-wrapper">

    {{-- Header --}}
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">
            {{ trans("technician_status.tech_live_status") }}
        </h3>
        <div class="d-flex gap-8">
            
        </div>
    </div>

    {{-- Main Content --}}
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">
                    {{-- Top Controls --}}
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="col-auto">
                            <select class="userModulePageLenth user-list-page-length amg-table-pagination-dropdown">
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
                                 <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
								</svg>

                                <input type="text"
                                    class="amg-list-searchbar__input searchbox"
                                    placeholder="{{ trans('technician_status.Search') }}">
                            </div>
                        </div>

						<div>
							 <button class="amg-refresh-btn btn-reload-list">
								<svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
								</svg>
								<span>{{ trans('technician_status.Refresh_List') }}</span>
							</button>
						</div>

                        <div class="d-flex align-items-center gap-2">
                            <button class="amg-status-action-btn amg-status-action-btn--active btna-active" type="button" data-bs-toggle="tooltip" title="{{ trans('technician_status.Active_user') }}">
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <rect width="16" height="16" rx="8" fill="#186B43"/>
                                    <circle cx="8" cy="8" r="3" fill="white"/>
                                </svg>
                                <span>{{ trans('technician_status.Active_user') }}</span>
                            </button>

                            <button class="amg-status-action-btn amg-status-action-btn--inactive btnd-inactive" type="button" data-bs-toggle="tooltip" title="{{ trans('technician_status.status_user') }}">
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <rect width="16" height="16" rx="8" fill="#F12F35"/>
                                    <circle cx="8" cy="8" r="3" fill="white"/>
                                </svg>
                                <span>{{ trans('technician_status.status_user') }}</span>
                            </button>
                        </div>

                    </div>

                    {{-- Table --}}
                    <div class="list-view-panel">
                        <div class="table-responsive">
                            <table id="mytable" class="table display app-data-table">
                                <thead>
                                    <tr>
                                        {{-- <th>
                                            <div class="amg-table-checkbox-col d-flex align-items-center">
                                                <input type="checkbox" class="chkParent form-check-input">                                                
                                            </div>
                                        </th> --}}
                                        <th><input class="form-check-input chkParent" type="checkbox"></th>
                                        <th><h4 class="b2-text">{{ trans("technician_status.name") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("technician_status.department_name") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("technician_status.status") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("technician_status.comment") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("technician_status.location") }}</h4> </th>
                                        <th><h4 class="b2-text">{{ trans('user.table_headers.updated_on') }}</h4> </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include("tickets.technicians.logactivity")
</div>
@endsection

@push('css')
<style>

    .table .active-user,
    .table .inactive-user,
    .table .ideal-user {
        display: inline-block;
        width: .8125rem;
        height: .8125rem;
        margin: 0 0 -.0625rem 0;
        border-radius: 50%;
    }

    .table .active-user {
        background: #5bd810;
    }

    .table .inactive-user {
        background: #f74c3f;
    }

    .table .ideal-user {
        background: #f8a01a;
    }

    .makeInActiveBtn,
    .makeActiveBtn {
        cursor: pointer;
    }

    #main-live-status-wrapper .amg-status-action-btn {
        display: inline-flex;
        align-items: center;
        gap: .4375rem;
        height: 2.125rem;
        padding: 0 .875rem;
        border: 1.5px solid #e0e0e0;
        border-radius: .5rem;
        background: #fff;
        color: #7f7f7f;
        font-size: .75rem;
        line-height: 1;
        white-space: nowrap;
        transition: background .18s ease, border-color .18s ease, color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    #main-live-status-wrapper .amg-status-action-btn svg {
        width: 1.125rem;
        height: 1.125rem;
        flex: 0 0 auto;
    }

    #main-live-status-wrapper .amg-status-action-btn:hover {
        background: #f5f5f5;
        border-color: #d0d5dd;
        transform: translateY(-1px);
    }

    #main-live-status-wrapper .amg-status-action-btn--active {
        color: #16a34a;
    }

    #main-live-status-wrapper .amg-status-action-btn--inactive {
        color: #f12f35;
    }

    [data-bs-theme="dark"] #main-live-status-wrapper .amg-status-action-btn {
        background: #2A2A2A;
        border-color: #3A3A3A;
    }

    [data-bs-theme="dark"] #main-live-status-wrapper .amg-status-action-btn:hover {
        background: #3f3f3f;
        border-color: #4A4A4A;
    }

    #main-live-status-wrapper .tech-status-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        flex-wrap: wrap;
    }

    #main-live-status-wrapper .tech-status-pill {
        display: inline-flex;
        align-items: center;
        gap: .375rem;
        min-height: 1.75rem;
        padding: .25rem .625rem;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1;
    }

    #main-live-status-wrapper .tech-status-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.125rem;
        height: 1.125rem;
        flex: 0 0 auto;
    }

    #main-live-status-wrapper .tech-status-icon svg {
        display: block;
        width: 1.125rem;
        height: 1.125rem;
    }

    #main-live-status-wrapper .tech-status-icon .ideal-user {
        width: 1rem;
        height: 1rem;
        margin: 0;
        box-shadow: 0 0 0 .1875rem rgba(217, 119, 6, .14);
    }

    #main-live-status-wrapper .tech-status-pill--active {
        color: #16a34a;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    #main-live-status-wrapper .tech-status-pill--idle {
        color: #d97706;
        background: #fffbeb;
        border-color: #fde68a;
    }

    #main-live-status-wrapper .tech-status-pill--inactive {
        color: #dc2626;
        background: #fef2f2;
        border-color: #fecaca;
    }

    #main-live-status-wrapper .tech-status-action {
        display: inline-flex;
        align-items: center;
        gap: .3125rem;
        min-height: 1.75rem;
        padding: .25rem .625rem;
        border: 1px solid #dbe2ef;
        border-radius: .375rem;
        background: #fff;
        color: #44506a;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1;
        text-decoration: none;
        transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease;
    }

    #main-live-status-wrapper .tech-status-action:hover {
        background: #eff2fa;
        border-color: #d0d5dd;
        color: #131927;
        transform: translateY(-1px);
    }

    #main-live-status-wrapper .tech-status-action--active {
        color: #16a34a;
    }

    #main-live-status-wrapper .tech-status-action--inactive {
        color: #dc2626;
    }

    [data-bs-theme="dark"] #main-live-status-wrapper .tech-status-pill--active {
        background: rgba(22, 163, 74, .12);
        border-color: rgba(34, 197, 94, .28);
    }

    [data-bs-theme="dark"] #main-live-status-wrapper .tech-status-pill--idle {
        background: rgba(217, 119, 6, .12);
        border-color: rgba(245, 158, 11, .28);
    }

    [data-bs-theme="dark"] #main-live-status-wrapper .tech-status-pill--inactive {
        background: rgba(220, 38, 38, .12);
        border-color: rgba(248, 113, 113, .28);
    }

    [data-bs-theme="dark"] #main-live-status-wrapper .tech-status-action {
        background: #2A2A2A;
        border-color: #3A3A3A;
        color: #E4E4E4;
    }

    span.Inactive {
        color: #fff;
        background-color: #f74c3f;
        border-radius: .1875rem;
        padding: .125rem .4375rem;
        font-size: .75rem;
    }

    span.Active {
        color: #fff;
        background-color: #5bd810;
        border-radius: .1875rem;
        padding: .125rem .4375rem;
        font-size: .75rem;
    }

    .btna-active {
        color: #5bd810;
    }

    .btnd-inactive {
        color: #f74c3f;
    }

    .switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 18px;
    }

    .switch input {
    opacity: 0;
    width: 0;
    height: 0;
    }

    .slider {
    position: absolute;
    cursor: pointer;
    background-color: #ccc;
    border-radius: 34px;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    transition: .3s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    border-radius: 50%;
    transition: .3s;
    }

    input:checked + .slider {
    background-color: #16a34a;
    }

    input:checked + .slider:before {
    transform: translateX(16px);
    }

    .tech-status-pill.makeActiveBtn:hover {
        background: #dcfce7;
        border-color: #86efac;
        cursor: pointer;
        transform: scale(1.05);
        transition: 0.2s;
    }

    .tech-status-pill.makeActiveBtn {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tech-status-pill.makeActiveBtn:hover {
        background: #dcfce7;
        border-color: #86efac;
        transform: scale(1.05);
    }

</style>
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/live-status/live-status.js') !!}"></script>
<script>
    $(document).ready(function () {
        var config = new Object;
        config.url = new Object;
        config.url.logUserActivity = "{{ route('logUserActivity') }}";
        config.url.logReport = "{{ url('technician/log-report') }}";
        config.url.livestatustUrl = "{{ url('technician/ajax-live-status') }}";
        config.url.bulkUserActivate = "{{ url('technician/bulkUserActivate') }}";
        config.url.bulkUserLogout = "{{ url('technician/bulkUserDeactivate') }}";
        config.url.status = "{{ route('getLogActivities') }}";
        config.token = "{{ csrf_token() }}";

        config.translations = {
            Search: '{{ trans('technician_status.Search') }}',
            Refresh_List: '{{ trans('technician_status.Refresh_List') }}',
            press_enter_with_Search: '{{ trans('technician_status.press_enter_with_Search') }}',
            are_you_activated: '{{ trans('content.user_fields.activated') }}',
            Active_user: '{{ trans('technician_status.Active_user') }}',
            status_user: '{{ trans('technician_status.status_user') }}',
            something_went_wrong: '{{ trans('content.user_group.something_went_wrong') }}',
            select_activity : '{{ trans('technician_status.select_activity') }}',
            select_technicien : '{{ trans('technician_status.select_technicien') }}',
            active_user : '{{trans('technician_status.active_user')}}',
            
        };

        new MyApp(config);

    });

</script>

@endpush
