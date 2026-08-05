{{--
/**
* ------------------------------------------------------------
* File: status_change_approval_info.blade.php
* Module: Status Approval
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: SA07SCA-26
* Created On: 2026-05
* Reviewed By: N/A
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial setup
* ------------------------------------------------------------
*/
--}}
@extends('layouts.layout1')
@section('title', trans('ticket-types.status_approval_change.title'))
@section('content')
    <div id="main-status-approval-change-wrapper">
    <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">
        <div class="py-3 w-100">
            <div class="container-fluid d-flex align-items-center justify-content-between ps-0">
               {{-- <button
                    onclick="location.href='{{ url('status-change-approvals/' . $main_filter) }}'"
                    class="d-flex gap-2 align-items-center bg-transparent outline-none border-0">

                    <svg width="18" height="18" viewBox="0 0 25 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 0 10.7007 0 10.5033C0 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                            fill="currentColor" />
                    </svg>

                    <h3 class="h3-text mb-0">
                        {{ trans('ticket-types.status_approval_change.approval_status') }}
                    </h3>
                </button> --}}
            </div>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="list-view-panel">
                        <div class="card rounded-4">
                            <div class="card-body">
                               
                                <div class="row">
                                   
                                    <!-- Ticket Details -->
                                    <div class="col-md-6">
                                        {{-- <h5 class="fw-semibold mb-3">Ticket Details</h5> --}}
                                        <h5 class="fw-semibold mb-3">{{ trans('ticket-types.status_approval_change.approvalStatus.ticket_details') }}</h5>
                                            @if($approval->ticket)
                                        <div class="row mb-2">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.approvalStatus.ticket_id') }}</div>
                                            <div class="col-8 fw-medium">#{{ $approval->ticket->id ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.approvalStatus.subject') }}</div>
                                            <div class="col-8 fw-medium">{{ $approval->ticket->subject ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.table.department') }}</div>
                                            <div class="col-8 fw-medium">{{ $approval->ticket->department->name ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.table.category') }}</div>
                                            <div class="col-8 fw-medium">{{ $approval->ticket->problemCategory->name ?? '-' }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.table.subcategory') }}</div>
                                            <div class="col-8 fw-medium">{{ $approval->ticket->subCategory->name ?? '-' }}</div>
                                        </div>
                                            @else
                                                <div class="text-muted small">
                                                    Ticket information is no longer available because the ticket has been removed.
                                                </div>
                                            @endif
                                    </div>

                                    <!-- Approval Info -->
                                    <div class="col-md-6">
                                        <h5 class="fw-semibold mb-3">{{ trans('ticket-types.status_approval_change.approvalStatus.status_change_info') }}</h5>
                                        <div class="row mb-2">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.approvalStatus.requested_status') }}</div>
                                            <div class="col-8 fw-medium">{{ $approval->requestedStatus->name ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.approvalStatus.start_time') }}</div>
                                            <div class="col-8 fw-medium">{{ $approval->start_time ?? '-' }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4 text-muted small">{{ trans('ticket-types.status_approval_change.approvalStatus.end_time') }}</div>
                                            <div class="col-8 fw-medium">{{ $approval->end_time ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Reason -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row mb-2">
                                            <div class="col-2 text-muted small">{{ trans('ticket-types.status_approval_change.approvalStatus.reason') }}</div>
                                            <div class="col-10 fw-medium">{!! $approval->reason ?? '-' !!}</div>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                            <hr class="my-3" />
                            <h5 class="fw-semibold mb-3 ms-3">{{ trans('ticket-types.status_approval_change.approvalStatus.approver') }}</h5>
                            <div class="table-responsive">
                                <table id="statusApprovalChanges" class="table display app-data-table">
                                    <thead>
                                        <tr>
                                            <th><h4>{{ trans('ticket-types.status_approval_change.approvalStatus.name') }}</h4></th>
                                            <th><h4>{{ trans('ticket-types.status_approval_change.approvalStatus.email') }}</h4></th>
                                            <th><h4>{{ trans('ticket-types.status_approval_change.approvalStatus.approvalStatus') }}</h4></th>
                                            <th><h4>{{ trans('ticket-types.status_approval_change.approvalStatus.updated_at') }}</h4></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($approval->approvers as $apr_user)
                                            <tr>
                                                    <td>{{ ($apr_user->approver->first_name ?? '') . ' ' . ($apr_user->approver->last_name ?? '') . ' - (' . ($apr_user->approver->username ?? '-') . ')' }}</td>
                                                    <td>{{ $apr_user->approver->email ?? '-' }}</td>
                                                    <td>
                                                        @if($apr_user->status == 1)
                                                            <span class="badge bg-warning">Pending</span>
                                                        @elseif($apr_user->status == 2)
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif($apr_user->status == 3)
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @endif
                                                </td>
                                                    <td>{{ $apr_user->updated_at ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>                        
                    </div>                  
                </div>
            </div>
        </div> 
    </main>    
</div>
@endsection