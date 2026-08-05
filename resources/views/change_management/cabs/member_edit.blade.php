@extends('layouts.layout1')
@section('title', "Edit Cab Member")
@section('content')
<div id="main-user-list-wrapper">
        <section class="content">
            <div id="main-change-cab-list-wrapper" class="change-management-cab-list">
                {{-- Header with back button and title --}}
                <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
                    <div class="d-flex align-items-center gap-1">
                        <button class="go-list d-flex gap-3 align-items-center bg-transparent outline-none border-0" data-bs-toggle="tooltip" title="Back">
                            <svg width="16" height="21" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <h3 class="h3-text mb-0">CAB - {{ $cab->name }}</h3>
                    </div>
                    
                </div>

                <main class="main-content" id="mainContent">
                    {{-- CAB Details Card --}}
                    <div class="container-fluid px-0">
                        <div class="card rounded-0 mb-0">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <p><span class="fw-bold">{{ trans("content.change_management_fields.Name") }} :</span> {{ $cab->name }}</p>
                                    <p><span class="fw-bold">{{ trans("content.change_management_fields.Description") }} :</span> {!! $cab->description !!}</p>
                                    <p><span class="fw-bold">{{ trans("content.change_management_fields.Level_Based_Approval") }} :</span> {{ $cab->getApprovalModeName() }}</p>
                                    @if($cab->shouldShowRequiredApprovals())
                                        <p><span class="fw-bold">{{ trans("content.ticket_procurement.Required_Minimum_Approvals") }} :</span> {{ $cab->required_minimum_approvals }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Card for Members and Approvers --}}
                    <div class="container-fluid px-0">
                        <div class="card rounded-0">
                            <div class="card-body">

                                {{-- Location & Department Approval Tables (hierarchy_approval 4,5,6) --}}
                                @if(in_array($cab->hierarchy_approval, [4,5,6]))
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
                                {{-- Add Member Form --}}
                                <div class="add-member @if(!$show_form)  @endif">
                                    {{-- <form name="member" method="POST" action="{{ url('change-management/cab/members/update', ['id' => $mem->id]) }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-7">
                                                <h5 class="mb-3">{{ trans("content.ticket_procurement.New_Member") }}</h5>

                                                <div class="mb-3 row">
                                                    <label class="col-md-3 col-form-label fw-semibold mandatory" for="user">
                                                        {{ trans("content.ticket_procurement.User") }}
                                                    </label>
                                                    <div class="col-md-9">
                                                        <select name="user" id="user" class="form-select">
                                                            <option value="">{{ trans("general.Select_User") }}</option>
                                                        </select>
                                                        @if(isset($errors) && $errors->get('user'))
                                                            <div class="invalid-feedback d-block">{{ $errors->get('user')[0] }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($cab->hierarchy_approval == 1)
                                                    @php
                                                        $approvalLevels = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
                                                        $usedLevels = $cabMembers->pluck('hierarchy_level')->toArray();
                                                        $available = array_diff($approvalLevels, $usedLevels);
                                                    @endphp
                                                    <div class="mb-3 row">
                                                        <label class="col-md-3 col-form-label fw-semibold" for="hierarchy_level">
                                                            {{ trans("content.ticket_procurement.Approval_Level") }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            <select name="hierarchy_level" id="hierarchy_level" class="form-select">
                                                                @foreach($available as $level)
                                                                    <option value="{{ $level }}">{{ $level }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if(isset($errors) && $errors->get('hierarchy_level'))
                                                                <div class="invalid-feedback d-block">{{ $errors->get('hierarchy_level')[0] }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-9 offset-md-3 d-flex gap-2">
                                                        <button class="amg-btn amg-btn-primary" type="submit" id="btnSubmit">
                                                            {{ trans("button.add") }}
                                                        </button>
                                                        <button class="amg-btn amg-btn-secondary" type="button" id="show-members">
                                                            {{ trans("button.cancel") }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form> --}}
                                    <form name="member" method="POST" action="{{ url('change-management/cab/members/update', ['id' => $mem->id]) }}">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h4 class="title">{{ trans("content.change_management_fields.Edit_Member") }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="pab_id" value="{{ $cab->id }}">
                                                <div class="form-group mar-top">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="control-label text-bold mandatory" for="user">{{ trans("content.ticket_procurement.User") }}</label>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <div>
                                                                <select name="user" id="user" class="form-control">
                                                                    <option value="{{$mem->user_id}}">{{$mem->user->fullName()}}</option>
                                                                </select>
                                                            </div>
                                                            @if(isset($errors) && $errors->get('user'))
                                                                <label class="error">{{ $errors->get('user')[0] }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($cab->hierarchy_approval == 1)
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="control-label text-bold" for="user">{{ trans("content.ticket_procurement.Approval_Level") }}</label>
                                                        </div>
                                                        @php $approvalLebal=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]; 
                                                        $remove=[];
                                                        foreach ($cabMembers as $key => $mems) {
                                                            if($mem->user_id !=$mems->user_id){
                                                                array_push($remove,$mems->hierarchy_level);
                                                            }
                                                        }
                                                        $label=array_diff($approvalLebal,$remove)  ;                                           
                                                        @endphp
                                                        <div class="col-sm-9">
                                                            <div>
                                                                <select name="hierarchy_level" id="hierarchy_level" class="form-control">
                                                                    <?php foreach($label as $lab) { ?>
                                                                        <option value="{{ $lab }}" @if($lab==$mem->hierarchy_level){{"selected"}} @endif>{{ $lab }}</option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            @if(isset($errors) && $errors->get('hierarchy_level'))
                                                                <label class="error">{{ $errors->get('hierarchy_level')[0] }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="row mt-2">
                                                    <div class="col-md-9 offset-md-3 d-flex gap-2">
                                                        <button class="amg-btn amg-btn-primary" type="submit" id="btnSubmit">
                                                            {{ trans("button.update") }}
                                                        </button>
                                                        <button class="amg-btn amg-btn-secondary" type="button" id="show-members">
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
        </section>
    </div>
@endsection
@push('scripts')
    <script src="{!! CommonHelper::asset('js/cab/members.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#show-members").on("click", function(e) {
                window.location = '{{ url("change-management/cab/members",$cab->id ) }}';
            });
            var config = new Object;
            config.url = new Object;
            config.url.get_locations = "{{ url('getLocationByQuery') }}";
            config.url.get_departments = "{{ route('getDepartment') }}";
            config.url.getUserByAjax = "{{  url('getUserByQuery') }}";
            config.url.actionUpdate = "{{ route('actionUpdate') }}";
            config.url.cabList = "{{ url('change-management/cab/list') }}"
            {{--config.users = {!! json_encode($users) !!};--}}
            config.cab = {!! json_encode($cab) !!};
            config.cabMembers = {!! json_encode($cabMembers) !!};
            config.token = "{{ csrf_token() }}";
            new CABMembers(config);
        });
    </script>
@endpush