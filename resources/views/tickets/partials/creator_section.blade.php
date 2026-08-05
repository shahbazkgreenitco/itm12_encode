 {{-- Creator Section --}}
 <div class="tkd-panel">
     <div class="tkd-panel-head d-flex align-items-center justify-content-between" data-bs-toggle="collapse"
         data-bs-target="#creator_info_panel" role="button" aria-expanded="false" aria-controls="creator_info_panel">
         <div class="d-flex align-items-center gap-2">
             <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                 <polyline points="6 9 12 15 18 9" />
             </svg>
             <span class="b4-text">{{ trans('content.service_ticket_fields.Creator_Info') }}</span>
             @if (isset($action_controls['ctrl_change_creator']) &&
                     $action_controls['ctrl_change_creator'] == 1 &&
                     $ticket->status_id != 6 &&
                     $ticket->status_id != 5 &&
                     $ticket->status_id != 10)
                 <a href="#" class="js-act-change-creator amg-link-sm ms-2">
                     <i class="bi bi-pencil-square"></i>
                     {{ trans('content.service_ticket_fields.Change_Creator') }}
                 </a>
             @endif
         </div>
     </div>

     <!-- Always visible summary (collapsed state) -->
     <div class="p-1 pb-1">
         <div class="d-flex align-items-center gap-3 flex-column">
             <img class="rounded-circle" width="40" height="40" alt="Profile Picture"
                 src="{{ $creator->getProfileImg() }}">
             <div class="d-flex flex-column align-items-center">
                 <div class="b5-text fw-semibold"><a href="{{ config('app.url') }}/user/info/{{$creator->id}}">{{ $creator->fullName() }}</a></div>
                 {{-- <div class="b7-text opacity-70">@ {{ $creator->username }}</div>
                                    <div class="b7-text opacity-70 mt-1">{{ $creator->email }}</div> --}}
             </div>
         </div>
     </div>

     <!-- Expandable details (collapsed initially) -->
     <div id="creator_info_panel" class="collapse">
         <div class="p-3 pt-0">
             <div class="row">
                 <div class="col-12">
                     <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                         <span class="b7-text text-muted" style="min-width: 110px;">
                             {{ trans('content.user_fields.emp_code') }}
                             <span>:</span>
                         </span>
                         <span class="b7-text opacity-70">{{ $creator->employee_num }}</span>
                     </div>
                     <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                         <span class="b7-text text-muted" style="min-width: 110px;">
                             {{ trans('content.user_fields.designation') }}
                             <span>:</span>
                         </span>
                         <span class="b7-text opacity-70">{{ $creator->jobtitle }}</span>
                     </div>
                     <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                         <span class="b7-text text-muted" style="min-width: 110px;">
                             {{ trans('content.user_fields.department') }}
                             <span>:</span>
                         </span>
                         <span class="b7-text opacity-70">
                             @if ($creator->department)
                                 {{ $creator->department->name }}
                             @endif
                         </span>
                     </div>
                     <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                         <span class="b7-text text-muted" style="min-width: 110px;">
                             {{ trans('content.user_fields.location') }}
                             <span>:</span>
                         </span>
                         <span class="b7-text opacity-70">
                             @if ($creator->location)
                                 {{ $creator->location->name }}
                             @endif
                         </span>
                     </div>
                     <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                         <span class="b7-text text-muted" style="min-width: 110px;">
                             {{ trans('content.user_fields.base_location') }}
                             <span>:</span>
                         </span>
                         <span class="b7-text opacity-70">
                             @if ($creator->baselocation)
                                 {{ $creator->baselocation->name }}
                             @endif
                         </span>
                     </div>
                     <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                         <span class="b7-text text-muted" style="min-width: 110px;">
                             {{ trans('content.user_fields.mobile') }}
                             <span>:</span>
                         </span>
                         <span class="b7-text opacity-70">{{ $creator->phone }}</span>
                     </div>
                     <div class="d-flex flex-wrap gap-2 align-items-baseline">
                         <span class="b7-text text-muted" style="min-width: 110px;">
                             {{ trans('content.service_ticket_fields.Company') }}
                             <span>:</span>
                         </span>
                         <span class="b7-text opacity-70">
                             @if ($creator->job_type != null && $creator->job_type > 0)
                                 {{ $creator->ex_user_company }}
                             @else
                                 {{ $creator->company != null ? $creator->company->name : '' }}
                             @endif
                         </span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
