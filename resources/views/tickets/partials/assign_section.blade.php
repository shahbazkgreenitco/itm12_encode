{{-- Assign Section --}}
<div class="tkd-panel">
    <div class="tkd-panel-head d-flex align-items-center justify-content-between" data-bs-toggle="collapse"
        data-bs-target="#assigned_user_panel" role="button" aria-expanded="false" aria-controls="assigned_user_panel">
        <div class="d-flex align-items-center gap-2">
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <polyline points="6 9 12 15 18 9" />
            </svg>
            <span class="b4-text">{{ trans('content.service_ticket_fields.Assigned_To') }}</span>
        </div>
    </div>

    <!-- Always visible summary (collapsed state) -->
    <div class="p-1 pb-1">
        @if (!$assigned_to || !$assigned_to->exists)
            @if ($ticket->assigned_to !== null || in_array($ticket->status_id, [5, 6]))
                <div class="d-flex align-items-center gap-3">
                    <img class="rounded-circle" width="40" height="40" alt="Profile Picture"
                        src="{{ file_exists(public_path('imgs/profile-75.jpg')) ? asset('imgs/profile-75.jpg') : asset('imgs/default-profile.png') }}">
                    <div>
                        <div class="b5-text fw-semibold">{{ trans('ticket.ticket_detail.system') }}</div>
                        <div class="b7-text opacity-70">@ System</div>
                    </div>
                </div>
            @else
                <div class="b7-text opacity-70">
                    {{ trans('content.service_ticket_fields.No_Assigned_User_Found') }}</div>
            @endif
        @else
            <div class="d-flex align-items-center gap-3 flex-column">
                <img class="rounded-circle" width="40" height="40" alt="Profile Picture"
                    src="{{ $assigned_to->getProfileImg() }}">
                <div class="d-flex flex-column align-items-center">
                    <div class="b5-text fw-semibold">
                    <a href="{{ config('app.url') }}/user/info/{{$assigned_to->id}}">
                            {{ $assigned_to->fullName() ?? $assigned_to->username }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Expandable details (collapsed initially) -->
    <div id="assigned_user_panel" class="collapse">
        <div class="p-3 pt-0">
            @if ($assigned_to && $assigned_to->exists)
                <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                    <span class="b7-text text-muted" style="min-width:110px;">
                        {{ trans('content.user_fields.emp_code') }}
                        <span>:</span>
                    </span>

                    <span class="b7-text opacity-70">
                        {{ $assigned_to->employee_num }}
                    </span>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                    <span class="b7-text text-muted" style="min-width:110px;">
                        {{ trans('content.user_fields.designation') }}
                        <span>:</span>
                    </span>

                    <span class="b7-text opacity-70">
                        {{ $assigned_to->jobtitle }}
                    </span>
                </div>

                @if ($assigned_to->department)
                    <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                        <span class="b7-text text-muted" style="min-width:110px;">
                            {{ trans('content.user_fields.department') }}
                            <span>:</span>
                        </span>
                        <span class="b7-text opacity-70">
                            {{ $assigned_to->department->name }}
                        </span>
                    </div>
                @endif

                @if ($assigned_to->location)
                    <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                        <span class="b7-text text-muted" style="min-width:110px;">
                            {{ trans('content.user_fields.location') }}
                            <span>:</span>
                        </span>
                        <span class="b7-text opacity-70">
                            {{ $assigned_to->location->name }}
                        </span>
                    </div>
                @endif

                @if ($assigned_to->baselocation)
                    <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                        <span class="b7-text text-muted" style="min-width:110px;">
                            {{ trans('content.user_fields.base_location') }}
                            <span>:</span>
                        </span>
                        <span class="b7-text opacity-70">
                            {{ $assigned_to->baselocation->name }}
                        </span>
                    </div>
                @endif

                <div class="d-flex flex-wrap gap-2 align-items-baseline mb-2">
                    <span class="b7-text text-muted" style="min-width:110px;">
                        {{ trans('content.user_fields.mobile') }}
                        <span>:</span>
                    </span>
                    <span class="b7-text opacity-70">
                        {{ $assigned_to->phone }}
                    </span>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-baseline">
                    <span class="b7-text text-muted" style="min-width:110px;">
                        {{ trans('content.service_ticket_fields.Company') }}
                        <span>:</span>
                    </span>
                    <span class="b7-text opacity-70">
                        @if ($assigned_to->job_type > 0)
                            {{ $assigned_to->ex_user_company }}
                        @else
                            {{ optional($assigned_to->company)->name }}
                        @endif
                    </span>
                </div>

            @endif
        </div>
    </div>
</div>
