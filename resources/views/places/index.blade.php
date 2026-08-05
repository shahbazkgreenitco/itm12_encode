{{--
/**
* ------------------------------------------------------------
* File: index.blade.php
* Module: Internal Places
* IP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #IP-002
* Created On: 2026-04-29
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.2] - Changes for the company dropdown not working issue.
* [1.0.1] - Changes in the layout for the pagination section going under the footer issue
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('internal_place.view.header'))

@section('content')

<main class="main-content" id="mainContent">
  <div class="container-fluid px-0">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between px-4">
        <div class="mb-0">
            <h3 class="h3-text mb-0">{{ trans("internal_place.view.page_heading") }}</h3>
        </div>
        <div class="d-flex gap-8">
            {{-- <button class="header-icon-btn-only header-icon-btn-only-sm open-add-modal"
                title="{{ trans('internal_place.view.add') }}">
            <svg viewBox="0 0 20 20" fill="none">
                <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            </svg>
            </button> --}}
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-download btn-internal-place-export"
                data-bs-toggle="tooltip" data-bs-original-title="{{ trans('internal_place.view.download') }}">
                <svg viewBox="0 0 20 20" fill="none">
                    <path d="M10 3V13M10 13L6 9M10 13L14 9M3 17H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                </svg>
            </button>
            <button class="header-icon-btn-only header-icon-btn-only-sm btn-import btn-import-place"
                 data-bs-toggle="tooltip" data-bs-original-title="{{ trans('internal_place.view.import_place') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none"><path d="M18.0006 7.50042C18.0006 7.69933 17.9216 7.8901 17.7809 8.03075C17.6403 8.1714 17.4495 8.25042 17.2506 8.25042H7.50059C7.30168 8.25042 7.11091 8.1714 6.97026 8.03075C6.82961 7.8901 6.75059 7.69933 6.75059 7.50042C6.75059 7.3015 6.82961 7.11074 6.97026 6.97009C7.11091 6.82943 7.30168 6.75042 7.50059 6.75042H17.2506C17.4495 6.75042 17.6403 6.82943 17.7809 6.97009C17.9216 7.11074 18.0006 7.3015 18.0006 7.50042ZM7.50059 2.25042H17.2506C17.4495 2.25042 17.6403 2.1714 17.7809 2.03075C17.9216 1.89009 18.0006 1.69933 18.0006 1.50042C18.0006 1.3015 17.9216 1.11074 17.7809 0.970087C17.6403 0.829435 17.4495 0.750417 17.2506 0.750417H7.50059C7.30168 0.750417 7.11091 0.829435 6.97026 0.970087C6.82961 1.11074 6.75059 1.3015 6.75059 1.50042C6.75059 1.69933 6.82961 1.89009 6.97026 2.03075C7.11091 2.1714 7.30168 2.25042 7.50059 2.25042ZM17.2506 12.7504H0.75059C0.551678 12.7504 0.360912 12.8294 0.22026 12.9701C0.0796077 13.1107 0.000589907 13.3015 0.000589907 13.5004C0.000589907 13.6993 0.0796077 13.8901 0.22026 14.0307C0.360912 14.1714 0.551678 14.2504 0.75059 14.2504H17.2506C17.4495 14.2504 17.6403 14.1714 17.7809 14.0307C17.9216 13.8901 18.0006 13.6993 18.0006 13.5004C18.0006 13.3015 17.9216 13.1107 17.7809 12.9701C17.6403 12.8294 17.4495 12.7504 17.2506 12.7504ZM0.219965 8.78104C0.28962 8.85077 0.372336 8.90609 0.463385 8.94384C0.554433 8.98158 0.652028 9.00101 0.75059 9.00101C0.849151 9.00101 0.946747 8.98158 1.0378 8.94384C1.12884 8.90609 1.21156 8.85077 1.28121 8.78104L5.03122 5.03104C5.10095 4.96139 5.15627 4.87867 5.19401 4.78762C5.23175 4.69657 5.25118 4.59898 5.25118 4.50042C5.25118 4.40186 5.23175 4.30426 5.19401 4.21321C5.15627 4.12216 5.10095 4.03945 5.03122 3.96979L1.28121 0.219792C1.14048 0.0790615 0.949613 0 0.75059 0C0.551567 0 0.360695 0.0790615 0.219965 0.219792C0.0792344 0.360523 0.000172913 0.551394 0.000172913 0.750417C0.000172913 0.94944 0.0792344 1.14031 0.219965 1.28104L3.44028 4.50042L0.219965 7.71979C0.150233 7.78945 0.0949134 7.87216 0.0571702 7.96321C0.019427 8.05426 0 8.15186 0 8.25042C0 8.34898 0.019427 8.44657 0.0571702 8.53762C0.0949134 8.62867 0.150233 8.71139 0.219965 8.78104Z" fill="currentColor"/></svg>
            </button>
            <div class="column-toggle-wrapper" style="position: relative; display: inline-block;">
                {{-- Button that triggers the dropdown --}}
                <button class="header-icon-btn-only header-icon-btn-only-sm show-hide-columns"
                    data-bs-toggle="tooltip" title="{{ trans('suppliers.view.show_columns') }}">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M12 21V3M5.4 3h13.2A2.4 2.4 0 0 1 21 5.4v13.2a2.4 2.4 0 0 1-2.4 2.4H5.4A2.4 2.4 0 0 1 3 18.6V5.4A2.4 2.4 0 0 1 5.4 3"/></svg>
                </button>

                {{-- Dropdown menu --}}
                <div class="dropdown-menu column-visibility-div" id="columnVisibilityControls" style="width: max-content; min-width: 150px;">
                </div>
            </div>
            <button class="amg-btn amg-btn-primary open-add-modal" type="button" data-bs-toggle="tooltip" title="{{ trans('internal_place.view.add_button') }}">
                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" ><path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" /></svg>
                <span>{{ trans('internal_place.view.add_button') }}</span>
            </button>
        </div>
    </div>
    {{-- <main class="main-content" id="mainContent"> --}}
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
                <table id="mytable" class="table amg-datatable display">
                    <thead>
                        <tr>
                            <th><h4>{{ trans("internal_place.table.id") }}</h4></th>
                            <th><h4>{{ trans("internal_place.table.place_name") }}</h4></th>
                            <th><h4>{{ trans("internal_place.table.branch_code") }}</h4></th>
                            <th><h4>{{ trans("internal_place.table.location") }}</h4></th>
                            <th><h4>{{ trans("internal_place.table.company") }}</h4></th>
                            <th><h4>{{ trans("internal_place.table.updated_at") }}</h4></th>
                            <th><h4>{{ trans("internal_place.table.actions") }}</h4></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
    @include('places.modal_html')
</main>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script type="text/javascript">
    var config = {};
    config.url = {};
    config.url.list = "{{ url('jx-internal-places') }}";
    config.url.add = "{{ url('internal-places/ajax-add') }}";
    config.url.edit = "{{ url('internal-places/ajax-edit') }}";
    config.url.get = "{{ url('internal-places/ajax-get') }}";
    config.url.delete = "{{ url('internal-places/ajax-delete') }}";
    config.permissions = {!! json_encode($permissionArray) !!};
    config.url.download_url = "{{ url('export-internal-places') }}";
    config.url.import_url = "{{ url('internal-places/import') }}";
    config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
    config.token = "{{ csrf_token() }}";
    config.company_default = {!! json_encode($vd->companies) !!};
    config.getCompanyUsers = "{{ url('getCompanyByUserAccess') }}";
    config.datatable_translations=  @json(trans('datatable.datatable'));
    config.translations = {
		edit_internal_place: '{{ trans('internal_place.config.edit_internal_place') }}',
		delete_internal_place: '{{ trans('internal_place.config.delete_internal_place') }}',
		select_place_placeholder: '{{ trans('internal_place.config.select_location_placeholder') }}',
		select_company_placeholder: '{{ trans('internal_place.config.select_company_placeholder') }}',
		save: '{{ trans('internal_place.config.save') }}',
		something_went_wrong: '{{ trans('internal_place.config.something_went_wrong') }}',
		are_you_delete: '{{ trans('internal_place.config.delete_message') }}',
		add_internal_place: '{{ trans('internal_place.config.add_internal_place') }}',
		edit_internal_place_modal_title: '{{ trans('internal_place.config.edit_internal_place_modal_title') }}'
    };
</script>
<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/place/index.js') !!}"></script>

@endpush
