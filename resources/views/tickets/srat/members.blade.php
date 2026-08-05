@extends('layouts.layout1')
@section('title', trans("content.ticket_procurement.PAB_Members"))
@section('content')
<div id="srat-members-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4 mb-3">
        <button type="button" class="bg-transparent border-0 d-flex align-items-center gap-2"
            onclick="window.location.href='{{ url('tickets/srat/list') }}'">
            <svg width="20" height="20" viewBox="0 0 25 21" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                    fill="currentColor"></path>
            </svg>
            <h3 class="h3-text mb-0">{{ trans("content.ticket_procurement.PAB_Members") }} - {{ $pab->name }}</h3>
        </button>
        <div class="d-flex gap-8">
            <button class="amg-btn amg-btn-outline amg-btn-sm go-history">
                <i class="bi bi-clock-history"></i>
                <span>{{ trans("content.tab_header.history") }}</span>
            </button>

            @if(!in_array($pab->hierarchy_approval, [4,5,6,12]))
            <button class="amg-btn amg-btn-primary amg-btn-sm go-add">
                <i class="fas fa-user-plus"></i>
                <span>{{ trans("content.ticket_procurement.AddNew_Member") }}</span>
            </button>
            @endif
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-3 fw-semibold">
                            {{ trans("content.ticket_procurement.Name") }}
                        </div>
                        <div class="col-md-9">
                            {{ $pab->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-semibold">
                            {{ trans("content.ticket_procurement.Description") }}
                        </div>
                        <div class="col-md-9">
                            @if(strlen($pab->description) > 50)
                                <span>
                                    {{ \Illuminate\Support\Str::limit($pab->description, 50, '...') }}
                                </span>

                                <a href="javascript:void(0)"
                                class="read-more"
                                data-full-text="{{ e($pab->description) }}">
                                    Read More
                                </a>
                            @else
                                {{ $pab->description }}
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-semibold">
                            {{ trans("content.ticket_procurement.Approval_Mode") }}
                        </div>
                        <div class="col-md-9">
                            {{ $pab->getApprovalModeName() }}
                        </div>
                    </div>

                    @if($pab->shouldShowRequiredApprovals())
                    <div class="row mb-4">
                        <div class="col-md-3 fw-semibold">
                            {{ trans("content.ticket_procurement.Required_Minimum_Approvals") }}
                        </div>
                        <div class="col-md-9">
                            {{ $pab->required_minimum_approvals }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-3 fw-semibold">
                            {{ trans("content.ticket_procurement.company") }}
                        </div>
                        <div class="col-md-9">
                            {{ optional($pab->company)->name }}
                        </div>
                    </div>

                    @if(in_array($pab->hierarchy_approval, [4,5,6]))
                        <div class="form-group locationApproverDiv cover hide">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card rounded-0">
                                                <div class="card-header">
                                                    <h3 class="h5 mb-0">
                                                        {{ trans("content.service_ticket_fields.Location") }}
                                                    </h3>
                                                </div>
                                                <div class="card-body table-responsive">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <div class="col-auto">
                                                            <select class="amg-table-pagination-dropdown amgTablePageLenth srat-group-list-page-length">
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
                                                                <input type="text" class="amg-list-searchbar__input user-list-search" placeholder="Search...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table id="locationApproverTable" class="mytable amg-datatable table display app-data-table">
                                                        <thead>
                                                        <th>{{ trans("content.service_ticket_fields.Location") }}</th>
                                                        <th>Location Head</th>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="form-group departmentApproverDiv cover hide">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card rounded-0">
                                                <div class="card-header">
                                                    <h3 class="h5 mb-0">
                                                        {{ trans("content.service_ticket_fields.Departments") }}
                                                    </h3>
                                                </div>
                                                <div class="card-body table-responsive">
                                                    <table id="departmentApproverTable" class="table align-middle">
                                                        <thead>
                                                        <th>{{ trans("content.service_ticket_fields.Departments") }}</th>
                                                        <th>Concerned Person</th>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!in_array($pab->hierarchy_approval, [4,5,6,12]))

                    <div class="add-member {{ !$show_form ? 'd-none' : '' }}">
                        <form name="member" method="POST"
                            action="{{ url('tickets/srat/members', ['id' => $pab->id, 'company_id' => $pab->company_id]) }}">

                            @csrf

                            <div class="row">
                                <div class="col-lg-7">

                                    <h5 class="mb-3">
                                        {{ trans("content.ticket_procurement.New_Member") }}
                                    </h5>

                                    <div class="mb-3 row">
                                        <label class="col-md-3 col-form-label fw-semibold mandatory">
                                            {{ trans("content.ticket_procurement.User") }}
                                        </label>

                                        <div class="col-md-9">
                                            <select name="user" id="user" class="form-control">
                                                <option value="">Select User</option>
                                            </select>

                                            @if(isset($errors) && $errors->get('user'))
                                            <label class="error">
                                                {{ $errors->get('user')[0] }}
                                            </label>
                                            @endif
                                        </div>
                                    </div>

                                    @if($pab->hierarchy_approval == 1)

                                    @php
                                        $approvalLebal=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
                                        $remove=[];
                                        foreach ($ticketPabMembers as $key => $mem) {
                                            array_push($remove,$mem->hierarchy_level);
                                        }
                                        $label=array_diff($approvalLebal,$remove);
                                    @endphp

                                    <div class="mb-3 row">
                                        <label class="col-md-3 col-form-label fw-semibold">
                                            {{ trans("content.ticket_procurement.Approval_Level") }}
                                        </label>

                                        <div class="col-md-9">
                                            <select name="hierarchy_level" id="hierarchy_level" class="form-control">
                                                @foreach($label as $lab)
                                                <option value="{{ $lab }}">{{ $lab }}</option>
                                                @endforeach
                                            </select>

                                            @if(isset($errors) && $errors->get('hierarchy_level'))
                                            <label class="error">
                                                {{ $errors->get('hierarchy_level')[0] }}
                                            </label>
                                            @endif
                                        </div>
                                    </div>

                                    @endif

                                    <div class="row">
                                        <div class="col-md-9 offset-md-3 d-flex gap-2">
                                            <button class="amg-btn amg-btn-primary amg-btn-md mb-2" type="submit" id="btnSubmit">
                                                {{ trans("button.add") }}
                                            </button>

                                            <button class="amg-btn amg-btn-secondary amg-btn-md mb-2 ms-2" type="button" id="show-members">
                                                {{ trans("button.cancel") }}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="members-list {{ $show_form ? 'd-none' : '' }}">
                        @if($pab->totMembers())
                        <div class="table-responsive">
                            <table class="table align-middle dataTable" id="members-list">
                                <thead>
                                    <tr>
                                        <th><h4>{{ trans("content.ticket_procurement.Member_Name") }}</h4></th>
                                        <th><h4>{{ trans("content.ticket_procurement.Email") }}</h4></th>
                                        <th><h4>{{ trans("content.ticket_procurement.Username") }}</h4></th>

                                        @if($pab->hierarchy_approval == 1)
                                        <th><h4>{{ trans("content.ticket_procurement.Approval_Level") }}</h4></th>
                                        @endif
                                        <th><h4>{{ trans("content.ticket_procurement.Action") }}</h4></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pab->members as $mem)
                                    <tr>
                                        <td>{{ $mem->user->fullName() }}</td>
                                        <td>{{ $mem->user->email }}</td>
                                        <td>{{ $mem->user->username }}</td>
                                        @if($pab->hierarchy_approval == 1)
                                        <td>
                                            @if($pab->shouldShowLevel())
                                            {{ $mem->hierarchy_level }}
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            <div class="amg-datatable d-flex gap-2">
                                                <a href="{{ url('tickets/srat/members/remove', ['id'=>$pab->id, 'company_id'=>$pab->company_id, 'mbr'=>$mem->id]) }}"
                                                    class="amg-action-btn" data-bs-toggle='tooltip'
                                                    title="{{ trans('content.ticket_procurement.Edit_Member') }}">

                                                    <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ url('tickets/srat/members/edit', ['id'=>$pab->id, 'company_id'=>$pab->company_id, 'mbr'=>$mem->id]) }}"
                                                    class="amg-action-btn" data-bs-toggle='tooltip'
                                                    title="{{ trans('content.ticket_procurement.delete_member') }}">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"
                                                        fill="currentColor"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-light text-center mb-0">
                            {{ trans("content.ticket_procurement.No_Member_Found") }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
    @include('tickets/modal_description')
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .alert {
        color: var(--app-text);
    }
    [data-bs-theme=dark] .table thead th,[data-bs-theme=dark] .table thead th.sorting,[data-bs-theme=dark] .table thead th.sorting_asc,[data-bs-theme=dark] .table thead th.sorting_desc {
        background-color: var(--dark-primary)!important;
         color: var(--app-text) !important;
    }
    label.control-label {
        width: 60%;
    }
</style>
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/tickets/srat/members.js') !!}"></script>

<script>
$(function () {
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
    
    $("#btnSubmit").click(function () {
        var user = $("#user").val();
        if (user == "") {
            var data = {
                msg: 'Please select user!'
            };
            sweetAlert("center", 'error', data);
            return false;
        }
        return true;
    });

    $(".go-history").on("click", function () {
        window.location = "{{ url('tickets/pab/pab_member_history', ['id' => $pab->id]) }}";
    });

    $(".go-add").on("click", function () {
        $(".members-list").addClass("d-none");
        $(".add-member").removeClass("d-none");
    });

    $("#show-members").on("click", function () {
        $(".add-member").addClass("d-none");
        $(".members-list").removeClass("d-none");
    });

    var config = new Object;
    config.url = new Object;
    config.url.get_locations = "{{ url('getLocationByQuery') }}";
    config.url.get_departments = "{{ route('getDepartment') }}";
    config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
    config.url.actionUpdate = "{{  url('tickets/srat/members/actionUpdate') }}";
    config.url.userList = "{{ url('tickets/srat/list') }}";
    config.url.history = "{{ url('tickets/srat/srat_member_history') }}";

    config.pab = {!! json_encode($pab) !!};
    config.ticketPabMembers = {!! json_encode($ticketPabMembers) !!};
    config.company_id = {!! json_encode($company_id) !!};
    config.token = "{{ csrf_token() }}";

    new TicketPABMembers(config);
});
</script>
@endpush