{{-- @page-meta
{
  "page_no": "USR06I-26",
  "file": "info-tab.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    },
    {
      "version": "1.01",
      "writer": "Prithvi Pillai",
      "from": "2026-06",
      "reviewer": null,
      "description": "Changes for the components backtick and ui issue changes"
    },
    {
      "version": "1.02",
      "writer": "Safdar Ali",
      "from": "2026-06",
      "reviewer": null,
      "description": "Tab wise re-direction from right side cards"
    }

  ]
}
--}}
@php
    $phone_code = optional($user->phoneCountry)->phonecode;
    $alt_phone_code = optional($user->phone2Country)->phonecode;
    $work_phone_code = optional($user->workPhoneCountry)->phonecode;
@endphp
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="mb-3">
        <h5 class="fw-semibold mb-0">{{ trans('user.overview_title') }}</h5>
    </div>
    <!-- Layout Row -->
    <div class="row g-3">
        <!-- Left: Main Overview Card -->
        <div class="col-lg-8 col-md-7">
            <div class="card rounded-4">
                <div class="card-body">
                    <!-- Section 1 -->
                    <div class="row mb-2">
                        <div class="col-4">
                            <span class="b5-text fw-regular" >{{ trans('user.fields.first_name') }}</span>
                        </div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F" >{{ $user->first_name }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4">
                            <span class="b5-text fw-regular">{{ trans('user.fields.last_name') }}</span>
                        </div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->last_name }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 ">
                            <span class="b5-text fw-regular">{{ trans('user.fields.role') }}</span>
                        </div>
                        <div class="col-8">
                            @foreach ($user->roles as $role)
                                <span class="badge rounded-pill b5-text fw-regular text-black" style="background-color: #E8FFF6;">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4">
                            <span class="b5-text fw-regular">{{ trans('user.fields.username') }}</span>
                        </div>
                        <div class="col-8 d-flex align-items-center gap-2">
                            <img src="{{ $user->getProfileImg() }}" class="rounded-circle" width="20" />
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->username }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4">
                            <span class="b5-text fw-regular">{{ trans('user.fields.email') }}</span>
                        </div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->email }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4">
                            <span class="b5-text fw-regular">{{ trans('user.fields.contact_number') }}</span>
                        </div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ ($phone_code ? (Str::startsWith($phone_code, '+') ? $phone_code : '+' . $phone_code) : '') . ' ' . $user->phone }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <span class="b5-text fw-regular">{{ trans('user.fields.active_status') }}</span>
                        </div>
                        <div class="col-8">
                            <span class="badge rounded-pill"
                                style="background-color: {{ $user->activated ? '#028244' : '#dc3545' }};">
                                <span class="rounded-circle"
                                    style="width:7px; height:7px; display:inline-block; background:#fff !important"></span>
                                <span class="b5-text fw-regular">{{ $user->activated ? trans('user.status.active') : trans('user.status.inactive') }}</span>
                            </span>
                        </div>
                    </div>

                    <hr class="my-3" />

                    <!-- Section 2 -->
                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.emp_code') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->employee_num }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.date_of_joining') }}</span></div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->doj ? CommonHelper::displayDateTime($user->doj, 'date', 'display') : '' }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.last_working_date') }}</span></div>
                        <div class="col-8 ">
                             @if(empty($user->last_working_date))
                            <svg width="20" height="18" viewBox="0 0 20 18" fill="none"
                                >
                                <path
                                    d="M12 8.25C12 8.44891 11.921 8.63968 11.7803 8.78033C11.6397 8.92098 11.4489 9 11.25 9H8.25C8.05109 9 7.86032 8.92098 7.71967 8.78033C7.57902 8.63968 7.5 8.44891 7.5 8.25C7.5 8.05109 7.57902 7.86032 7.71967 7.71967C7.86032 7.57902 8.05109 7.5 8.25 7.5H11.25C11.4489 7.5 11.6397 7.57902 11.7803 7.71967C11.921 7.86032 12 8.05109 12 8.25Z"
                                    fill="currentColor" />
                            </svg>
                            @endif
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->last_working_date ? CommonHelper::displayDateTime($user->last_working_date, 'date', 'display') : '' }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.designation') }}</span></div>
                        <div class="col-8 d-flex align-items-center gap-2">
                             @if(empty($user->jobtitle))
                            <svg width="20" height="18" viewBox="0 0 20 18" fill="none"
                                >
                                <path
                                    d="M12 8.25C12 8.44891 11.921 8.63968 11.7803 8.78033C11.6397 8.92098 11.4489 9 11.25 9H8.25C8.05109 9 7.86032 8.92098 7.71967 8.78033C7.57902 8.63968 7.5 8.44891 7.5 8.25C7.5 8.05109 7.57902 7.86032 7.71967 7.71967C7.86032 7.57902 8.05109 7.5 8.25 7.5H11.25C11.4489 7.5 11.6397 7.57902 11.7803 7.71967C11.921 7.86032 12 8.05109 12 8.25Z"
                                    fill="currentColor" />
                            </svg>
                            @endif
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->jobtitle }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.user_type') }}</span></div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F" >
                                @if ($user->job_type == 1)
                                    {{ trans('user.user_type_options.contract_staff') }}
                                @elseif($user->job_type == 2)
                                    {{ trans('user.user_type_options.external_user') }}
                                @else
                                    {{ trans('user.user_type_options.company_staff') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.alternate_phone') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F">
                            {{ ($alt_phone_code ? (Str::startsWith($alt_phone_code, '+') ? $alt_phone_code : '+' . $alt_phone_code) : '') . ' ' . $user->phone2 }}
                        </span></div>
                    </div>

                    <hr class="my-3" />

                    <!-- Section 3 -->
                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.work_phone') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F" >
                            {{ ($work_phone_code ? (Str::startsWith($work_phone_code, '+') ? $work_phone_code : '+' . $work_phone_code) : '') . ' ' . $user->work_phone }}
                        </span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.business_unit') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->business_unit }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.department_unit') }}</span></div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->delivery_unit }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular" >{{ trans('user.fields.communication_address') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F" >{{ $user->address }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.department') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->department->name ?? '' }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.company') }}</span></div>
                        <div class="col-8">
                            <span class="badge text-black b5-text fw-regular" style="background-color: #E8FFF6;">
                                {{ $user->company->name ?? '' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 ">
                            <span class="b5-text fw-regular">{{ trans('user.fields.user_accessible_company') }}</span>
                        </div>
                    
                        <div class="col-8">
                            @if (!empty($userAccessCompany) && count($userAccessCompany) > 0)
                            @php
                            $companies = $userAccessCompany;
                            $visible = $companies->take(2);
                            @endphp
                    
                            @foreach ($visible as $comp)
                            <span class="badge text-black me-1 mb-1 b5-text fw-regular" style="background-color: #E8FFF6;">
                                {{ $comp->name }}
                            </span>
                            @endforeach
                    
                            @if($companies->count() > 2)
                            <a href="javascript:void(0)" class="read-more-company b5-text fw-regular"
                                data-companies="{{ $companies->pluck('name')->implode(',') }}">
                                +{{ $companies->count() - 2 }}more
                            </a>
                            @endif
                            @else
                            -
                            @endif
                        </div>
                    </div>

                    <hr class="my-3" />

                    <!-- Section 5 -->
                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.location') }}</span></div>
                        <div class="col-8 fw-medium"><span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->location->name ?? '' }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.internal_place') }}</span></div>
                        <div class="col-8 fw-medium"><span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->places->place ?? '' }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.base_location') }}</span></div>
                        <div class="col-8 fw-medium"><span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->baseLocation->name ?? '' }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.manager') }}</span></div>
                        <div class="col-8 d-flex align-items-center gap-2">
                            <img src="{{ $user->manager?->getProfileImg() }}" class="rounded-circle" width="20" />
                            <span class="b5-text fw-regular" style="color:#7F7F7F" >{{ $user->manager?->getGuranteedNameText(true) }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4"><span class="b5-text fw-regular">{{ trans('user.fields.seat_no') }}</span></div>
                        <div class="col-8">
                            <span class="b5-text fw-regular" style="color:#7F7F7F" >{{ $user->userDetails->seat_no ?? '' }}</span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.grade') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F" >{{ $user->userDetails->grade ?? '' }}</span></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-4 "><span class="b5-text fw-regular">{{ trans('user.fields.notes') }}</span></div>
                        <div class="col-8"><span class="b5-text fw-regular" style="color:#7F7F7F">{{ $user->notes }}</span></div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Right: Sidebar Cards -->
        <div class="col-lg-4 col-md-5">
            <div class=" rounded-4">
                <div class="card-body">

                    <!-- Profile Summary -->
                    <div class="card rounded-4">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center gap-3 p-3 border rounded-3">
                                <img src="{{ $user->getProfileImg() }}"
                                    class="rounded-circle border border-2 border-danger" width="52"
                                    height="52" />
                                <div>
                                    <div class="fw-semibold">
                                        <span class="s1-text fw-medium">{{ $user->getGuranteedNameText(true) }}</span>
                                    </div>
                                    <div class="">
                                        <span class="b5-text fw-regular" style="color:#7F7F7F" >{{ $user->jobtitle ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Stats Grid -->
                    <div class="card rounded-4">
                        <div class="card-body p-4">
                            <div class="row g-2">

                                <!-- card 1 -->
                                <div class="col-6 d-flex">
                                    <div class="p-3 rounded-4 user-stats-card border bg-light se-cards">
                                        <div>
                                            <span class="b3-text fw-regular" style="color:#7F7F7F !important">{{ trans('user.sidebar.stats.total_tickets_open') }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end pt-2">
                                            <span class="fw-bold s1-text">
                                                {{ $user->ticket_count ?? 0 }}
                                            </span>

                                            <span
                                                class="d-flex align-items-center justify-content-center border rounded-circle"
                                                style="height:40px; width:40px;">
                                                <svg width="20" height="20" viewBox="0 0 24 25"
                                                    fill="none" >
                                                    <path d="M7 22H17" stroke="#E20505" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z"
                                                        stroke="#E20505" stroke-width="1.5" />
                                                    <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- card 2 -->
                                <div class="col-6 d-flex">
                                    <div class="p-3 rounded-4 user-stats-card border se-cards open-tab">
                                        <div>
                                            <span class="b3-text fw-regular" style="color:#7F7F7F !important">{{ trans('user.sidebar.stats.total_devices') }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end pt-2">
                                            <span class="fw-bold s1-text">
                                                <a href="javascript:void(0)" class="text-decoration-none text-dark open-tab" data-tab="assets-tab">
                                                {{ $user->devices_count ?? 0 }}
                                                </a>
                                            </span>

                                            <span class="d-flex align-items-center justify-content-center border rounded-circle"
                                                style="height:40px; width:40px;">
                                                <svg width="20" height="20" viewBox="0 0 24 25" fill="none">
                                                    <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z"
                                                        stroke="#E20505" stroke-width="1.5" />
                                                    <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- card 3 -->
                                <div class="col-6 d-flex">
                                    <div class="p-3 rounded-4 border user-stats-card se-cards open-tab">
                                        <div>
                                            <span class="b3-text fw-regular" style="color:#7F7F7F !important" >{{ trans('user.sidebar.stats.total_components') }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end pt-2">
                                            <span class="fw-bold s1-text">
                                                <a href="javascript:void(0)" class="text-decoration-none text-dark open-tab"
                                                    data-tab="components-tab-link">
                                                {{ $user->component_count ?? 0 }}
                                                </a>
                                            </span>
                                            <span class="d-flex align-items-center justify-content-center border rounded-circle"
                                                    style="height:40px; width:40px;">
                                                <svg width="20" height="20" viewBox="0 0 24 25" fill="none">
                                                    <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                        <path
                                                            d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z"
                                                            stroke="#E20505" stroke-width="1.5" />
                                                    <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                
                                        </div>
                                    </div>
                                </div>

                                <!-- card 4 -->
                                <div class="col-6 d-flex">
                                    <div class="p-3 rounded-4 border user-stats-card se-cards">
                                        <div>
                                            <span class="b3-text fw-regular" style="color:#7F7F7F !important">{{ trans('user.sidebar.stats.total_accessories') }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end pt-2">
                                            <span class="fw-bold s1-text">
                                                <a href="javascript:void(0)" class="text-decoration-none text-dark open-tab"
                                                    data-tab="accessories-tab-link">
                                                {{ $user->accessory_count ?? 0 }}
                                                </a>
                                            </span>
                                            <span class="d-flex align-items-center justify-content-center border rounded-circle"
                                                style="height:40px; width:40px;">
                                                <svg width="20" height="20" viewBox="0 0 24 25" fill="none">
                                                    <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z"
                                                        stroke="#E20505" stroke-width="1.5" />
                                                    <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- card 5 -->
                                <div class="col-6 d-flex">
                                    <div class="p-3 rounded-4 border user-stats-card se-cards">
                                        <div>
                                            <span class="b3-text fw-regular" style="color:#7F7F7F !important">{{ trans('user.sidebar.stats.total_licenses') }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end pt-2">
                                            <span class="fw-bold s1-text">
                                                <a href="javascript:void(0)" class="text-decoration-none text-dark open-tab" data-tab="license-tab">
                                                {{ $user->licenses_count ?? 0 }}
                                                </a>
                                            </span>

                                            <span class="d-flex align-items-center justify-content-center border rounded-circle"
                                                style="height:40px; width:40px;">
                                                <svg width="20" height="20" viewBox="0 0 24 25" fill="none">
                                                    <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z"
                                                        stroke="#E20505" stroke-width="1.5" />
                                                    <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- card 6 -->
                                <div class="col-6 d-flex">
                                    <div class="p-3 rounded-4 border user-stats-card se-cards">
                                        <div>
                                            <span class="b3-text fw-regular" style="color:#7F7F7F !important" >{{ trans('user.sidebar.stats.total_consumables') }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-end pt-2">
                                            <span class="fw-bold s1-text">
                                                <a href="javascript:void(0)" class="text-decoration-none text-dark open-tab"
                                                    data-tab="consumables-tab-link">
                                                {{ $user->consumable_count ?? 0 }}
                                                </a>
                                            </span>

                                            <span class="d-flex align-items-center justify-content-center border rounded-circle"
                                                style="height:40px; width:40px;">
                                                <svg width="20" height="20" viewBox="0 0 24 25" fill="none">
                                                    <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z"
                                                        stroke="#E20505" stroke-width="1.5" />
                                                    <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Login Activity -->
                    <div class="card border rounded-3 pb-3">
                        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
                            <div>
                                <div class="b2-text fw-medium">{{ trans('user.sidebar.login_activity.title') }}</div>
                                <div class="">
                                    @php
                                        $currentSession = collect($user_sessions)->first(function ($s) {
                                            return $s->isSameSession();
                                        });
                                        $agent = $currentSession ? $currentSession->getAgent() : null;
                                    @endphp

                                    @if ($agent)
                                        <span class="b4-text fw-regular" style="color:#7F7F7F !important" >
                                            @if ($agent->isDesktop())
                                                {{ $agent->platform() }} {{ $agent->version($agent->platform()) }},
                                                {{ $agent->browser() }} {{ $agent->version($agent->browser()) }}
                                            @elseif($agent->isPhone())
                                                {{ $currentSession->getPhoneOsType() }},
                                                {{ $agent->browser() }} {{ $agent->version($agent->browser()) }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if(Auth::user()->id == $user_id)
                                <button class="btn btn-sm d-flex align-items-center gap-2 px-4 text-white make-logout"
                                    data-session="{{ $currentSession->session_id }}"
                                    style="background-color:#F12F35; height:30px;">
                                      <svg width="16" height="16" viewBox="0 0 18 18" fill="none"
                                            >
                                            <path
                                                d="M7.5 17.25C7.5 17.4489 7.42098 17.6397 7.28033 17.7803C7.13968 17.921 6.94891 18 6.75 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V0.75C0 0.551088 0.0790178 0.360322 0.21967 0.21967C0.360322 0.0790178 0.551088 0 0.75 0H6.75C6.94891 0 7.13968 0.0790178 7.28033 0.21967C7.42098 0.360322 7.5 0.551088 7.5 0.75C7.5 0.948912 7.42098 1.13968 7.28033 1.28033C7.13968 1.42098 6.94891 1.5 6.75 1.5H1.5V16.5H6.75C6.94891 16.5 7.13968 16.579 7.28033 16.7197C7.42098 16.8603 7.5 17.0511 7.5 17.25ZM17.7806 8.46937L14.0306 4.71937C13.8899 4.57864 13.699 4.49958 13.5 4.49958C13.301 4.49958 13.1101 4.57864 12.9694 4.71937C12.8286 4.86011 12.7496 5.05098 12.7496 5.25C12.7496 5.44902 12.8286 5.63989 12.9694 5.78063L15.4397 8.25H6.75C6.55109 8.25 6.36032 8.32902 6.21967 8.46967C6.07902 8.61032 6 8.80109 6 9C6 9.19891 6.07902 9.38968 6.21967 9.53033C6.36032 9.67098 6.55109 9.75 6.75 9.75H15.4397L12.9694 12.2194C12.8286 12.3601 12.7496 12.551 12.7496 12.75C12.7496 12.949 12.8286 13.1399 12.9694 13.2806C13.1101 13.4214 13.301 13.5004 13.5 13.5004C13.699 13.5004 13.8899 13.4214 14.0306 13.2806L17.7806 9.53063C17.8504 9.46097 17.9057 9.37825 17.9434 9.2872C17.9812 9.19616 18.0006 9.09856 18.0006 9C18.0006 8.90144 17.9812 8.80384 17.9434 8.7128C17.9057 8.62175 17.8504 8.53903 17.7806 8.46937Z"
                                                fill="white" />
                                        </svg>
                                        <span class="b4-text fw-medium">
                                            <a href="#" class="logout-btn text-decoration-none text-white">{{ trans('user.sidebar.login_activity.logout') }}</a>
                                        </span>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                                            @csrf
                                    </form>

                                </button>
                            @endif
                        </div>

                        <div class="px-3 mt-3 activity-scroll"
                            style="max-height: 260px;overflow-y: auto;margin-right: 15px;">
                            <ul class="list-unstyled mb-0">

                                @foreach ($user_sessions as $user_session)
                                    <li class=" py-2">

                                        <small>
                                            <span class="b4-text fw-regular" style="color:#7F7F7F !important">{{ trans('user.sidebar.login_activity.last_active') }} :</span>
                                            <span class="b4-text fw-regular" style="color:#7F7F7F !important">
                                                {{ CommonHelper::displayDateTime($user_session->last_active, 'datetime', 'display') }}
                                            </span>
                                            <span class="b4-text fw-regular" style="color:#7F7F7F !important">{{ CommonHelper::displayDateTime($user_session->last_active, 'datetime', 'display') }}</span>
                                        </small>

                                        @if ($user_session->isSameSession())
                                            <div><small class="b2-text fw-regular" style="color:#7F7F7F !important">{{ trans('user.sidebar.login_activity.current_window') }}</small></div>
                                        @elseif($user_session->status == 1)
                                            <div>
                                                <a href="#" class="make-logout" class="b2-text fw-regular"
                                                    data-session="{{ $user_session->session_id }}">
                                                    {{ trans('user.sidebar.login_activity.logout') }}
                                                </a>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
