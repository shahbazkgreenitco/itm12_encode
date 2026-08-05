@extends('layouts.layout1')

@section('title', trans('content.ticket_procurement.Add_New_PAB'))

@section('content')
<div id="srat-group-list-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <button type="button" class="bg-transparent border-0 d-flex align-items-center gap-2"
            onclick="window.location.href='{{ url('tickets/srat/list') }}'">
            <svg width="20" height="20" viewBox="0 0 25 21" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                    fill="currentColor"></path>
            </svg>
            <h3 class="h3-text mb-0">{{ trans('content.ticket_procurement.Add_New_PAB') }}</h3>
        </button>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <!-- Top Actions -->
                    <div class="d-flex justify-content-end mb-4">
                    </div>
                    <!-- Form -->
                    <form name="pab" method="POST" id="addForm" action="{{ url('tickets/srat/add') }}">
                        @csrf
                        <!-- Company -->
                        <div class="row mb-4 align-items-center">
                            <label class="col-md-3 col-form-label text-md-end fw-semibold mandatory">
                                {{ trans('content.ticket_procurement.company') }}
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-building"></i>
                                    </span>
                                    <select name="company_id" id="company_id" class="form-select select2">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="row mb-4 align-items-center">
                            <label for="name" class="col-md-3 col-form-label text-md-end fw-semibold mandatory">
                                {{ trans("content.ticket_procurement.Name") }}
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="{{ trans("content.ticket_procurement.Name_PH") }}" value="{{ request()->name ?? '' }}">
                                </div>

                                @if(isset($errors) && $errors->get('name'))
                                    <div class="text-danger small mt-1">
                                        {{ $errors->get('name')[0] }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-4">
                            <label for="description" class="col-md-3 col-form-label text-md-end fw-semibold mandatory">
                                {{ trans("content.ticket_procurement.Description") }}
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text align-items-start pt-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                                            <path d="M1.5 3a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 0 1H2a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h12a.5.5 0 0 1 0 1H2a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1H2a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H2a.5.5 0 0 1-.5-.5z"/>
                                        </svg>
                                    </span>
                                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="{{ trans("content.ticket_procurement.Description_PH") }}">{{ request()->description ?? '' }}</textarea>
                                </div>

                                @if(isset($errors) && $errors->get('description'))
                                    <div class="text-danger small mt-1">
                                        {{ $errors->get('description')[0] }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Approval Mode -->
                        <div class="row mb-4 align-items-center">
                            <label for="hierarchy_approval" class="col-md-3 col-form-label text-md-end fw-semibold mandatory">
                                {{ trans("content.ticket_procurement.Approval_Mode") }}
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <!-- Standard List/Menu Icon for Dropdowns -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                                        </svg>
                                    </span>
                                    <select id="hierarchy_approval" name="hierarchy_approval" class="form-select">
                                        <option value="3">Group Approval</option>
                                        <option value="1">Level By Level</option>
                                        <option value="2">Minimum Approval</option>
                                        <option value="4">Manager Approval</option>
                                        <option value="5">Location Approval</option>
                                        <option value="8">System Approval</option>

                                        @if(in_array(config('app.client'), ['ltts', 'rolepermission', 'grdemo']))
                                            <option value="9">DU Head Approval</option>
                                            <option value="10">BU Head Approval</option>
                                            <option value="13">BU/DU Head Approval</option>
                                            <option value="14">DU/BU Head Approval</option>
                                        @endif

                                        <option value="11">Department Head Approval</option>
                                        <option value="12">Next Level Manager Approval</option>
                                    </select>
                                </div>

                                @if(isset($errors) && $errors->get('hierarchy_approval'))
                                    <div class="text-danger small mt-1">
                                        {{ $errors->get('hierarchy_approval')[0] }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Minimum Approval -->
                        <div class="row mb-4 cover d-none">

                            <label id="minimum_approval_required"
                                   for="required_minimum_approvals"
                                   class="col-md-3 col-form-label text-md-end fw-semibold mandatory d-none">
                                {{ trans("content.ticket_procurement.Required_Minimum_Approvals") }}
                            </label>

                            <label id="required_manager_level"
                                   for="required_minimum_approvals"
                                   class="col-md-3 col-form-label text-md-end fw-semibold mandatory d-none">
                                {{ trans("ticket.service_ticket_fields.required_manager_level") }}
                            </label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8.39 1.995a.5.5 0 0 1 .22.67L7.216 6h5.284a.5.5 0 0 1 .484.62l-.5 2A.5.5 0 0 1 12 9H6.61l-1.396 5.32a.5.5 0 0 1-.966-.254L5.616 9H.5a.5.5 0 0 1-.484-.62l.5-2A.5.5 0 0 1 1 6h5.39l1.396-5.32a.5.5 0 0 1 .604-.365z"/>
                                        </svg>
                                    </span>
                                    <input type="number" name="required_minimum_approvals" id="required_minimum_approvals" min="1" class="form-control" value="{{ request()->required_minimum_approvals ?? 1 }}">
                                </div>

                                @if(isset($errors) && $errors->get('required_minimum_approvals'))
                                    <div class="text-danger small mt-1">
                                        {{ $errors->get('required_minimum_approvals')[0] }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="offset-md-3 col-md-9 d-flex gap-2">
                                <button type="submit" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-md mb-2">
                                    {{ trans("button.add") }}
                                </button>
                                <button type="button" id="cancel" onclick="window.location.href='{{ url('tickets/srat/list') }}'" class="amg-btn amg-btn-secondary amg-btn-md mb-2 ms-2">
                                    {{ trans("button.cancel") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single .select2-selection__arrow b{
        border: 4px solid #0000;
        border-top: 5px solid #888;
        border-bottom-width: 0;
        width: 0;
        height: 0;
        margin-top: -2px;
        margin-left: -4px;
        position: absolute;
        top: 50%;
        left: 50%;
        display: none; 
    }
    
    /* --- FIX FOR SELECT2 INSIDE INPUT-GROUP --- */
    /* Ensure Select2 stretches properly and has correct border radius */
    .input-group .select2-container {
        flex: 1 1 auto;
        width: 1% !important;
        min-width: 0;
    }
    
    .input-group .select2-container--default .select2-selection--single {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        height: 100% !important;
    }

    /* Fix Select2 right-side dropdown arrow alignment */
    .input-group .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 50% !important;
        transform: translateY(-50%);
        margin-top: 0 !important;
    }
    
    label.error {
        font-size: .875em;
        font-weight: normal;
        color: #ff6692;
        text-align: left;
        width: 100%;
    }
    
    [data-bs-theme=dark] .table thead th,[data-bs-theme=dark] .table thead th.sorting,[data-bs-theme=dark] .table thead th.sorting_asc,[data-bs-theme=dark] .table thead th.sorting_desc {
        background-color: var(--dark-primary)!important;
        color: var(--app-text) !important;
    }
</style>
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/tickets/srat/index.js') !!}"></script>
<script>
$(document).ready(function () {
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
    var config = {};
    config.url = {};
    config.token = "{{ csrf_token() }}";
    config.company_defulte = {!! json_encode($company_id) !!};
    config.company_user_detail = {!! json_encode($userDatail) !!};
    config.url.get_company_by_user_access ="{{ url('getCompanyByUserAccess') }}";
    config.translations = {
        manager_approval: '{{ trans('content.ticket_procurement.Required_Minimum_Approvals_PH') }}',
        next_level_approval: '{{ trans('content.ticket_procurement.manager_level') }}'
    }
    new TicketPAB(config);

});
</script>

@endpush