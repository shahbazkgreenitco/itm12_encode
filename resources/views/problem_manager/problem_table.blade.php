{{-- * ------------------------------------------------------------
* File: problem_table.blade.php
* Module: Problem Management
* PROBM/26/03
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #003
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}


@extends('layouts.layout1')
@section('title', trans("header.problem_manage.problem_manage"))
@section('content')
<div id="main-user-list-wrapper">
<section class="content">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">
            <button onclick="location.href='{{ url('problem_manager/AllProblemLists') }}'" class="bg-transparent outline-none border-0" data-bs-toggle="tooltip" title="Back">
                <svg width="16" height="21" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor"></path>
                </svg>
            </button>
            {{  trans("header.problem_manage.problem_manage") }}- #{{ $problem->id }}</h3>
        <div class="d-flex gap-8">
           
           
            <button type="button" data-id="{{$problem->id}}" class="header-action-btn-with-text btn d-flex align-items-center justify-content-center gap-2 px-3 border rounded-2 bg-white pmHistory">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                </svg>
                {{-- <i class="bi bi-clock-history"></i> --}}
                <span>History</span>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0 mb-0">
                <div class="card-body">                   
                    <div class="col-md-12">
                        <p>
                            <span class="text-bold">{{ trans('content.problem_manager.attender') }} : </span> {{$problem->handler_names}}
                        </p>
                        <p>
                            <span class="text-bold">{{ trans('content.problem_manager.problem') }} : </span> 
                            {{$problem->name}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-bar">
            <ul class="nav nav-underline" id="myTab" role="tablist">                
                <li class="nav-item">
                    <a class="nav-link active" id="impact_ticket_tab" data-bs-toggle="tab" href="#impactTicket" aria-controls="impactTicket" role="tab" aria-expanded="true">
                        <span class="b1-text fw-medium">{{ trans("content.problem_manager.Impacted_Ticket") }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="impact_devices_tab" data-bs-toggle="tab" href="#impactDevice" role="tab" aria-controls="impactDevice">
                        <span class="b1-text fw-medium">{{ trans("content.problem_manager.Impacted_Devices") }}</span>
                    </a>
                </li>                
            </ul>
        </div>
        
        <div class="tab-content tabcontent-border p-3" id="myTabContent">          
            <div class="tab-pane fade show active" id="impactTicket" role="tabpanel" aria-labelledby="impact_ticket_tab">
                <div class="d-flex align-items-center gap-2 mb-4" data-select2-id="select2-data-5-1om7">
                    <div class="col-auto">
                        <select id="impactTicket-page-length" class="amg-table-pagination-dropdown userModulePageLenth impactTicket-page-length" aria-label="Rows per page">
                            <option value="10" selected>{{ trans('content.dynamic_form.show') }} (10)</option>
                            <option value="15">{{ trans('content.dynamic_form.show') }} (15)</option>
                            <option value="25">{{ trans('content.dynamic_form.show') }} (25)</option>
                            <option value="50">{{ trans('content.dynamic_form.show') }} (50)</option>
                        </select>
                    </div>

                    <!-- spacer -->
                    <div class="flex-grow-1"></div>
                    <div>
                        <div class="amg-list-searchbar" id="fieldset-list-search">
                            <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                            </svg>
                            <input type="text" class="amg-list-searchbar__input holiday-list-search" id="fieldset-searchbox" placeholder="{{ trans('content.problem_manager.press_enter_with_Search') }}">
                        </div>
                    </div>
                    <button class="header-icon-btn-only header-icon-btn-only-sm btnt-complete" type="button" data-bs-toggle="tooltip" title="{{ trans('content.problem_manager.Complete') }}">
                        <svg  width="24" height="24" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>
                    </button>
                    <button class="header-icon-btn-only header-icon-btn-only-sm btnt-incomplete" type="button" data-bs-toggle="tooltip" title="{{ trans('content.problem_manager.Incomplete') }}">
                        <svg  width="24" height="24" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>
                    </button>
                    
                </div>

                <div class="table-responsive" style="overflow-y:hidden;">
                    <table name="impactTicket" id="impactTicketTable" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><input class="form-check-input select-all-ticket" type="checkbox"></th>
                                <th><h4 class="b2-text">{{ trans("header.problem_manage.Impacted_Ticket") }}</h4></th>
                                <th><h4 class="b2-text">{{ trans("header.problem_manage.status") }}</h4></th>
                                <th><h4 class="b2-text">{{ trans("content.problem_manager.CreatedAt") }}</h4></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="impactDevice" role="tabpanel" aria-labelledby="impact_devices_tab">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="col-auto">
                        <select id="impactDevice-page-length" class="amg-table-pagination-dropdown userModulePageLenth impactDevice-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>

                    <div class="flex-grow-1"></div>

                    <div>
                        <div class="amg-list-searchbar">
                            <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                            </svg>
                            <input type="text" class="amg-list-searchbar__input holiday-list-search" id="fields-searchbox" placeholder="Search...">
                        </div>
                    </div>

                    <button class="header-icon-btn-only header-icon-btn-only-sm btnd-complete" type="button" data-bs-toggle="tooltip" title="{{ trans('content.problem_manager.Complete') }}">
                        <svg  width="24" height="24" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>
                    </button>
                    <button class="header-icon-btn-only header-icon-btn-only-sm btnd-incomplete" type="button" data-bs-toggle="tooltip" title="{{ trans('content.problem_manager.Incomplete') }}">
                        <svg  width="24" height="24" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>
                    </button>
                    
                </div>

                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="impactDevices" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><input class="form-check-input select-all-device" type="checkbox"></th>
                                <th class="b2-text">{{ trans("header.problem_manage.Impacted_Devices") }}</th>
                                <th class="b2-text">{{ trans("header.problem_manage.status") }}</th>
                                <th class="b2-text">{{ trans("content.problem_manager.CreatedAt") }}</th>
                                <th class="b2-text">{{ trans("content.problem_manager.Handler") }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>  

        </div>
    </main>
   @include("problem_manager.pm_history_modal")
</section>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
<style>
    .btn-open-filter {
        position: relative;
    }
    .form-check-input.select-all-ticket {
        border: 1px solid #f5a3a3; /* Light red */
    }
    .filter-count-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #dc3545;
        color: #fff;
        border-radius: 10px;
        font-size: 10px;
        line-height: 1;
        min-width: 16px;
        height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
    }
</style>
@endpush
@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/problem_management/manageproblem.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('plugins/Event-Calendar/dist/calendar.js') !!}"></script>
<script type="text/javascript">
    var config = new Object;
    config.url = new Object;
    config.translations = {
        something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
        are_you_update: '{{ trans('content.problem_manager.Problem_Status') }}',
        Complete: '{{ trans('content.problem_manager.Complete') }}',
        Incomplete: '{{ trans('content.problem_manager.Incomplete') }}',
    };
    config.url.getProlemManage = "{{ url('jx-get-user-problem-management') }}";
    config.url.getProlemManageTicket = "{{ url('jx-get-user-problem-management-ticket') }}";
    config.url.solveMultipleProblem = "{{ url('problem-management/select-multiple-problem') }}";
    config.url.unsolveMultipleProblem = "{{ url('problem-management/select-multiple-problem-unsolve') }}";
    config.url.solveDeviceMultipleProblem = "{{ url('problem-management/select-multiple-problem-device') }}";
    config.url.unsolveDeviceMultipleProblem = "{{ url('problem-management/select-multiple-problem-device-unsolve') }}";
    config.url.pm_history = "{{ url('problem-management/pm_history') }}";
    
    config.groupDeviceId = "<?php if (!empty($groupDevice)) echo $groupDevice->problem_id ?>";
    config.groupTicketId = "<?php if (!empty($groupTicket)) echo $groupTicket->problem_id ?>";
    config.token = "{{ csrf_token() }}";
    new GroupsProblem(config);
</script>

@endpush


