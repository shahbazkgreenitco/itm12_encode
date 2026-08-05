{{-- @page-meta
{
  "page_no": "",
  "file": "pab_members.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "AI Migration",
      "from": "2026-07",
      "reviewer": null,
      "description": "Migrated from Laravel 8 / Bootstrap 3 legacy module"
    }
  ]
}
--}}
@extends('layouts.layout1')

@section('title', trans("content.ticket_procurement.PAB_Members"))

@section('content')
<div id="main-pab-members-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans("content.ticket_procurement.PAB_Members") }}</h3>
        <div class="d-flex gap-8">
            <button type="button" class="header-icon-btn header-icon-btn-sm" data-bs-toggle="tooltip" title="{{ trans("content.ticket_procurement.Back_to_PAB_List") }}">
                <i class="bi bi-arrow-left"></i>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="b6-text text-muted">{{ trans("content.ticket_procurement.Name") }}</span>
                        </div>
                        <div class="col-md-9">
                            <span class="b5-text">{{ $pab->name }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="b6-text text-muted">{{ trans("content.ticket_procurement.Description") }}</span>
                        </div>
                        <div class="col-md-9">
                            <span class="b5-text">{{ $pab->description }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="b6-text text-muted">{{ trans("content.ticket_procurement.Approval_Mode") }}</span>
                        </div>
                        <div class="col-md-9">
                            <span class="b5-text">{{ $pab->getApprovalModeName() }}</span>
                        </div>
                    </div>
                    @if($pab->shouldShowRequiredApprovals())
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="b6-text text-muted">{{ trans("content.ticket_procurement.Required_Minimum_Approvals") }}</span>
                        </div>
                        <div class="col-md-9">
                            <span class="b5-text">{{ $pab->required_minimum_approvals }}</span>
                        </div>
                    </div>
                    @endif

                    @if(in_array($pab->hierarchy_approval, [4,5,6]))
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card rounded-0">
                                <div class="card-header bg-transparent border-bottom-0 py-2">
                                    <h4 class="b2-text mb-0">{{ trans("content.service_ticket_fields.Location") }}</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="locationApproverTable" class="table display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans("content.service_ticket_fields.Location") }}</th>
                                                    <th>Location Head</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card rounded-0">
                                <div class="card-header bg-transparent border-bottom-0 py-2">
                                    <h4 class="b2-text mb-0">{{ trans("content.service_ticket_fields.Departments") }}</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="departmentApproverTable" class="table display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans("content.service_ticket_fields.Departments") }}</th>
                                                    <th>Concerned Person</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form name="member" method="POST" action="{{ url('procurements/pab/members/update', ['id' => $mem->id]) }}">
                                @csrf
                                <input type="hidden" name="pab_id" value="{{ $pab->id }}">
                                <div class="mb-3">
                                    <h4 class="b2-text mb-2">{{ trans("content.ticket_procurement.New_Member") }}</h4>
                                </div>
                                <div class="mb-3">
                                    <div class="amg-form-field">
                                        <label for="user" class="form-label">{{ trans("content.ticket_procurement.User") }} <span class="text-danger">*</span></label>
                                        <select name="user" id="user" class="form-select">
                                            <option value="{{$mem->user_id}}">{{$mem->user->first_name.' '.$mem->user->last_name ." (".$mem->user->username.")"}}</option>
                                        </select>
                                        @error('user') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                @if($pab->hierarchy_approval == 1)
                                <div class="mb-3">
                                    <div class="amg-form-field">
                                        <label for="hierarchy_level" class="form-label">{{ trans("content.ticket_procurement.Approval_Level") }}</label>
                                        <select name="hierarchy_level" id="hierarchy_level" class="form-select">
                                            <?php $approvalLebal=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]; 
                                              $remove=[];
                                            foreach ($pabMembers as $key => $mems) {
                                                if($mem->user_id !=$mems->user_id){
                                                    array_push($remove,$mems->hierarchy_level);
                                                }
                                            }
                                            $label=array_diff($approvalLebal,$remove)  ;                                           
                                            @endphp
                                            <?php foreach($label as $lab) { ?>
                                                <option value="{{ $lab }}" @if($lab==$mem->hierarchy_level){{"selected"}} @endif>{{ $lab }}</option>
                                            <?php } ?>
                                        </select>
                                        @error('hierarchy_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                @endif
                                <div class="mb-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="amg-btn amg-btn-primary amg-btn-sm">
                                            {{ trans("button.update") }}
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="show-members">
                                            {{ trans("button.cancel") }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('css')
<style>
    .active-user, .inactive-user {
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
</style>
@endpush

@push('scripts')
<script src="{!! CommonHelper::asset('js/procurements/pab_members.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    config.url.get_locations = "{{ url('getLocationByQuery') }}";
    config.url.get_departments = "{{ route('getDepartment') }}";
    config.url.getUserByAjax = "{{  url('getUserByQuery') }}";
    config.url.actionUpdate = "{{ url('procurements/pab/members/update') }}";
    config.pab = {!! json_encode($pab) !!};
    config.ticketPabMembers = {!! json_encode($pabMembers) !!};
    config.token = "{{ csrf_token() }}";
    new ProcurementPABMembers(config);
</script>
@endpush
