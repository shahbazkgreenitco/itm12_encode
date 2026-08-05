@extends('layouts.layout1')
@section('title', trans("content.ticket_procurement.PAB_Members"))
@section('content')
<div id="srat-members-wrapper">

    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4 mb-3">
        <h3 class="h3-text mb-0">
            {{ trans("content.ticket_procurement.PAB_Members") }} - {{ $pab->name }}
        </h3>

        <div class="d-flex gap-8">
            <button class="amg-btn amg-btn-outline amg-btn-sm go-history">
                <i class="fas fa-info-circle"></i>
                <span>{{ trans("content.tab_header.history") }}</span>
            </button>

            <button class="amg-btn amg-btn-outline amg-btn-sm go-list">
                <i class="fas fa-arrow-left"></i>
                <span>{{ trans("content.ticket_procurement.Back_to_PAB_List") }}</span>
            </button>
        </div>
    </div>

    <main class="main-content">
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
                            {{ $pab->description }}
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
                                                    <table id="locationApproverTable" class="table align-middle">
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

                    <div class="edit-member">
                        <form name="member" method="POST" action="{{ url('tickets/srat/members/updates') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="pab_id" value="{{ $pab->id }}">
                            <input type="hidden" name="id" value="{{ $mem->id }}">
                            <input type="hidden" name="company_id" value="{{ $pab->company_id }}">

                            <div class="row">
                                <div class="col-lg-7">

                                    <h5 class="mb-3">
                                        {{ trans("content.ticket_procurement.Edit_Member") }}
                                    </h5>

                                    <div class="mb-3 row">
                                        <label class="col-md-3 col-form-label fw-semibold mandatory">
                                            {{ trans("content.ticket_procurement.User") }}
                                        </label>

                                        <div class="col-md-9">
                                            <select name="user" id="user" class="form-control">
                                                <option value="{{ $mem->user_id }}">
                                                    {{$mem->user->fullName()}}
                                                </option>
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
                                    foreach ($ticketPabMembers as $key => $mems) {
                                        if($mem->user_id !=$mems->user_id){
                                            array_push($remove,$mems->hierarchy_level);
                                        }
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
                                                <option value="{{ $lab }}" @if($lab==$mem->hierarchy_level) selected @endif>
                                                    {{ $lab }}
                                                </option>
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
                                                {{ trans("button.update") }}
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

                </div>
            </div>
        </div>
    </main>
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
</style>
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/tickets/srat/members.js') !!}"></script>

<script>
    $(".go-history").on("click", function () {
        window.location = "{{ url('tickets/pab/pab_member_history', ['id' => $pab->id]) }}";
    });   
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
    $(document).ready(function() {

        $("#show-members").on("click", function(e) {
            window.location = '{{ url('tickets/srat/members', ['id' => $pab->id,'company_id' =>  $pab->company_id]) }}';
        });
        var config = new Object;
        config.url = new Object;
        config.url.get_locations = "{{ url('getLocationByQuery') }}";
        config.url.get_departments = "{{ route('getDepartment') }}";
        config.url.getUserByAjax = "{{  url('getUserByQuery') }}";
        config.url.actionUpdate = "{{ route('actionUpdate') }}";
        config.url.userList = "{{ url('tickets/srat/list') }}";
        config.pab = {!! json_encode($pab) !!};
        config.ticketPabMembers = {!! json_encode($ticketPabMembers) !!};
        config.company_id = {!! json_encode($company_id) !!};
        config.token = "{{ csrf_token() }}";
        new TicketPABMembers(config);   
    });
</script>
@endpush
