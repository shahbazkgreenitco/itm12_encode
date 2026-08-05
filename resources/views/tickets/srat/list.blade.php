@extends('layouts.layout1')
@section('title', trans('sidebar.ticket_menu.ticket_srat'))
@section('content')
    <div id="srat-group-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{trans("content.ticket_procurement.ticket_procurement_breadcrumb")}}</h3>
            <div class="d-flex gap-8">
                <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip"
                    title="{{ trans('content.ticket_procurement.Filter') }}">
                    <svg viewBox="0 0 20 18" fill="none">
                        <path
                            d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                            fill="currentColor" />
                    </svg>
                    <span class="">{{ trans('content.ticket_procurement.Filter') }}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>
                <button class="header-icon-btn header-icon-btn-sm btn-export-srat btn-export" data-toggle="tooltip" data-bs-toggle="tooltip" title="{{ trans('content.ticket_procurement.Download') }}"><i class="bi bi-download"></i></button>
                <a href="{{ url('tickets/srat/add') }}" role="button" class="amg-btn amg-btn-primary amg-btn-sm btn-add-group" data-bs-toggle="tooltip"  title="{{ trans('content.ticket_procurement.Add_New_PAB') }}">
                    <i class="bi bi-plus-lg"></i><span>{{ trans('content.ticket_procurement.Add_New_PAB') }}</span>
                </a>
            </div>

        </div>
        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="col-auto">
                                <select class="amg-table-pagination-dropdown amgTablePageLenth srat-group-list-page-length">
                                    <option value="10" selected>{{ trans('content.ticket_procurement.show') }} (10)</option>
                                    <option value="25">{{ trans('content.ticket_procurement.show') }} (25)</option>
                                    <option value="50">{{ trans('content.ticket_procurement.show') }} (50)</option>
                                    <option value="100">{{ trans('content.ticket_procurement.show') }} (100)</option>
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
                                    <input type="text" class="amg-list-searchbar__input user-list-search" placeholder="{{ trans('content.ticket_procurement.search') }}">
                                </div>
                            </div>

                            <button class="amg-refresh-btn btn-reload-list" title="{{ trans('content.ticket_incident.Refresh_List') }}">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                        fill="currentColor"></path>
                                </svg>
                                <span>{{ trans('content.ticket_incident.Refresh_List') }}</span>
                            </button>
                        </div>

                        <div class="">
                            <div class="table-responsive">
                                <table id="mytable" class="mytable amg-datatable table display app-data-table">
                                    <thead>
                                        <tr>
                                            <th><h4 class="b2-text">{{ trans("content.ticket_procurement.PAB_Name") }}</h4></th>
                                            <th><h4 class="b2-text">{{ trans("content.ticket_procurement.company") }}</h4></th>
                                            <th style="width:15%"><h4 class="b2-text">{{ trans("content.ticket_procurement.Approval_Mode") }}</h4></th>
                                            <th><h4 class="b2-text">{{ trans("content.ticket_procurement.Description") }}</h4></th>
                                            <th style="width:12%"><h4 class="b2-text">{{ trans("content.ticket_procurement.Total_Members") }}</h4></th>
                                            <th><h4 class="b2-text">{{ trans("content.ticket_procurement.created_at") }}</h4></th>
                                            <th><h4 class="b2-text">{{ trans("content.ticket_procurement.updated_at") }}</h4></th>
                                            <th style="text-align: center !important"><h4 class="b2-text">{{ trans("content.ticket_procurement.Action") }}</h4></th>
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
        @include('tickets/modal_description')
        @include('tickets.srat.srat-filter')        
    </div>
@endsection
@push('scripts')
    <script src="{!! CommonHelper::asset('js/tickets/srat/pab.js') !!}"></script>
    <script type="text/javascript">
        @if(Session::has('msg'))
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: "3000"
            };
            @if(Session::get('msg')['status'] == 'success')
                toastr.success("{{ Session::get('msg')['msg'] }}");
            @elseif(Session::get('msg')['status'] == 'danger')
                toastr.error("{{ Session::get('msg')['msg'] }}");
            @elseif(Session::get('msg')['status'] == 'warning')
                toastr.warning("{{ Session::get('msg')['msg'] }}");
            @else
                toastr.info("{{ Session::get('msg')['msg'] }}");
            @endif
            {{ Session::forget('msg') }}
        @endif
        var config = new Object;
        config.url = new Object;
        config.url.getlist = "{{ url('tickets/srat/ajax-list') }}";
        config.url.edit = "{{ url('tickets/srat/edit') }}";
        config.url.delete = "{{ url('tickets/srat/delete') }}";
        config.url.history = "{{ url('tickets/srat/srat_history') }}";
        config.url.manage_users = "{{ url('tickets/srat/members') }}";
        config.url.export = "{{ url('tickets/srat/export') }}";
        config.url.getCompanyByUserAccess = "{{ url('getCompanyByUserAccess') }}";
        config.url.getActiveUsersByQuery = "{{ url('getUserByQuery') }}";
        config.company_user_detail = {!! json_encode($userDatail) !!};
        config.translations = {
            serach_option: '{{ trans('content.procurement_fields.serach_option') }}',
            filter: '{{ trans('content.user_fields.Filter') }}',
            Select_Department: '{{ trans('content.procurement_fields.Select_Department') }}',
            press_enter_with_Search: '{{ trans('content.procurement_fields.press_enter_with_Search') }}',
            Search: '{{ trans('content.service_ticket_fields.search') }}',
            placeholder: '{{ trans('content.ticket_incident.search_option') }}',
            Refresh_List: '{{ trans('content.procurement_fields.Refresh_List') }}',
            Download_Excel: '{{ trans('content.service_ticket_fields.download_excel') }}',
            Edit_SRAT: '{{ trans('content.ticket_procurement.srat_edit') }}',
            History: '{{ trans('content.procurement_fields.View_History') }}',
            ADD_SRAT_MEMBER: '{{ trans('content.ticket_procurement.srat_add_members') }}',
            DELETE_SRAT: '{{ trans('content.procurement_fields.srat_delete') }}',
            Member_List: '{{ trans('content.procurement_fields.Member_List') }}',
            Delete_PAB: '{{ trans('content.procurement_fields.Delete_PAB') }}',
            HISTORY_SRAT: '{{ trans('content.ticket_procurement.srat_history') }}',
            Add_Financial_Year: '{{ trans('content.procurement_fields.Add_Financial_Year') }}',
            are_you_delete: '{{ trans('content.procurement_fields.are_you_delete') }}',
            save_financial: '{{ trans('button.save_financial') }}',
            save_changes: '{{ trans('button.save_changes') }}',
            Add_New_PAB: '{{ trans('content.ticket_procurement.Add_New_PAB') }}',
            Delete: '{{ trans('content.service_ticket_fields.Delete') }}',
            are_you_delete_pab: '{{ trans('content.service_ticket_fields.are_you_delete_pab') }}',
            something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
            please_enter_valid_search: '{{ trans('content.user_fields.please_enter_valid_search') }}',
        };
        config.token = "{{ csrf_token() }}";
    </script>
@endpush
