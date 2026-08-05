{{-- * ------------------------------------------------------------
* File: index.blade.php
* Module: Allocation Type
* ALLT/26/01
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* [1.0.1] - Changes for the qa cards and added translations - Prithvi Pillai - 7/28/2026
* ------------------------------------------------------------ --}}

@extends('layouts.layout1')
@section('title', trans("config.allocation_type.allocation_type"))
@section('content')
<div id="main-user-list-wrapper">
    <section class="content">        
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans("config.allocation_type.allocation_type") }}</h3>
            <div class="d-flex gap-8">         
                {{-- Download Excel --}}
               
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-allocations-export" type="button" data-bs-toggle="tooltip" title="{{ trans('config.allocation_type.download') }}">
                    <svg viewBox="0 0 18 18" fill="none" ><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor" /></svg>
                </button>
                            
               
                <button class="amg-btn amg-btn-primary amg-btn-sm btn-add" type="button" data-bs-toggle="tooltip" title="{{ trans('config.allocation_type.add_new_allocation_type') }}">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" ><path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" /></svg>
                        <span>{{ trans("config.allocation_type.add_new_allocation_type") }}</span>
                </button>
              
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">                       
                            <div class="col-auto">
                                <select id="user-list-page-length" class="amg-table-pagination-dropdown userModulePageLenth list-page-length">
                                    <option value="10" selected>{{ trans("config.allocation_type.show") }} (10)</option>
                                    <option value="25">{{ trans("config.allocation_type.show") }} (25)</option>
                                    <option value="50">{{ trans("config.allocation_type.show") }} (50)</option>
                                    <option value="100">{{ trans("config.allocation_type.show") }} (100)</option>
                                </select>
                            </div>
                            <div class="flex-grow-1"></div>                    
                            <div>
                                <div class="amg-list-searchbar">
                                    <svg class="amg-list-searchbar__icon btn-searchbox" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                    </svg>
                                    <input type="text" class="amg-list-searchbar__input searchbox plain-search" placeholder="{{ trans('config.allocation_type.search') }}...">
                                </div>
                            </div>

                            <button class="amg-refresh-btn btn-reload-list">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                </svg>
                                <span>{{ trans("config.allocation_type.refresh") }}</span>
                            </button>
                        </div>
                        <div class="table-responsive" style="overflow-y:hidden;">
                            <table id="mytable" class="table display amg-datatable" style="width:100%">
                                <thead>
                                <tr>
                                    <th><h4>{{ trans("config.allocation_type.id") }}</h4></th>
                                    <th><h4>{{ trans("config.allocation_type.name") }}</h4></th>
                                    <th><h4>{{ trans("config.allocation_type.created_at") }}</h4></th>
                                    <th><h4>{{ trans("config.allocation_type.updated_at") }}</h4></th>
                                    <th><h4>{{ trans("config.allocation_type.actions") }}</h4></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include("config.asset_allocation_type.create_modal")
    </section>
</div>
@endsection
@push('scripts')
<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/config/asset_allocation_type/index.js') !!}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var config = {};
        config.url = {};
        config.url.allocationtype_list = "{{ url('jx-allocationtype-list') }}";
        config.url.delete = "{{ url('asset_allocation_type/delete') }}";
        config.url.create = "{{ url('asset_allocation_type/createAllocationtype') }}";
        config.url.allocationtype = "{{ url('asset_allocation_type') }}";
        config.url.edit = "{{ url('asset_allocation_type/edit') }}";
        config.url.update = "{{ url('asset_allocation_type/update') }}";
        config.url.download_url = "{{ url('export-allocation')}}";
        config.datatable_translations =  @json(trans('datatable.datatable'));
        config.translations = {
            create_allocation_type: '{{ trans('config.allocation_type.create_allocation_type') }}',
            delete: '{{ trans('config.allocation_type.delete') }}',
            delete_record: '{{ trans('config.allocation_type.delete_record') }}',
            record_deleted: '{{ trans('config.allocation_type.record_deleted') }}',
            search: '{{ trans('config.allocation_type.search') }}',
            refresh_List: '{{ trans('config.allocation_type.refresh_List') }}',
            press_enter_with_search: '{{ trans('config.allocation_type.press_enter_with_search') }}',
            edit_allocation_type: '{{ trans('config.allocation_type.edit_allocation_type') }}',
            update_allocation_types: '{{ trans('config.allocation_type.update_allocation_types') }}',
            allocation_type_update: '{{ trans('config.allocation_type.allocation_type_update') }}',
            allocation_type_create: '{{ trans('config.allocation_type.allocation_type_create') }}',
            Download: '{{ trans('config.allocation_type.download') }}',
            error: '{{trans('config.allocation_type.error')}}',
            create: '{{trans('config.allocation_type.create')}}',
            edit: '{{ trans('config.allocation_type.edit') }}',
            please_enter_valid_value_for_search: '{{ trans('config.allocation_type.please_enter_valid_value_for_search') }}',
            please_wait: '{{ trans('config.allocation_type.please_wait') }}',
            failed_to_load_data: '{{ trans('config.allocation_type.failed_to_load_data') }}',
            yes_delete: '{{ trans('config.allocation_type.yes_delete') }}',
            delete_failed: '{{ trans('config.allocation_type.delete_failed') }}',
            required: '{{ trans('config.allocation_type.required') }}',
            clean_text_only: '{{ trans('config.allocation_type.clean_text_only') }}',
            maxlength: '{{ trans('config.allocation_type.maxlength') }}',
            letters_numbers_allowed: '{{ trans('config.allocation_type.letters_numbers_allowed') }}',
        };
        config.permissions = {!! json_encode($permissionArray) !!};
        config.token = "{{ csrf_token() }}";
        new AllocationType(config);
    });
</script>
@endpush
