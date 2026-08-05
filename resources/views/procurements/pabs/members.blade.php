{{-- @page-meta
{
  "page_no": "",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "AI Migration",
      "from": "2026-07",
      "reviewer": null,
      "description": "Migrated from Laravel 8 / Bootstrap 3 legacy po_company module"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans("content.procurement_fields.PAB_Members"))

@section('content')
<div id="main-pab-members-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans("content.procurement_fields.PAB_Members") }}</h3>
        <div class="d-flex gap-8">
            @if(!in_array($pab->hierarchy_approval, [4,5,6,9]))
            <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-member" type="button"
                    data-bs-toggle="tooltip" title="{{ trans("content.procurement_fields.AddNew_Member") }}">
                <i class="bi bi-plus-lg"></i>
                <span>{{ trans("content.procurement_fields.AddNew_Member") }}</span>
            </button>
            @endif
            @if(in_array($pab->hierarchy_approval, [9]))
            <button class="amg-btn amg-btn-primary amg-btn-sm btn-add-budget-range" type="button"
                    data-bs-toggle="tooltip" title="{{ trans("content.procurement_fields.AddNew_Budget_Member") }}">
                <i class="bi bi-plus-lg"></i>
                <span>{{ trans("content.procurement_fields.AddNew_Budget_Member") }}</span>
            </button>
            @endif
            <button class="header-icon-btn header-icon-btn-sm btn-go-list" type="button"
                    data-bs-toggle="tooltip" title="{{ trans("content.service_ticket_fields.Back") }}">
                <i class="bi bi-arrow-left"></i>
                <span class="b6-text opacity-50">{{ trans("content.service_ticket_fields.Back") }}</span>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="row mb-3">
                        <div class="col-md-3 text-bold" style="text-align: left">{{ trans("content.procurement_fields.Name") }}</div>
                        <div class="col-sm-9">{{ $pab->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-bold" style="text-align: left">{{ trans("content.procurement_fields.Description") }}</div>
                        <div class="col-sm-9">{{ strip_tags(str_replace('&nbsp;', ' ', $pab->description))  }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-bold" style="text-align: left">{{ trans("content.procurement_fields.Approval_Mode") }}</div>
                        <div class="col-sm-9">{{ $pab->getApprovalModeName() }}</div>
                    </div>
                    @if($pab->shouldShowRequiredApprovals())
                    <div class="row mb-3">
                        <div class="col-md-2 text-bold">{{ trans("content.procurement_fields.Required_Minimum_Approvals") }}</div>
                        <div class="col-sm-9">{{ $pab->required_minimum_approvals }}</div>
                    </div>
                    @endif
                    <hr/>
                    <div class="members-list @if($show_form) hide @endif">
                        @if($pab->totMembers() && in_array($pab->hierarchy_approval,[1,2,3]))
                        <div class="table-responsive">
                            <table id="memberTable" class="table display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Action") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Member_Name") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Email") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Username") }}</h4></th>
                                        @if($pab->hierarchy_approval == 1)
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Approval_Level") }}</h4></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        @elseif($pab->getApprovalModeName() == "Location Approval")
                        
                        <div class="form-group locationApproverDiv cover ">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card rounded-0">
                                                <div class="card-header">
                                                    <h5 class="card-title">{{ trans("content.service_ticket_fields.Location") }}</h5>
                                                </div>
                                                <div class="card-body table-responsive">
                                                    <table id="locationApproverTable" class="table display" style="width:100%">
                                                        <thead>
                                                        <th><h4 class="b2-text">{{ trans("content.service_ticket_fields.Location") }}</h4></th>
                                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.location_head") }}</h4></th>
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
                        @elseif(in_array($pab->hierarchy_approval,[9]))
                        <div class="table-responsive">
                            <table id="new_budget_range" class="table display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Action") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Budget_from") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Budget_to") }}</h4></th>
                                        <th><h4 class="b2-text">{{ trans("content.procurement_fields.Member_Name") }}</h4></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        @else
                        <div class="well text-center">{{ trans("content.procurement_fields.no_PAB") }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Advance filter modal — FIXED id per project convention, do not rename --}}
    <div class="modal fade" id="advanceFilterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans("content.procurement_fields.PAB_Members") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="amg-form-field">
                                <label class="form-label">{{ trans("content.procurement_fields.serach_option") }}</label>
                                <div class="amg-list-searchbar">
                                    <i class="bi bi-search amg-list-searchbar__icon search-icon"></i>
                                    <input type="text" class="amg-list-searchbar__input member-list-search"
                                           placeholder="{{ trans("content.procurement_fields.search") }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-clear-filter" id="btnClrFilter">
                        {{ trans("content.filter_heading.Clear") }}
                    </button>
                    <button type="button" class="amg-btn amg-btn-primary btn-filter">
                        {{ trans("button.filter") }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('procurements.pabs.modal_html_member')
    @include('procurements.pabs.add-budget-range')
</div>
@endsection

@push('css')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
<style>
    .logo-thumb { width: 60px; height: 40px; object-fit: contain; }
    .active-user,
    .inactive-user {
        display: inline-block;
        width: 11px;
        height: 11px;
        margin: 0 0 -1px 0;
        border-radius: 10px;
    }
    .active-user {
        background: #5BD810;
        margin-left: 8px;
    }
    .inactive-user {
        background: #F74C3F;
        margin-left: 8px;
    }
    td.sorting_1 {
        width: 40%;
    }
    @media (min-width: 992px) {
       .col-md-3 {
            text-align: right;
        } 
    }
    td .select2 {
        width: 300px !important;
    }
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/procurements/members.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    config.translations = {
        search_option: '{{ trans('content.procurement_fields.serach_option') }}',
        user_placeholder: '{{ trans('header.escalation_group.user_placeholder') }}',
        location_update: '{{ trans('header.escalation_group.location_update') }}',
        select_user: '{{ trans('content.procurement_fields.select_user') }}',
        edit_budget_range: '{{ trans('content.procurement_fields.edit_budget_range') }}',
        already_taken: '{{ trans('content.procurement_fields.already_taken') }}',
        confirmation_message: '{{ trans('content.procurement_fields.confirmation_message') }}',
        save_changes: '{{ trans('content.procurement_fields.save_changes') }}',
        btn_update: '{{ trans('button.update') }}',
        btn_add: '{{ trans('button.add') }}',
        edit_member: '{{ trans('content.user_group.edit_member') }}',
        add_member: '{{ trans('content.procurement_fields.New_Member') }}',
        Search: '{{ trans('content.procurement_fields.search') }}',
        Refresh_List: '{{ trans('content.procurement_fields.Refresh_List') }}',
    };
    config.pab = {!! json_encode($pab) !!};
    config.url.get_locations = "{{ url('getLocationByQuery') }}";
    config.url.get_departments = "{{ route('getDepartment') }}";
    config.url.getUserByAjax = "{{  url('getUserByQuery') }}";
    config.url.actionUpdate = "{{ url('procurements/pab/updateLocation') }}";
    config.url.getBudgetRange = "{{ url('procurements/pab/getBudgetRange') }}";
    config.url.addBudgetRange = "{{ url('procurements/pab/addBudgetRange') }}";
    config.url.editBudgetRange = "{{ url('procurements/pab/editBudgetRange') }}";
    config.url.fetchBudgetRange = "{{ url('procurements/pab/fetchBudgetRange') }}";
    config.url.updateBudgetRange = "{{ url('procurements/pab/updateBudgetRange') }}";
    config.url.checkBudgetExists = "{{ url('procurements/pab/checkBudgetExists') }}";
    config.url.deleteBudgetExists = "{{ url('procurements/pab/deleteBudgetExists') }}";
    config.url.ajaxMemberList = "{{ url('procurements/pab/getmemberList') }}";
    config.url.addMember = "{{ url('procurements/pab/members') }}";
    config.url.edit_member = "{{ url('procurements/pab/members/edit') }}";
    config.url.update_member = "{{ url('procurements/pab/members/update') }}";
    config.url.delete_member = "{{ url('procurements/pab/members/remove') }}";
    config.url.updateLevelByDrag = "{{ url('procurements/pab/updateLevelByDrag') }}";
    config.token = "{{ csrf_token() }}";
    config.pabId = "{{ $pab->id }}";
    config.hierarchy_approval = "{{ $pab->hierarchy_approval }}";
    new ProcurementPABMembers(config);
    new ProcurementBudgetRange(config);
    new ProcurementPABLocation(config);
</script>
@endpush
