{{-- @page-meta
{
  "page_no": "KNB-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-06",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('kanban.all_kanban_board.all_kanban_board'))
@section('content')
<div id="all-kanban-member-wrapper">
    {{-- Header Actions --}}
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">
            {{ trans("ticket.service_ticket_fields.kabnan_board_member") }} - {{ $board->name ?? "" }}
        </h3>
        <div class="d-flex gap-8">

            @if(Auth::user()->hasPermissionTo('KanbanMemberViewHistory'))
                <button class="header-icon-btn header-icon-btn-sm btn-open-filter view-member-history" type="button" data-bs-toggle="tooltip"
                    title="History">
                <i class="bi bi-clock-history"></i>
                </button>
            @endif


            @if(Auth::user()->hasPermissionTo('KanbanBoardAddMembers'))
                <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-record add-kanban-member" type="button" data-bs-toggle="tooltip"
                    title="{{ trans('ticket.service_ticket_fields.add_kanban_member') }}">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" />
                    </svg>
                    <span>{{ trans('ticket.service_ticket_fields.add_kanban_member') }}</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Main Card --}}
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">
                    {{-- Toolbar --}}
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown amgTablePageLenth">
                                <option value="10" selected>{{ trans('kanban.all_kanban_board.show10') }}</option>
                                <option value="25">{{ trans('kanban.all_kanban_board.show25') }}</option>
                                <option value="50">{{ trans('kanban.all_kanban_board.show50') }}</option>
                                <option value="100">{{ trans('kanban.all_kanban_board.show100') }}</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon btn-searchbox" width="18" height="18" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor" />
                                </svg>
                                <input type="text" class="amg-list-searchbar__input searchbox plain-search search_box"
                                    placeholder="{{ trans('ticket.service_ticket_fields.search') }}">
                            </div>
                        </div>

                        <button class="amg-refresh-btn btn-reload" title="{{ trans('ticket.service_ticket_fields.Refresh_List') }}">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F" />
                            </svg>
                            <span>{{ trans('ticket.service_ticket_fields.Refresh_List') }}</span>
                        </button>
                    </div>

                    {{-- Table --}}
                    <div class="list-view-panel">
                        <div class="table-responsive">
                            <table id="mytable" class="mytable amg-datatable table app-data-table">
                                <thead>
                                    <tr>
                                        <th><h4 class="b2-text">{{ trans("ticket.service_ticket_fields.name") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("ticket.service_ticket_fields.access_type") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("ticket.service_ticket_fields.Created_At") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("ticket.service_ticket_fields.updated_at") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("ticket.service_ticket_fields.actions") }}</h4></th>
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
    {{-- Modals --}}
    @include("tickets.kanban-board.add-member.modal_kanban_member")
    @include("tickets.kanban-board.add-member.history")
</div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/kanban-board/member.js') !!}"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        var config = {};
        config.url = {};
        config.url.KanbanBoardMemberList = "{{ url('tickets/js-kanban-member') }}";
        config.url.departments_with_company = "{{ url('tickets/departments') }}";
        config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
        config.url.store_member = "{{ url('tickets/js-kanban-member-store') }}";
        config.url.edit_kanabanMember = "{{ url('tickets/js-kanban-edit-member') }}";
        config.url.update_kanbanMember = "{{ url('tickets/js-kanban-update-member') }}";
        config.url.delete_kanbanMember = "{{ url('tickets/js-kanban-delete-member') }}";
        config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
        config.url.getBoardMemberHistory= "{{ url('tickets/kanban-board/get-board-member-history') }}";
        config.url.user_info = "{{ url('user/info') }}";
        config.permissions = {!! json_encode($permissionArray) !!};
        config.companyIds = {!! json_encode($company_id) !!};
        config.isNormalUser = @json(Auth::user()->isNormalUser());
        config.user = {!! json_encode(Auth::user()->only("id", "first_name", "last_name", "username", "company_id")) !!};
        config.translations = {
            something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
            serach_option: '{{ trans('ticket.service_ticket_fields.serach_option') }}',
            search: '{{ trans('ticket.service_ticket_fields.search') }}',
            add_new: '{{ trans('ticket.service_ticket_fields.add_kanban_member') }}',
            Refresh_List: '{{ trans('ticket.service_ticket_fields.Refresh_List') }}',
            edit: '{{ trans('ticket.service_ticket_fields.edit_kanban_member') }}',
            delete: '{{ trans('ticket.service_ticket_fields.delete_kanban_member') }}',
            add_kanban_member: '{{ trans('ticket.service_ticket_fields.add_Kanban_member') }}',
            are_you_delete: '{{ trans('ticket.service_ticket_fields.are_you_delete') }}',
            select_user: '{{ trans('ticket.service_ticket_fields.select_user') }}',
        };

        config.token = "{{ csrf_token() }}";
        config.board = {!! json_encode($board) !!};
        new KanbanMember(config);        
    });
</script>
@endpush
