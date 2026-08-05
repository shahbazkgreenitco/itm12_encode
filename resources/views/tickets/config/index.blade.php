{{-- @page-meta
{
  "page_no": "TCFGMD-01",
  "file": "index.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans("ticket.ticket_configuration.Ticket_Configuration"))
@section('content')

    {{-- ══════════════════════════════════════════════════
    ① SECTION HEADER
    ══════════════════════════════════════════════════ --}}
    <div id="ticket-config-wrapper">

        <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">           
            <h3 class="h3-text mb-0">
                {{ trans("ticket.ticket_configuration.Ticket_Configuration") }}
            </h3>           
        </div>



        {{-- ══════════════════════════════════════════════════
        ② MAIN CONTENT
        ══════════════════════════════════════════════════ --}}
        <main class="main-content" id="mainContent">
            <div class="tcf-body">

                {{-- ── Tab Navigation ── --}}
                <ul class="nav nav-underline" id="ticket-config-tabs" role="tablist">
                    <li class="nav-item border-0">
                        <a class="nav-link active" id="basic-tab" data-bs-toggle="tab" href="#general" role="tab"
                            aria-controls="general" aria-selected="true">
                            <span class="b1-text fw-normal">{{ trans("ticket.ticket_configuration.general_settings") }}</span>
                        </a>
                    </li>
                    <li class="nav-item border-0">
                        <a class="nav-link" id="notification-tab" data-bs-toggle="tab" href="#notification-section" role="tab"
                            aria-controls="notification" aria-selected="false">
                            <span class="b1-text fw-normal">{{ trans("ticket.ticket_configuration.notification_settings") }}</span>
                        </a>
                    </li>
                    <li class="nav-item border-0">
                        <a class="nav-link" id="auto-updates-tab" data-bs-toggle="tab" href="#auto-updates" role="tab"
                            aria-controls="auto-updates" aria-selected="false">
                            <span class="b1-text fw-normal">{{ trans("ticket.ticket_configuration.auto_updates") }}</span>
                        </a>
                    </li>
                    <li class="nav-item border-0 hide">
                        <a class="nav-link" id="sla-tab" data-bs-toggle="tab" href="#sla" role="tab"
                            aria-controls="sla" aria-selected="false">
                            <span class="b1-text fw-normal">Custom SLA Configuration</span>
                        </a>
                    </li>
                    <li class="nav-item border-0">
                        <a class="nav-link" id="email-tab" data-bs-toggle="tab" href="#email" role="tab"
                            aria-controls="email" aria-selected="false">
                            <span class="b1-text fw-normal">{{ trans("ticket.ticket_configuration.email_to_ticket") }}</span>
                        </a>
                    </li>
                </ul>

                {{-- GENERAL SETTINGS TAB --}}
                <div class="tab-content tabcontent-border pt-3" id="settingsTabContent">

                    <div role="tabpanel" class="tab-pane fade show active" id="general"
                        aria-labelledby="basic-details-tab">
                        <div class="container-fluid py-3">
                            <div class="tab-body-wrapper">
                                <div class="tcf-section" id="settingsmdl">
                                    <div
                                        class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                        <div class="">
                                            <h2 class="s1-text text-white mb-0">{{ trans("ticket.ticket_configuration.general") }}</h2>
                                            <p class="b5-text mb-0" style="color:#7F7F7F">{{ trans("ticket.ticket_configuration.genral_info") }}</p>
                                        </div>                                       
                                    </div>
                                    <div class="tcf-form-body">
                                        <form id="userlist" class="" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                            <div class="row g-3 mb-3">
                                                {{-- Left Column --}}
                                                <div class="col-md-5">
                                                    <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                        <div class="input-group d-flex align-items-center gap-2">
                                                            <label for="send_email" class="form-label fw-medium b1-text mb-0">
                                                               {{ trans("ticket.ticket_configuration.hide_fields_for_user_view") }}
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center flex-wrap gap-4 mb-2 mt-2">
                                                        @foreach (['eu_hide_priority'    => __('ticket.ticket_configuration.Priority'),
                                                                    'eu_hide_assigned_to' => __('ticket.ticket_configuration.Assigned_To'),
                                                                    'eu_hide_expire_at'   => __('ticket.ticket_configuration.Expire_At'),
                                                                    'eu_hide_tat'         => __('ticket.ticket_configuration.TAT'),] as $field => $label)
                                                            <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                                <div class="input-group d-flex align-items-center gap-2 mb-2">
                                                                    <input name="{{ $field }}"  id="{{ $field }}" autocomplete="off" type="checkbox" value="1" class="form-check-input" {{ in_array($label, ['Priority', 'TAT']) ? 'checked' : '' }}>
                                                                    <label for="{{ $field }}" class="form-label fw-normal b1-text mb-0">
                                                                        {{ $label }}
                                                                    </label>

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                {{-- Right Column --}}
                                                <div class="col-md-5">
                                                    <div class="tcf-label mb-2">
                                                        {{ trans('ticket.service_ticket_fields.enable_vip_ticket') }}
                                                    </div>

                                                    <div class="tcf-toggle-wrap">
                                                        <label class="tcf-toggle">
                                                            <input type="checkbox" name="enable_vip_ticket" id="enable_vip_ticket">
                                                            <span class="tcf-toggle-slider"></span>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>

                                            {{-- Row 1: 3-col selects --}}
                                            <div class="tcf-form-row mb-3">
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('content.service_ticket_fields.Mail_Notification') }}</label>
                                                    <select class="tcf-select" name="mail_all_status_changes"
                                                        id="mail_all_status_changes">
                                                        <option value="1">
                                                            {{ trans('content.service_ticket_fields.Ticket_Status_Changes') }}
                                                        </option>
                                                        <option value="0">
                                                            {{ trans('content.service_ticket_fields.Ticket_Resolved_Status') }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('content.service_ticket_fields.TAT_By_Work_Hour') }}</label>
                                                    <select class="tcf-select" name="tat_by_work_hour"
                                                        id="tat_by_work_hour">
                                                        <option value="0">
                                                            {{ trans('content.service_ticket_fields.no') }}</option>
                                                        <option value="1">
                                                            {{ trans('content.service_ticket_fields.yes') }}</option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('content.service_ticket_fields.departments_access') }}</label>
                                                    <select class="tcf-select" name="department_config"
                                                        id="department_config">
                                                        <option value="0">
                                                            {{ trans('content.service_ticket_fields.no') }}</option>
                                                        <option value="1">
                                                            {{ trans('content.service_ticket_fields.yes') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Row 2: 3-col selects --}}
                                            <div class="tcf-form-row mb-3">
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('content.service_ticket_fields.kd_auto_suggestion') }}</label>
                                                    <select class="tcf-select" name="kd_auto_suggestion"
                                                        id="kd_auto_suggestion">
                                                        <option value="0">
                                                            {{ trans('content.service_ticket_fields.disable') }}
                                                        </option>
                                                        <option value="1">
                                                            {{ trans('content.service_ticket_fields.enable') }}</option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('content.service_ticket_fields.auto_archive_ticket_after_days') }}</label>
                                                    <input type="number" id="auto_archive_ticket_after_days"
                                                        name="auto_archive_ticket_after_days" class="form-control"
                                                        min="0"
                                                        placeholder="{{ trans('content.service_ticket_fields.auto_archive_ticket_after_days') }}"
                                                        value="">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('content.service_ticket_fields.checked_cc_checkbox') }}</label>
                                                    <select class="tcf-select" name="checked_cc_checkbox"
                                                        id="checked_cc_checkbox">
                                                        <option value="1">
                                                            {{ trans('content.service_ticket_fields.checked') }}
                                                        </option>
                                                        <option value="0">
                                                            {{ trans('content.service_ticket_fields.unchecked') }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Row 3 --}}
                                            <div class="tcf-form-row mb-3">
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('ticket.service_ticket_fields.auto_archive_user_activity_after_days') }}:</label>
                                                    <input type="number" id="auto_archive_user_activity_after_days"
                                                        name="auto_archive_user_activity_after_days" class="form-control"
                                                        min="0"
                                                        placeholder="{{ trans('ticket.service_ticket_fields.auto_archive_user_activity_after_days') }}"
                                                        value="">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('ticket.service_ticket_fields.ticket_id_initials') }}:</label>

                                                    <select name="ticket_id_initials[]" id="ticket_id_initials"
                                                        autocomplete="off" class="form-control" multiple>
                                                        <option value="com">
                                                            {{ trans('content.service_ticket_fields.company_tag') }}
                                                        </option>
                                                        <option value="dep">
                                                            {{ trans('content.service_ticket_fields.department_tag') }}
                                                        </option>
                                                        <option value="cat">
                                                            {{ trans('content.service_ticket_fields.category_tag') }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label
                                                        class="tcf-label">{{ trans('ticket.service_ticket_fields.ticket_id_initial_separator') }}:</label>
                                                    <input type="text" id="ticket_id_initial_separator"
                                                        name="ticket_id_initial_separator" class="form-control"
                                                        min="0"
                                                        placeholder="{{ trans('ticket.service_ticket_fields.ticket_id_initial_separator') }}"
                                                        value="">
                                                </div>
                                            </div>

                                            <div class="tcf-form-row mb-3">
                                                <!-- Feedback Required -->
                                                    <div class="tcf-form-group">
                                                        <label class="tcf-label">Feedback Required</label>
                                                        <select id="feedback_required" name="feedback_required" class="tcf-select">
                                                            <option value=" ">Select</option>
                                                            <option value="1">Yes</option>
                                                            <option value="0">No</option>
                                                        </select>
                                                    </div>
                                            

                                                <!-- Maximum Rating -->
                                                <div class="d-none" id="feedback_max_rating_div">
                                                    <div class="tcf-form-group">
                                                        <label class="tcf-label">Maximum Rating of Feedback</label>
                                                        <select id="feedback_max_rating" name="feedback_max_rating" class="tcf-select">
                                                            <option value="">Select Rating</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tcf-workhours row mt-3">
                                                <div class="col-md-4 px-3 py-3 border-end">
                                                    <div class="b1-text fw-medium mb-2">
                                                        {{ trans('content.service_ticket_fields.work_hour') }}
                                                    </div>
                                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                                        <div class="amg-form-field">
                                                            <label class="form-label b1-text" for="default_work_start">
                                                                {{ trans('content.service_ticket_fields.start_at') }}
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="text" id="default_work_start"
                                                                    name="default_work_start"
                                                                    class="form-control timepicker"
                                                                    placeholder="{{ trans('content.service_ticket_fields.Select_time') }}"
                                                                    value="" style="width:150px" />
                                                            </div>
                                                        </div>
                                                        <div class="amg-form-field">
                                                            <label class="form-label b1-text" for="default_work_end">
                                                                {{ trans('content.service_ticket_fields.end_at') }}
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="text" id="default_work_end" name="default_work_end"
                                                                    class="form-control timepicker"
                                                                    placeholder="{{ trans('content.service_ticket_fields.Select_time') }}"
                                                                    value="" style="width:150px" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="col-md-8 px-4 py-2 d-flex align-items-end justify-content-start">
                                                    <div class="px-4">
                                                        <div
                                                            class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                            <div class="input-group d-flex align-items-center gap-2 mb-3">

                                                               
                                                                <label class="form-label b1-text fw-bold mb-0">
                                                                    {{ trans("ticket.ticket_configuration.work_days") }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap gap-4 mb-2"
                                                            style="gap:10px;">
                                                            @foreach ([
                                                                        'sun' => __('ticket.ticket_configuration.sunday'),
                                                                        'mon' => __('ticket.ticket_configuration.monday'),
                                                                        'tue' => __('ticket.ticket_configuration.tuesday'),
                                                                        'wed' => __('ticket.ticket_configuration.wednesday'),
                                                                        'thu' => __('ticket.ticket_configuration.thursday'),
                                                                        'fri' => __('ticket.ticket_configuration.friday'),
                                                                        'sat' => __('ticket.ticket_configuration.saturday'),
                                                                    ] as $key => $day)
                                                                @php
                                                                    $field = "default_work_days_$key";
                                                                    $label = $day;
                                                                @endphp

                                                                <div
                                                                    class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                                    <div
                                                                        class="input-group d-flex align-items-center gap-2 mb-2">

                                                                        <input
                                                                            name="default_work_days[{{ $key }}]"
                                                                            id="{{ $field }}" type="checkbox"
                                                                            value="1" class="form-check-input blue">

                                                                        <label for="{{ $field }}"
                                                                            class="form-label fw-normal b1-text mb-0">
                                                                            {{ $label }}
                                                                        </label>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12"><br>
                                                    <button class="amg-btn amg-btn-primary" id="update"
                                                        type="button">
                                                        <span>{{ trans("ticket.ticket_configuration.update") }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>{{-- /form-body --}}
                                </div>{{-- /general section --}}
                                <div class="amg-accordion" id="acc">
                                    <div class="amg-accordion-header justify-content-start gap-3 amg-open" id="acc-header"
                                        onclick="toggleAcc(this)">
                                        <span class="s2-text fw-medium">{{ trans("ticket.ticket_configuration.report_fields") }}</span>
                                        <span class="amg-chevron amg-open" id="chevron">

                                            <svg width="7" height="17" viewBox="0 0 9 17" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z"
                                                    fill="#7F7F7F" />
                                            </svg>

                                        </span>
                                    </div>
                                    {{-- <div class="tcf-accordion-body" id="report_section"> --}}
                                    <div class="amg-accordion-body amg-open" id="report_section">
                                        <div class="mb-2 ps-1">
                                            {{-- <label class="tcf-check-item">
                                                <input type="checkbox" class="blue" id="selectAllFields" <span
                                                    style="font-size:12px;font-weight:500;">Select All</span>
                                            </label> --}}
                                            <label class="amg-select-all-checkbox">
                                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                    <div class="input-group d-flex align-items-center gap-2">
                                                        <input id="selectAllFields" type="checkbox" value="1"
                                                            class="form-check-input">
                                                        <label for="selectAllFields" class="form-label b1-text mb-0"
                                                            style="color:#515151">
                                                            {{ trans('service_ticket.select_all') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <form id="reportfields" class="" method="post">
                                            <div class="tcf-fields-grid">
                                                @foreach ($config->report_fields_list as $key => $field)
                                                    <label class="tcf-check-item field-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="{{ $field['code'] }}" id="{{ $field['code'] }}"
                                                            value="1">
                                                        {{ $field['lbl'] }}
                                                    </label>
                                                @endforeach
                                                <div class="row">
                                                    <div class="col-md-12"><br>
                                                        <button class="amg-btn amg-btn-primary" id="updatelist"
                                                            name="updatelist" type="button">
                                                            <span>{{ trans("ticket.ticket_configuration.update") }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @if (Auth::user()->hasAnyRole(['SuperAdmin']))
                                    {{-- ── Accordion: Departments ── --}}
                                    <div class="tcf-section">
                                        <div class="amg-accordion-header justify-content-start gap-3 amg-open"
                                            id="acc-header" onclick="toggleAcc(this)">
                                            <span class="s2-text fw-medium">{{ trans("ticket.ticket_configuration.department") }}</span>
                                            <span class="amg-chevron amg-open" id="chevron">

                                                <svg width="7" height="17" viewBox="0 0 9 17" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z"
                                                        fill="#7F7F7F" />
                                                </svg>

                                            </span>
                                        </div>
                                        <div class="amg-accordion-body amg-open" id="acc-body">
                                            <div id="body-departments">

                                                {{-- Table toolbar: Select All + Show(10) left | Search + refresh + toggles + Add right --}}
                                                <div class="tcf-table-bar">
                                                    <div class="tcf-table-bar-left">
                                                        
                                                        <div class="col-auto">
                                                            <select
                                                                class="userModulePageLenth deptShowCount amg-table-pagination-dropdown">
                                                                <option value="10" >
                                                                    {{ trans('ticket-types.table.show_10') }}
                                                                </option>
                                                                <option value="25">
                                                                    {{ trans('ticket-types.table.show_25') }}</option>
                                                                <option value="50">
                                                                    {{ trans('ticket-types.table.show_50') }}</option>
                                                                <option value="100">
                                                                    {{ trans('ticket-types.table.show_100') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="tcf-table-bar-right">
                                                        <div class="tcf-table-search">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <circle cx="11" cy="11" r="8" />
                                                                <path d="m21 21-4.35-4.35" />
                                                            </svg>
                                                            <input type="text" class="department-search"
                                                                placeholder="{{ trans("ticket.ticket_configuration.search_department") }}">
                                                        </div>
                                                        <button class="tcf-icon-btn refresh-department" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('ticket-types.refresh') }}" >
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <path
                                                                    d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                                                <path d="M3 3v5h5" />
                                                            </svg>
                                                        </button>
                                                        <label class="tcf-toggle btna-active"
                                                            style="width:38px;height:22px;cursor:pointer;"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ trans('content.service_ticket_fields.enable') }}">
                                                            <input type="checkbox" checked disabled>
                                                            <span class="tcf-toggle-slider"></span>
                                                        </label>
                                                        <label class="tcf-toggle btnd-inactive"
                                                            style="width:38px;height:22px;cursor:pointer;"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ trans('content.service_ticket_fields.disable') }}">
                                                            <input type="checkbox" disabled>
                                                            <span class="tcf-toggle-slider"></span>
                                                        </label>
                                                        <button class="tcf-add-btn add-department" data-bs-toggle="tooltip" data-bs-original-title="{{ trans("ticket.ticket_configuration.add_department") }}">
                                                            <svg width="19" height="19" viewBox="0 0 19 19"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                                                    fill="#7F7F7F" />
                                                            </svg>
                                                            {{ trans("ticket.ticket_configuration.add_department") }}
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Table --}}
                                                <div style="overflow-x:auto;">
                                                    <table class="tcf-table mytable" id="departments">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <div class="checkAll">
                                                                        <input type="checkbox"
                                                                            class="chkParent form-check-input" />&nbsp;&nbsp;
                                                                    </div>
                                                                </th>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Departments') }}
                                                                </h4></th>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Email_Account') }}
                                                                </h4></th>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.th_status') }}
                                                                </h4></th>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.action') }}
                                                                </h4></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Table footer: Page x of y | < Previous 1 2 Next > --}}


                                            </div>{{-- /body-departments --}}
                                        </div>
                                    </div>{{-- /departments accordion --}}
                                @endif
                                {{-- ── Accordion: Ticket Handler Limits ── --}}
                                <form id="handlerLimitsForm" method="post" onsubmit="return false;">
                                    <div class="tcf-section">
                                        <div class="amg-accordion-header justify-content-start gap-3 amg-open"
                                            id="acc-header" onclick="toggleAcc(this)">
                                            <span class="s2-text fw-medium">{{ trans("ticket.ticket_configuration.ticket_handler_limits") }}</span>
                                            <span class="amg-chevron amg-open" id="chevron">

                                                <svg width="7" height="17" viewBox="0 0 9 17" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z"
                                                        fill="#7F7F7F" />
                                                </svg>

                                            </span>
                                        </div>
                                        <div class="amg-accordion-body amg-open" id="acc-body">
                                            <div id="body-handler">

                                                <div class="tcf-user-split">

                                                    {{-- User list --}}
                                                    <div class="">
                                                         <div class="tcf-user-list">
                                                            <div class="tcf-user-list-info-header" >
                                                                <div class="tcf-user-list-head">
                                                                    {{ trans("ticket.ticket_configuration.user_info") }}
                                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <g opacity="0.5">
                                                                        <path d="M9.19254 13.3083C9.25065 13.3663 9.29674 13.4353 9.3282 13.5112C9.35965 13.587 9.37584 13.6684 9.37584 13.7505C9.37584 13.8326 9.35965 13.914 9.3282 13.9898C9.29674 14.0657 9.25065 14.1346 9.19254 14.1927L6.69254 16.6927C6.63449 16.7508 6.56556 16.7969 6.48969 16.8283C6.41381 16.8598 6.33248 16.876 6.25035 16.876C6.16821 16.876 6.08688 16.8598 6.01101 16.8283C5.93514 16.7969 5.86621 16.7508 5.80816 16.6927L3.30816 14.1927C3.25009 14.1346 3.20403 14.0657 3.1726 13.9898C3.14118 13.9139 3.125 13.8326 3.125 13.7505C3.125 13.6684 3.14118 13.5871 3.1726 13.5112C3.20403 13.4353 3.25009 13.3664 3.30816 13.3083C3.42544 13.191 3.5845 13.1251 3.75035 13.1251C3.83247 13.1251 3.91379 13.1413 3.98966 13.1727C4.06553 13.2042 4.13447 13.2502 4.19253 13.3083L5.62535 14.7419V3.75049C5.62535 3.58473 5.6912 3.42576 5.80841 3.30855C5.92562 3.19134 6.08459 3.12549 6.25035 3.12549C6.41611 3.12549 6.57508 3.19134 6.69229 3.30855C6.8095 3.42576 6.87535 3.58473 6.87535 3.75049V14.7419L8.30816 13.3083C8.36621 13.2502 8.43514 13.2041 8.51101 13.1726C8.58688 13.1412 8.66821 13.125 8.75035 13.125C8.83248 13.125 8.91381 13.1412 8.98969 13.1726C9.06556 13.2041 9.13449 13.2502 9.19254 13.3083ZM16.6925 5.8083L14.1925 3.3083C14.1345 3.25019 14.0656 3.20409 13.9897 3.17264C13.9138 3.14119 13.8325 3.125 13.7503 3.125C13.6682 3.125 13.5869 3.14119 13.511 3.17264C13.4351 3.20409 13.3662 3.25019 13.3082 3.3083L10.8082 5.8083C10.6909 5.92558 10.625 6.08464 10.625 6.25049C10.625 6.41634 10.6909 6.5754 10.8082 6.69268C10.9254 6.80995 11.0845 6.87584 11.2503 6.87584C11.4162 6.87584 11.5753 6.80995 11.6925 6.69268L13.1253 5.25909V16.2505C13.1253 16.4163 13.1912 16.5752 13.3084 16.6924C13.4256 16.8096 13.5846 16.8755 13.7503 16.8755C13.9161 16.8755 14.0751 16.8096 14.1923 16.6924C14.3095 16.5752 14.3753 16.4163 14.3753 16.2505V5.25909L15.8082 6.69268C15.9254 6.80995 16.0845 6.87584 16.2503 6.87584C16.4162 6.87584 16.5753 6.80995 16.6925 6.69268C16.8098 6.5754 16.8757 6.41634 16.8757 6.25049C16.8757 6.08464 16.8098 5.92558 16.6925 5.8083Z" fill="black"/>
                                                                        </g>
                                                                    </svg>
                                                                </div>
                                                                <span class="tcf-user-count px-2 mx-1 b5-text" style="color:#7F7F7F" id="priv_tech_user_count"></span>
                                                                <div class="amg-list-searchbar w-90 ms-2 my-3">
                                                                    <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20"
                                                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                    <input type="text"  id="tcf-userprivileges-search-input" class="amg-list-searchbar__input tcf-user-search-input w-100 px-2"
                                                                        placeholder="{{ trans("ticket.ticket_configuration.search_user") }}">
                                                                </div>
                                                            </div>
                                                           
                                                            <div class="users-sidebar">                                                             
                                                                <div id="userLists" class="users-list"></div>
                                                            </div>                                                            
                                                        </div>
                                                        {{-- List pagination --}}
                                                        <div style="padding:8px 12px;display:flex;align-items:center;justify-content:space-between;">
                                                            <div class="tcf-pagination"></div>
                                                        </div>
                                                    </div>

                                                    {{-- User detail --}}
                                                    <div class="tcf-user-detail" id="user_details_panel">
                                                        <div class="tcf-user-detail-head">
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                <div class="tcf-selected-user">{{ trans("ticket.ticket_configuration.selected_user") }}:</div>
                                                            </div>
                                                            <div class="tcf-detail-actions">
                                                                <button class="tcf-icon-btn refresh_tech_prev" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('ticket-types.refresh') }}"
                                                                    >
                                                                    <svg viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2">
                                                                        <path
                                                                            d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                                                        <path d="M3 3v5h5" />
                                                                    </svg>
                                                                </button>
                                                                <button class="tcf-export-btn btn-export-config" data-bs-toggle="tooltip" data-bs-original-title="{{ trans("ticket.ticket_configuration.export_user") }}"
                                                                    id="downloadprevi">
                                                                    <svg viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2">
                                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                        <polyline points="7 10 12 15 17 10" />
                                                                        <line x1="12" y1="15" x2="12"
                                                                            y2="3" />
                                                                    </svg>
                                                                    {{ trans("ticket.ticket_configuration.export_user") }}
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-start gap-3 align-items-center">
                                                            <label class="amg-select-all-checkbox tcf-check-item">
                                                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                                    <div class="input-group d-flex align-items-center gap-2">
                                                                        <input type="checkbox" value="1" class="form-check-input priv_select_all">
                                                                        <label class="form-label b1-text mb-0" style="color:#515151">
                                                                           {{ trans("ticket.ticket_configuration.select_all") }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </label>

                                                            <div class="amg-list-searchbar" style="height: 24px">
                                                                <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                                                </svg>
                                                                <input type="text" id="tcf-user-search-input" class="amg-list-searchbar__input search-prev-dept" placeholder="{{ trans("ticket.ticket_configuration.search_privileges") }}">
                                                            </div>
                                                        </div>


                                                        {{-- Departments permissions --}}
                                                        <div class="tcf-detail-section mt-3">
                                                            <div class="tcf-detail-section-title">{{ trans("ticket.ticket_configuration.department") }}</div>
                                                            <div class="tcf-perm-grid" id="previllege_department_list">
                                                                {{-- @foreach ([['Checklist', false], ['Dev Department', false], ['Development', false], ['Finance', false], ['Product Growth Team', true], ['IT Support', false], ['Product Design - UIUX', false], ['To be Assigned - Sales', false], ['QA-SDET', false], ['QA-SDET-DELHI', false], ['Developement', true], ['testing123', false], ['To be Assigned', false], ['Promp Engineer', false], ['Design', false]] as [$perm, $checked])
                                                                    <label class="tcf-perm-item">
                                                                        <input type="checkbox" {{ $checked ? 'checked' : '' }}>
                                                                        {{ $perm }}
                                                                    </label>
                                                                @endforeach --}}
                                                            </div>
                                                        </div>

                                                        <div class="tcf-detail-section">
                                                            <div class="tcf-detail-section-title">{{ trans("ticket.ticket_configuration.action_control") }}</div>
                                                            <div class="tcf-perm-grid" id="previllege_action_list">
                                                                {{-- @foreach ([['Checklist', false], ['Dev Department', false], ['Development', false], ['Finance', false], ['Product Growth Team', true], ['IT Support', false], ['Product Design - UIUX', false], ['To be Assigned - Sales', false], ['QA-SDET', false], ['QA-SDET-DELHI', false], ['Developement', true], ['testing123', false]] as [$perm, $checked])
                                                                    <label class="tcf-perm-item">
                                                                        <input type="checkbox" {{ $checked ? 'checked' : '' }}>
                                                                        {{ $perm }}
                                                                    </label>
                                                                @endforeach --}}
                                                            </div>
                                                        </div>
                                                        <div class="tcf-detail-section">
                                                            <div class="tcf-detail-section-title">{{ trans("ticket.ticket_configuration.ticket_creation") }}</div>
                                                            <div class="tcf-perm-grid" id="previllege_ticket_creation"></div>
                                                        </div>

                                                        <div class="tcf-detail-section">
                                                            <div class="tcf-detail-section-title">{{ trans("ticket.ticket_configuration.ticket_merge") }}</div>
                                                            <div class="tcf-perm-grid" id="previllege_ticket_merge"></div>
                                                        </div>

                                                        <input type="hidden" name="user_id" id="tcf_selected_user">
                                                        <button class="amg-btn amg-btn-primary" id="handlerLimits"
                                                            name="handlerLimits" type="button">
                                                            <span>{{ trans("ticket.ticket_configuration.save_changes") }}</span>
                                                        </button>
                                                    </div>{{-- /user-detail --}}
                                                    <div id="user_detail_empty" class="d-none text-center py-5">
                                                        <h5 class="mt-2">{{ trans("ticket.ticket_configuration.Details_Not_Found") }}</h5>
                                                        <p>{{ trans("ticket.ticket_configuration.Details_Not_Found_info") }}</p>
                                                    </div>
                                                </div>{{-- /user-split --}}

                                            </div>{{-- /body-handler --}}
                                        </div>
                                    </div>{{-- /handler accordion --}}
                                </form>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="notification-section" aria-labelledby="notification-tab">
                        <div class="container-fluid py-3" id="notify_setting">
                            <div class="tab-body-wrapper">
                                <form action="#" method="POST" id="notification">
                                     @csrf
                                    <div class="tcf-section">
                                        <div
                                            class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                            <div class="">
                                                <h2 class="s1-text text-white mb-0">{{ trans("ticket.ticket_configuration.notification_settings") }}</h2>
                                                <p class="b5-text mb-0" style="color:#7F7F7F">{{ trans("ticket.ticket_configuration.notification_settings_info") }}</p>
                                            </div>
                                        </div>
                                        <div class="tcf-form-body hide">
                                            <div class="tcf-form-row">
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Notify on Ticket Create</label>
                                                    <select class="tcf-select">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Notify on Status Change</label>
                                                    <select class="tcf-select">
                                                        <option >Yes</option>
                                                        <option>No</option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Notify on Assignment</label>
                                                    <select class="tcf-select">
                                                        <option>Yes</option>
                                                        <option>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                <div class="tcf-toggle-wrap">
                                                    <label class="tcf-toggle"><input type="checkbox" checked><span
                                                            class="tcf-toggle-slider"></span></label>
                                                    <span class="tcf-toggle-label">Email Notifications</span>
                                                </div>
                                                <div class="tcf-toggle-wrap">
                                                    <label class="tcf-toggle"><input type="checkbox"><span
                                                            class="tcf-toggle-slider"></span></label>
                                                    <span class="tcf-toggle-label">Push Notifications</span>
                                                </div>
                                                <div class="tcf-toggle-wrap">
                                                    <label class="tcf-toggle"><input type="checkbox" checked><span
                                                            class="tcf-toggle-slider"></span></label>
                                                    <span class="tcf-toggle-label">SMS Notifications</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tcf-form-body">
                                            <div class="tcf-form-row">
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.sla_reminder") }}</label>
                                                    <select class="tcf-select" id="sla_reminder" name="sla_reminder">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                                <div class="amg-form-field amg-form-field-row tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.sla_reminder_hr") }}</label>
                                                    <div class="input-group">
                                                        <input type="number" id="sla_reminder_hr" name="sla_reminder_hr" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.sla_reminder_type") }}</label>
                                                    <select class="tcf-select" id="sla_notification_type" name="sla_notification_type">
                                                        <option value="1">Normal</option>
                                                        <option value="2">Buzzar</option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.add_technician_as_cc") }}</label>
                                                    <select class="tcf-select" id="mark_technician_as_cc_in_ticket_create" name="mark_technician_as_cc_in_ticket_create">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button class="amg-btn amg-btn-primary" id="updateNotification" type="button">
                                                <span>{{ trans("ticket.ticket_configuration.update") }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>    
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="auto-updates" aria-labelledby="auto-updates-tab">
                        <div class="container-fluid py-3" id="autoUpdate_section">
                            <div class="tab-body-wrapper">
                                <form action="#" method="POST" id="auto-updates-section">
                                    @csrf
                                    <div class="tcf-section">
                                        <div
                                            class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                            <div class="">
                                                <h2 class="s1-text text-white mb-0">{{ trans("ticket.ticket_configuration.auto_updates") }}</h2>
                                                <p class="b5-text mb-0" style="color:#7F7F7F">{{ trans("ticket.ticket_configuration.auto_update_info") }}</p>
                                            </div>                                            
                                        </div>
                                        <div class="tcf-form-body hide">
                                            <div class="tcf-form-row">
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Auto Close After (Days)</label>
                                                    <select class="tcf-select">
                                                        <option>7</option>
                                                        <option>14</option>
                                                        <option>30</option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Auto Escalation</label>
                                                    <select class="tcf-select">
                                                        <option>Enable</option>
                                                        <option>Disable</option>
                                                    </select>
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Auto Assign Rule</label>
                                                    <select class="tcf-select">
                                                        <option>Round Robin</option>
                                                        <option>Load Balanced</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tcf-form-body">
                                            <div class="tcf-form-row">
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.mins_before_esclation_to_handler") }}</label>
                                                    <input type="number" name="min_before_escalation_to_handler" class="form-control" id="min_before_escalation_to_handler" min="0">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.mins_before_esclation_to_user") }}</label>
                                                    <input type="number" name="min_before_escalation_to_user" class="form-control" id="min_before_escalation_to_user" min="0">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.mins_before_sla_breach_to_handler") }}</label>
                                                    <input type="number" name="min_before_breached_to_technician" class="form-control" id="min_before_breached_to_technician" min="0">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">{{ trans("ticket.ticket_configuration.mins_before_ticket_close_to_handler") }}</label>
                                                    <input type="number" name="min_before_close_ticket_to_handler" class="form-control" id="min_before_close_ticket_to_handler" min="0">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label"><img src="{{ asset('images/mati.png') }}" alt="icon" style="width:20px; height:20px; margin-right:5px;"> {{ trans("ticket.ticket_configuration.enable_auto_response_on_ticket") }}</label>
                                                    <input type="checkbox" name="auto_response_for_ticket" id="auto_response_for_ticket" value="1" class="form-check-input">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label"><img src="{{ asset('images/mati.png') }}" alt="icon" style="width:20px; height:20px; margin-right:5px;"> {{ trans("ticket.ticket_configuration.enable_auto_resolve_Of_ticket") }}</label>
                                                    <input type="checkbox" name="auto_resolve_ticket" id="auto_resolve_ticket" value="1" class="form-check-input">
                                                </div>
                                            </div>
                                            <button class="amg-btn amg-btn-primary" id="autoUpdate" type="button">
                                                <span>{{ trans("ticket.ticket_configuration.update") }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>    
                            </div>
                        </div>
                        <div class="container-fluid py-3" id="duplicate_section">
                            <div class="tab-body-wrapper" id="duplicateTicket">
                                <form action="#" method="POST" id="dupl_section">
                                    @csrf
                                    <div class="tcf-section">
                                        <div
                                            class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                            <div class="">
                                                <h2 class="s1-text text-white mb-0">Duplicate Ticket</h2>
                                            </div>
                                        </div>
                                        <div class="tcf-form-body">
                                            <div class="tcf-form-row">
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Enable Duplicate Issue</label>
                                                    <input type="checkbox" name="enable_duplicate_issues_check" id="enable_duplicate_issues_check" value="1" class="form-check-input">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Auto Merge Duplicate Issues</label>
                                                    <input type="checkbox" name="auto_merge_duplicate_issues" id="auto_merge_duplicate_issues" value="1" class="form-check-input">
                                                </div>
                                                <div class="tcf-form-group">
                                                    <label class="tcf-label">Allow User to Continue Ticket Creation on Duplication</label>
                                                    <input type="checkbox" name="allow_user_to_continue_ticket_creation_on_duplicate" id="allow_user_to_continue_ticket_creation_on_duplicate" value="1" class="form-check-input">
                                                </div>
                                            </div>
                                            <button class="amg-btn amg-btn-primary" id="duplicateTicketButton" name="duplicateTicketButton" type="button">
                                                <span>{{ trans("ticket.ticket_configuration.update") }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>    
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="sla" aria-labelledby="sla-tab">
                        <div class="container-fluid py-3">
                            <div class="tab-body-wrapper">
                                <div class="tcf-section">
                                    <div
                                        class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                        <div class="">
                                            <h2 class="s1-text text-white mb-0">Custom SLA Configuration</h2>
                                            <p class="b5-text mb-0" style="color:#7F7F7F">Define service level agreements
                                                per priority and
                                                department.</p>
                                        </div>
                                        {{-- <div class="d-flex gap-2">
                                            <button autocomplete="off" type="button" id="btnSubmit"
                                                class="amg-btn amg-btn-outline-white amg-btn-sm min-w-150 text-white">
                                                Discard Changes
                                            </button>

                                            <button autocomplete="off" type="button" id="btnSubmit"
                                                class="amg-btn amg-btn-primary amg-btn-sm min-w-150">
                                                Save Changes
                                            </button>
                                        </div> --}}
                                    </div>
                                    <div class="tcf-form-body">
                                        <div class="tcf-form-row">
                                            <div class="tcf-form-group">
                                                <label class="tcf-label">Critical SLA (hours)</label>
                                                <input type="number" class="tcf-input" value="2">
                                            </div>
                                            <div class="tcf-form-group">
                                                <label class="tcf-label">High SLA (hours)</label>
                                                <input type="number" class="tcf-input" value="8">
                                            </div>
                                            <div class="tcf-form-group">
                                                <label class="tcf-label">Medium SLA (hours)</label>
                                                <input type="number" class="tcf-input" value="24">
                                            </div>
                                        </div>
                                        <div class="tcf-form-row">
                                            <div class="tcf-form-group">
                                                <label class="tcf-label">Low SLA (hours)</label>
                                                <input type="number" class="tcf-input" value="72">
                                            </div>
                                            <div class="tcf-form-group">
                                                <label class="tcf-label">Breach Notification</label>
                                                <select class="tcf-select">
                                                    <option>Enable</option>
                                                    <option>Disable</option>
                                                </select>
                                            </div>
                                            <div class="tcf-form-group">
                                                <label class="tcf-label">SLA Calculation</label>
                                                <select class="tcf-select">
                                                    <option>Business Hours</option>
                                                    <option>Calendar Hours</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="email" aria-labelledby="email-tab">
                        <div class="container-fluid py-3">
                            <div class="tab-body-wrapper">
                                <div class="tcf-section">
                                    <div
                                        class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                        <div class="">
                                            <h2 class="s1-text text-white mb-0">{{trans('ticket.ticket_configuration.email_to_ticket')}}</h2>
                                            <p class="b5-text mb-0" style="color:#7F7F7F">{{trans('ticket.ticket_configuration.email_to_ticket_info')}}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            {{-- <h6 class="tcf-section-title">Email Based Ticket Generation</h6> --}}
                                            <h6 class="tcf-section-title">{{trans('ticket.ticket_configuration.email_based_ticket_generation')}}</h6>
                                        </div>
                                        {{-- <div class="col-md-6 text-end">
                                            <button class="amg-btn amg-btn-primary add-email-to-ticket-account" type="button"
                                                data-bs-toggle="tooltip" data-bs-original-title="Add Email To TIcket Account">
                                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                                <span>Add Account</span>
                                            </button>
                                        </div> --}}
                                    </div>
                                    <div class="tcf-form-body">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <div class="col-auto">
                                                    <select
                                                        class="userModulePageLenth user-list-page-length amg-table-pagination-dropdown">
                                                        <option value="10" selected>
                                                            {{ trans('ticket-types.table.show_10') }}</option>
                                                        <option value="25">{{ trans('ticket-types.table.show_25') }}
                                                        </option>
                                                        <option value="50">{{ trans('ticket-types.table.show_50') }}
                                                        </option>
                                                        <option value="100">{{ trans('ticket-types.table.show_100') }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="flex-grow-1"></div>
                                                <div>
                                                    <div class="amg-list-searchbar">
                                                        <svg class="amg-list-searchbar__icon" width="20"
                                                            height="20" viewBox="0 0 20 20" fill="currentColor"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                                                fill="currentColor"></path>
                                                        </svg>
                                                        <input type="text" name="search"
                                                            class="amg-list-searchbar__input searchbox plain-search"
                                                            placeholder="{{ trans('ticket-types.search_placeholder') }}">
                                                    </div>
                                                </div>

                                                <button class="amg-refresh-btn btn-reload-list btn-reload-list"data-bs-toggle="tooltip" data-bs-original-title="{{ trans('ticket-types.refresh') }}">
                                                    <svg class="amg-refresh-btn__icon" width="20" height="18"
                                                        viewBox="0 0 20 18" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                    <span>{{ trans('ticket-types.refresh') }}</span>
                                                </button>

                                                <button
                                                    class="amg-refresh-btn btn-reload-list btn-reload-list add-email-to-ticket-account"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{trans('ticket.ticket_configuration.add_account')}}">
                                                    <svg width="19" height="19" viewBox="0 0 19 19"
                                                        fill="none">
                                                        <path
                                                            d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                    <span>{{trans('ticket.ticket_configuration.add_account')}}</span>
                                                </button>
                                            </div>
                                            <div class="list-view-panel">
                                                <div class="table-responsive">
                                                    <table id="mytable" class="mytable table tcf-table">
                                                        <thead>
                                                            <tr>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Email_Account') }}
                                                                </th></h4>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Email_Service_Status') }}
                                                                </th></h4>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.email_host') }}
                                                                </th></h4>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Email_Port') }}
                                                                </th></h4>
                                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Actions') }}
                                                                </th></h4>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div id="alias_account_panel" class="list-view-panels">
                                            <div class="card-body">
                                                <div class="panel-heading box-header with-border lite-clr-btn-border">
                                                    <div class="row panel-title box-title">
                                                        <div class="col-md-6 pull-left">
                                                            {{ trans('content.service_ticket_fields.Alias_Accounts') }}
                                                        </div>
                                                        <div class="col-md-6 text-end">
                                                            {{-- <button class="amg-btn amg-btn-primary open-alias-add-modal" type="button"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="Add Email To TIcket Account">
                                                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                                    <path
                                                                        d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                                                        fill="currentColor"></path>
                                                                </svg>
                                                                <span>Add Account</span>
                                                            </button> --}}
                                                        </div>
                                                    </div>
                                                </div><br>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <div class="col-auto">
                                                        <select
                                                            class="userModulePageLenth user-list-page-length-alias amg-table-pagination-dropdown">
                                                            <option value="10" selected>
                                                                {{ trans('ticket-types.table.show_10') }}
                                                            </option>
                                                            <option value="25">
                                                                {{ trans('ticket-types.table.show_25') }}</option>
                                                            <option value="50">
                                                                {{ trans('ticket-types.table.show_50') }}</option>
                                                            <option value="100">
                                                                {{ trans('ticket-types.table.show_100') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="flex-grow-1"></div>
                                                    <div>
                                                        <div class="amg-list-searchbar">
                                                            <svg class="amg-list-searchbar__icon" width="20"
                                                                height="20" viewBox="0 0 20 20" fill="currentColor"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                                                    fill="currentColor"></path>
                                                            </svg>
                                                            <input type="text" name="search"
                                                                class="amg-list-searchbar__input searchbox-alias plain-search"
                                                                placeholder="{{ trans('ticket-types.search_placeholder') }}">
                                                        </div>
                                                    </div>

                                                    <button class="amg-refresh-btn btn-reload-list-alias" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('ticket-types.refresh') }}">
                                                        <svg class="amg-refresh-btn__icon" width="20" height="18"
                                                            viewBox="0 0 20 18" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                                                fill="currentColor"></path>
                                                        </svg>
                                                        <span>{{ trans('ticket-types.refresh') }}</span>
                                                    </button>



                                                    <button
                                                        class="amg-refresh-btn btn-reload-list btn-reload-list open-alias-add-modal"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{trans('ticket.ticket_configuration.add_account')}}">
                                                        <svg width="19" height="19" viewBox="0 0 19 19"
                                                            fill="none">
                                                            <path
                                                                d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                                                fill="currentColor"></path>
                                                        </svg>
                                                        <span>{{trans('ticket.ticket_configuration.add_account')}}</span>
                                                    </button>
                                                </div>
                                                <div class="flex-grow-1"></div>
                                            </div>
                                            <div class="panel-body box-body table-responsive">
                                                <table id="alias_accounts" class="alias_accounts table tcf-table">
                                                    <thead>
                                                        <tr>
                                                            <th><h4 class= "b2-text">{{ trans('content.service_ticket_fields.Alias_Accounts') }}
                                                            </h4></th>
                                                            <th><h4 class= "b2-text">{{ trans('content.service_ticket_fields.Status') }}</h4></th>
                                                            <th><h4 class= "b2-text">{{ trans('content.service_ticket_fields.Departments') }}
                                                            </h4></th>
                                                            <th><h4 class= "b2-text">{{ trans('content.service_ticket_fields.Problem_Category') }}
                                                            </h4></th>
                                                            <th><h4 class= "b2-text">{{ trans('content.service_ticket_fields.sub_category') }}
                                                            </h4></th>
                                                            <th><h4 class= "b2-text">{{ trans('content.service_ticket_fields.Actions') }}</h4></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>






                {{-- AUTO UPDATES TAB --}}
                <div class="tcf-panel" id="tab-auto-updates">
                    <div class="tcf-section">
                        <h6 class="tcf-section-title">Auto Updates</h6>
                        <p class="tcf-section-subtitle">Configure automatic update rules for tickets.</p>
                        <div class="tcf-form-body">
                            <div class="tcf-form-row">
                                <div class="tcf-form-group">
                                    <label class="tcf-label">Auto Close After (Days)</label>
                                    <select class="tcf-select">
                                        <option>7</option>
                                        <option>14</option>
                                        <option>30</option>
                                    </select>
                                </div>
                                <div class="tcf-form-group">
                                    <label class="tcf-label">Auto Escalation</label>
                                    <select class="tcf-select">
                                        <option>Enable</option>
                                        <option>Disable</option>
                                    </select>
                                </div>
                                <div class="tcf-form-group">
                                    <label class="tcf-label">Auto Assign Rule</label>
                                    <select class="tcf-select">
                                        <option>Round Robin</option>
                                        <option>Load Balanced</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SLA CONFIG TAB --}}
                <div class="tcf-panel" id="tab-sla">
                    <div class="tcf-section">
                        <h6 class="tcf-section-title">Custom SLA Configuration</h6>
                        <p class="tcf-section-subtitle">Define service level agreements per priority and department.</p>
                        <div class="tcf-form-body">
                            <div class="tcf-form-row">
                                <div class="tcf-form-group">
                                    <label class="tcf-label">Critical SLA (hours)</label>
                                    <input type="number" class="tcf-input" value="2">
                                </div>
                                <div class="tcf-form-group">
                                    <label class="tcf-label">High SLA (hours)</label>
                                    <input type="number" class="tcf-input" value="8">
                                </div>
                                <div class="tcf-form-group">
                                    <label class="tcf-label">Medium SLA (hours)</label>
                                    <input type="number" class="tcf-input" value="24">
                                </div>
                            </div>
                            <div class="tcf-form-row">
                                <div class="tcf-form-group">
                                    <label class="tcf-label">Low SLA (hours)</label>
                                    <input type="number" class="tcf-input" value="72">
                                </div>
                                <div class="tcf-form-group">
                                    <label class="tcf-label">Breach Notification</label>
                                    <select class="tcf-select">
                                        <option>Enable</option>
                                        <option>Disable</option>
                                    </select>
                                </div>
                                <div class="tcf-form-group">
                                    <label class="tcf-label">SLA Calculation</label>
                                    <select class="tcf-select">
                                        <option>Business Hours</option>
                                        <option>Calendar Hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EMAIL TO TICKET TAB --}}
                <div class="tcf-panel" id="tab-email">
                    <div class="tcf-section">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="tcf-section-title">Email to Ticket</h6>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="amg-btn amg-btn-primary add-email-to-ticket-account" type="button"
                                    data-bs-toggle="tooltip" data-bs-original-title="Add Email To TIcket Account">
                                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <span>Add Account</span>
                                </button>
                            </div>
                        </div>
                        <p class="tcf-section-subtitle">Configure inbound email settings to auto-create tickets. </p>
                        <div class="tcf-form-body">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="col-auto">
                                        <select
                                            class="userModulePageLenth user-list-page-length amg-table-pagination-dropdown">
                                            <option value="10" selected>{{ trans('ticket-types.table.show_10') }}
                                            </option>
                                            <option value="25">{{ trans('ticket-types.table.show_25') }}</option>
                                            <option value="50">{{ trans('ticket-types.table.show_50') }}</option>
                                            <option value="100">{{ trans('ticket-types.table.show_100') }}</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                    <div>
                                        <div class="amg-list-searchbar">
                                            <svg class="amg-list-searchbar__icon" width="20" height="20"
                                                viewBox="0 0 20 20" fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                            <input type="text" name="search"
                                                class="amg-list-searchbar__input searchbox plain-search"
                                                placeholder="{{ trans('ticket-types.search_placeholder') }}">
                                        </div>
                                    </div>

                                    <button class="amg-refresh-btn btn-reload-list btn-reload-list">
                                        <svg class="amg-refresh-btn__icon" width="20" height="18"
                                            viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                                fill="currentColor"></path>
                                        </svg>
                                        <span>{{ trans('ticket-types.refresh') }}</span>
                                    </button>
                                </div>
                                <div class="list-view-panel">
                                    <div class="table-responsive">
                                        <table id="mytable" class="mytable table table-striped">
                                            <thead>
                                                <tr>
                                                    <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Actions') }}</h4></th>
                                                    <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Email_Account') }}</h4></th>
                                                    <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Email_Service_Status') }}
                                                    </h4></th>
                                                    <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.email_host') }}</h4></th>
                                                    <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Email_Port') }}</h4></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div id="alias_account_panel" class="list-view-panels">
                                <div class="card-body">
                                    <div class="panel-heading box-header with-border lite-clr-btn-border">
                                        <div class="row panel-title box-title">
                                            <div class="col-md-6 pull-left">
                                                {{ trans('content.service_ticket_fields.Alias_Accounts') }}
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button class="amg-btn amg-btn-primary open-alias-add-modal"
                                                    type="button" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Add Email To TIcket Account">
                                                    <svg width="19" height="19" viewBox="0 0 19 19"
                                                        fill="none">
                                                        <path
                                                            d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                    <span>Add Account</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="col-auto">
                                            <select
                                                class="userModulePageLenth user-list-page-length-alias amg-table-pagination-dropdown">
                                                <option value="10" selected>{{ trans('ticket-types.table.show_10') }}
                                                </option>
                                                <option value="25">{{ trans('ticket-types.table.show_25') }}</option>
                                                <option value="50">{{ trans('ticket-types.table.show_50') }}</option>
                                                <option value="100">{{ trans('ticket-types.table.show_100') }}</option>
                                            </select>
                                        </div>
                                        <div class="flex-grow-1"></div>
                                        <div>
                                            <div class="amg-list-searchbar">
                                                <svg class="amg-list-searchbar__icon" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                                <input type="text" name="search"
                                                    class="amg-list-searchbar__input searchbox-alias plain-search"
                                                    placeholder="{{ trans('ticket-types.search_placeholder') }}">
                                            </div>
                                        </div>

                                        <button class="amg-refresh-btn btn-reload-list-alias">
                                            <svg class="amg-refresh-btn__icon" width="20" height="18"
                                                viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                            <span>{{ trans('ticket-types.refresh') }}</span>
                                        </button>
                                    </div>
                                    <div class="flex-grow-1"></div>
                                </div>
                                <div class="panel-body box-body table-responsive">
                                    <table id="alias_accounts" class="alias_accounts table table-striped">
                                        <thead>
                                            <tr>
                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Actions') }}</h4></th>
                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Alias_Accounts') }}</h4></th>
                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Status') }}</h4></th>
                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Departments') }}</h4></th>
                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.Problem_Category') }}</h4></th>
                                                <th><h4 class="b2-text">{{ trans('content.service_ticket_fields.sub_category') }}</h4></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('tickets.email_config.modal_html')
            @include('tickets.email_config.modal_alias_account')
            @include('departments.dept-modal')
        </main>
    </div>
    @push('css')
        <link rel="stylesheet" href="{!! CommonHelper::asset('assets/css/ticket_config.css') !!}">
        <link href="{!! CommonHelper::asset('plugins/mdtimepicker/mdtimepicker.css') !!}" rel="stylesheet" />
        <style>
            .select2-container--open {
                z-index: 1060 !important;
            }

            #ticket-config-wrapper .ticket-confg-tab-header {
                background: #001236;
            }

            #ticket-config-wrapper table#departments th {
                background: #F6ECF8;
                height: 30px;
            }

            [data-bs-theme=dark] #ticket-config-wrapper table#departments th {
                background: #1E0505;
                height: 30px;
            }

            #ticket-config-wrapper table#departments {
                border:1px solid #DFDFE1;
                border-radius: 12px;
                overflow: hidden;
            }

            [data-bs-theme=dark] #ticket-config-wrapper table#departments {
                border:1px solid #2A2A2D;
            }
           
            [data-bs-theme=dark] #ticket-config-wrapper table#departments input[type="checkbox"] {
                border: 1px solid #2A2A2D !important;
            }

            /* department table pagination start */
            /* Pagination wrapper */
            #ticket-config-wrapper .dataTables_paginate {
                display: flex;
                align-items: center;
                justify-content: end;
                gap: 12px;
                margin-top: 20px;
            }

            /* All buttons */
            #ticket-config-wrapper .dataTables_paginate .paginate_button {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                min-width: 48px;
                /* height: 48px; */
                padding: 8px 14px;
                border: 1px solid #DFDFE1 !important;
                border-radius: 8px !important;
                background: #EFF2FA !important;
                color: #7F7F7F !important;
                font-size: 12px;
                font-weight: 400;
                text-decoration: none !important;
                cursor: pointer;
                box-shadow: none !important;
            }

             [data-bs-theme=dark] #ticket-config-wrapper .dataTables_paginate .paginate_button {
                border: 1px solid #2A2A2D !important;
                background: #111111 !important;
            }

            /* Current page */
            #ticket-config-wrapper .dataTables_paginate .paginate_button.current {
                background: #EFF2FA !important;
                min-width: 38px;
                color: #7F7F7F !important;
                border-color: #DFDFE1 !important;
            }
             [data-bs-theme=dark]  #ticket-config-wrapper .dataTables_paginate .paginate_button.current {
                background: #111111 !important;
                border-color: #2A2A2D !important;
            }

            /* Hover */
            #ticket-config-wrapper .dataTables_paginate .paginate_button:hover {
                background: #e5e7eb !important;
                color: #555 !important;
                border-color: #cfd4dc !important;
            }

            /* Disabled buttons */
            #ticket-config-wrapper .dataTables_paginate .paginate_button.disabled {
                opacity: 1 !important;
                color: #8b8b8b !important;
                cursor: not-allowed;
            }

            /* Page numbers container */
            #ticket-config-wrapper .dataTables_paginate span {
                display: flex;
                gap: 12px;
            }
            
            #ticket-config-wrapper .dataTables_info {
                float: left;
            }
            #departments_previous::before {
                content: "";
                display: inline-block;
                width: 18px;
                height: 18px;
                margin-right: 4px;
                background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23777777' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 18l-6-6 6-6'/%3E%3C/svg%3E") no-repeat center;
                background-size: contain;
            }

            #departments_next::after {
                content: "";
                display: inline-block;
                width: 18px;
                height: 18px;
                margin-left: 4px;
                background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23777777' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 18l6-6-6-6'/%3E%3C/svg%3E") no-repeat center;
                background-size: contain;
            }
            /* department table pagination end */

            [data-bs-theme=dark] #ticket-config-wrapper .ticket-confg-tab-header {
                background: #1D1D1D;
            }

            #ticket-config-wrapper .amg-form-field .form-check-input {
                height: 14px;
                width: 14px;
                border-radius: 2px;
            }

            [data-bs-theme=dark] #ticket-config-wrapper .amg-form-field .form-check-input {
                border-color: #7F7F7F;
            }

            /* amg-accoedian start */
            .amg-accordion {
                width: 100%;
                /* max-width: 900px; */
                /* border: 1px solid #e5e7eb; */
                /* border-radius: 8px; */
                overflow: hidden;
                background: #fff;
            }

            .amg-accordion-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 20px;
                background: #EFF2FA;
                cursor: pointer;
                user-select: none;
                border-bottom: 1px solid transparent;
                transition: background 0.15s;
            }

            [data-bs-theme=dark] .amg-accordion-header {
                background: #1D1D1D;
            }

            /* .amg-accordion-header:hover {
                                                                                                                                                                        background: #f3f4f6;
                                                                                                                                                                    } */

            .amg-accordion-header.amg-open {
                /* border-bottom-color: #e5e7eb; */
            }

            .amg-accordion-title {
                font-size: 15px;
                font-weight: 600;
                color: #111827;
            }

            .amg-chevron {
                font-size: 18px;
                color: #6b7280;
                transition: transform 0.2s ease;
                display: inline-block;
            }

            .amg-chevron.amg-open {
                transform: rotate(90deg);
            }

            .amg-accordion-body {
                padding: 20px;
                display: none;
            }

            .amg-accordion-body.amg-open {
                display: block;
            }

            [data-bs-theme=dark] .amg-accordion-body.amg-open {
                background: #191919;
            }

            /* ticket handler pagination  start*/
            .tcf-pagination {
                display: flex;
                align-items: center;
                justify-content: end;
                gap: 12px;
                margin-top: 6px;
            }

            .tcf-pg-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;

                min-width: 48px;
                padding: 8px 14px;

                border: 1px solid #DFDFE1;
                border-radius: 6px;

                background: #EFF2FA;
                color: #7F7F7F;

                font-size: 12px;
                font-weight: 400;
                line-height: 1;

                cursor: pointer;
                text-decoration: none;
                box-shadow: none;
                transition: all .2s ease;
            }

            [data-bs-theme=dark] .tcf-pg-btn {
                border-color: #2A2A2D;
                background: #111111;
            }

            .tcf-pg-btn:hover:not(:disabled) {
                background: #e5e7eb;
                color: #555;
                border-color: #cfd4dc;
            }

            .tcf-pg-btn:disabled {
                opacity: 1;
                color: #8b8b8b;
                cursor: not-allowed;
            }
            /* ticket handler pagination  end*/


            /* .amg-select-all-row {
                                                                                                                                                                        margin-bottom: 14px;
                                                                                                                                                                        padding-bottom: 12px;
                                                                                                                                                                        border-bottom: 1px solid #e5e7eb;
                                                                                                                                                                    } */

            .amg-field-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 10px 16px;
            }

            .amg-field-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                color: #374151;
                cursor: pointer;
            }

            .amg-field-item input[type="checkbox"] {
                width: 15px;
                height: 15px;
                cursor: pointer;
                accent-color: #e53935;
                flex-shrink: 0;
            }

            /* amg-accoedian end */

            #ticket-config-wrapper .amg-select-all-checkbox {
                height: 24px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 0px 8px;
                border: 1px solid ##DFDFE1;
                border-radius: 7px;
                background: #fff;
                cursor: pointer;
                user-select: none;
                font-size: 12px;
                font-weight: 400;
                color: #00000070;
                box-shadow: 0 0 0 1px rgba(0, 0, 0, .06);
            }

            [data-bs-theme=dark] #ticket-config-wrapper .amg-select-all-checkbox {
                border: 1px solid #2A2A2D;
                background: none;
            }

            #ticket-config-wrapper .table-responsive .table thead th {
                background: #F6ECF8 !important;
            }

            [data-bs-theme=dark] #ticket-config-wrapper .table-responsive .table thead th {
                background: #1E0505 !important;
            }



            #ticket-config-wrapper .main-content .table-responsive {
                border-radius: 10px;
            }

            #ticket-config-wrapper .table.table-bordered.dataTable .form-check-input,
            .table .form-check-input,
            .table-responsive .form-check-input {
                background: #Fff;
            }

            /* ticket handler limit css start */
            .app-card {
                background: #fff;
                /* border-radius: 16px; */
                /* box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07); */
                /* overflow: hidden; */
                display: flex;
                width: 100%;
                /* max-width: 1100px; */
                min-height: 580px;
            }

            .tcf-user-list-info-header{
                background: #F6ECF8;
            }
            [data-bs-theme=dark] .tcf-user-list-info-header{
                background: #1E0505;
            }
            /* ── Sidebar ── */
            .sidebar {
                border: 1px solid #DFDFE1;
                border-radius: 10px;
                overflow: hidden;
                width: 260px;
                min-width: 260px;
                background: #fff;
                /* border-right: 1px solid #e9eaed; */
                display: flex;
                flex-direction: column;
                padding: 0;
            }

            [data-bs-theme=dark] .sidebar {
                border-color: #2A2A2D;
            }

            .sidebar-header {
                background: #F6ECF8;
                padding: 20px 18px 8px;
                /* border-bottom: 1px solid #e9eaed; */
            }

            [data-bs-theme=dark] .sidebar-header {
                background: #1E0505;
            }

            .sidebar-title {
                font-size: 14px;
                font-weight: 600;
                color: #111;
                display: flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 4px;
            }

            .sidebar-title .bi-arrow-down-up {
                font-size: 13px;
                color: #888;
            }

            .sidebar-sub {
                font-size: 11.5px;
                color: #9095a0;
                margin-bottom: 10px;
            }

            .search-input-wrap {
                position: relative;
            }

            .search-input-wrap .bi-search {
                position: absolute;
                left: 10px;
                top: 50%;
                transform: translateY(-50%);
                color: #aaa;
                font-size: 13px;
            }

            .search-input-wrap input {
                width: 100%;
                padding: 7px 10px 7px 30px;
                border: 1px solid #e2e4e9;
                border-radius: 8px;
                font-size: 13px;
                font-family: inherit;
                outline: none;
                color: #333;
                background: #f8f9fb;
                transition: border-color .2s;
            }

            .search-input-wrap input:focus {
                border-color: #b0b8d0;
                background: #fff;
            }

            .user-list {
                flex: 1;
                overflow-y: auto;
                /* padding: 8px 0; */
            }

            .user-list {
                flex: 1;
                max-height: 455px;
                overflow-y: auto;
                padding: 8px 0;
            }

            [data-bs-theme=dark] .user-list {
                background: #191919;
            }

            .user-list {
                flex: 1;
                max-height: 455px;
                overflow-y: auto;
                padding: 8px 0;

                /* Hide gutter */
                overflow-x: hidden;
                scrollbar-gutter: stable;
            }

            .user-list::-webkit-scrollbar {
                width: 4px;
            }

            .user-list::-webkit-scrollbar-track {
                background: transparent;
                border: none;
                box-shadow: none;
            }

            .user-list::-webkit-scrollbar-thumb {
                background: #DFDFE1;
                border-radius: 10px;
            }

            .user-list::-webkit-scrollbar-thumb:hover {
                background: #DFDFE1;
            }

            /* Firefox */
            .user-list {
                scrollbar-width: thin;
                scrollbar-color: #DFDFE1 transparent;
            }

            [data-bs-theme=dark] .user-list {
                scrollbar-width: thin;
                scrollbar-color: #2A2A2D transparent;
            }


            .user-item {
                display: flex;
                align-items: start;
                gap: 8px;
                padding: 12px 16px;
                cursor: pointer;
                transition: background .15s;
                border-radius: 0;
                border-bottom: 1px solid #F0F0F0;
            }

            [data-bs-theme=dark] .user-item {
                border-color: #2A2A2D;
            }



            .user-item:hover {
                background: #f1f3f8;
            }

            [data-bs-theme=dark] .user-item:hover {
                background: #252525;
            }

            .user-item.active {
                background: #001B51;
            }

            .user-item.active .fw-medium {
                color: #fff !important;
            }

            .user-item.active .fw-light {
                color: #7F7F7F !important;
            }

            .user-avatar {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                object-fit: cover;
                flex-shrink: 0;
            }

            .avatar-placeholder {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background: #d0d4e0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 9px;
                font-weight: 500;
                color: #555;
                flex-shrink: 0;
            }

            .user-item.active .avatar-placeholder {
                background: #3a5099;
                color: #fff;
            }

            .user-name {
                font-size: 13px;
                font-weight: 500;
                color: #222;
                line-height: 1.2;
            }

            .user-handle {
                font-size: 11.5px;
                color: #9095a0;
            }

            .user-item.active .user-name,
            .user-item.active .user-handle {
                color: #fff;
            }

            .sidebar-footer {
                /* border-top: 1px solid #e9eaed; */
                gap: 12px;
                /* padding: 12px 14px; */
                margin-top: 12px;
                margin-left: 12px;
                display: flex;
                align-items: center;
                /* justify-content: space-between; */
            }

            .pagination-btn {
                border: 1px solid #DFDFE1;
                background: #EFF2FA;
                border-radius: 6px;
                padding: 4px 10px;
                font-size: 12.5px;
                font-family: inherit;
                color: #7F7F7F;
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                transition: background .15s, border-color .15s;
            }

            [data-bs-theme=dark] .pagination-btn {
                border: 1px solid #2A2A2D;
                background: #111111;
            }

            .pagination-btn:hover {
                background: #f1f3f8;
                border-color: #ccc;
            }

            /* .pagination-btn:disabled {
                                                                                                                opacity: .45;
                                                                                                                cursor: default;
                                                                                                            } */

            .page-numbers {
                display: flex;
                gap: 8px;
            }

            .page-num {
                width: 28px;
                height: 28px;
                border-radius: 6px;
                border: 1px solid #DFDFE1;
                background: #EFF2FA;
                font-size: 12.5px;
                font-family: inherit;
                color: #7F7F7F;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all .15s;
            }

            [data-bs-theme=dark] .page-num {
                border: 1px solid #2A2A2D;
                background: #111111;
            }

            /* .page-num.active {
                                                                                                                background: #1e2d5e;
                                                                                                                color: #fff;
                                                                                                                border-color: #1e2d5e;
                                                                                                            } */

            .page-num:hover:not(.active) {
                background: #f1f3f8;
            }

            [data-bs-theme=dark] .ticket-handler-sidebar-wrapper {
                background: #191919;
            }

            /* ── Main Content ── */
            .app-card .handler-limits {
                flex: 1;
                display: flex;
                flex-direction: column;
                padding: 24px 28px;
                overflow-y: auto;
            }

            [data-bs-theme=dark] .app-card .handler-limits {
                background: #191919;
            }

            .main-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .selected-user-label {
                font-size: 15px;
                font-weight: 600;
                color: #111;
            }

            .topbar-actions {
                display: flex;
                gap: 8px;
            }

            .icon-btn {
                width: 34px;
                height: 34px;
                border: 1px solid #e2e4e9;
                border-radius: 8px;
                background: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 14px;
                color: #555;
                transition: background .15s;
            }

            .icon-btn:hover {
                background: #f1f3f8;
            }

            .export-btn {
                display: flex;
                align-items: center;
                gap: 6px;
                border: 1px solid #e2e4e9;
                border-radius: 8px;
                background: #fff;
                padding: 6px 14px;
                font-size: 13px;
                font-family: inherit;
                font-weight: 500;
                color: #333;
                cursor: pointer;
                transition: background .15s;
            }

            .export-btn:hover {
                background: #f1f3f8;
            }

            .select-all-wrap {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 18px;
            }

            .select-all-wrap label {
                font-size: 13px;
                color: #444;
                cursor: pointer;
            }

            /* ── Permission Sections ── */
            .perm-section {
                margin-bottom: 22px;
            }

            .perm-section-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 4px;
            }

            .perm-section-header label {
                font-size: 13.5px;
                font-weight: 600;
                color: #111;
                cursor: pointer;
            }

            .perm-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1px 16px;
            }

            .perm-item {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .perm-item label {
                font-size: 13px;
                color: #444;
                cursor: pointer;
                user-select: none;
            }

            /* Custom checkboxes */
            .form-check-input {
                width: 15px;
                height: 15px;
                border: 1.5px solid #c5c9d4;
                border-radius: 3px;
                cursor: pointer;
                flex-shrink: 0;
                accent-color: #e74c3c;
            }

            .form-check-input:checked {
                background-color: #F12F35;
                border-color: #F12F35;
            }

            .form-check-input:focus {
                box-shadow: 0 0 0 2px rgba(231, 76, 60, .15);
            }

            hr.perm-divider {
                border: none;
                border-top: 1px solid #eef0f4;
                margin: 4px 0 18px;
            }

            .single-perm-item {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-top: 8px;
            }

            .single-perm-item label {
                font-size: 13px;
                color: #444;
                cursor: pointer;
            }

            [data-bs-theme=dark] #ticket-config-wrapper .amg-form-field .select2-container--default .select2-selection--single,
            [data-bs-theme="dark"] #ticket-config-wrapper .amg-form-field input[type="text"],
            [data-bs-theme="dark"] #ticket-config-wrapper .amg-form-field input[type="number"] {
                background: #191919;
                border: 1px solid #2A2A2D;
            }

            .amg-form-field-row label.error {
                display: block;
                color: #F12F35 !important;
                font-size: 10px;
                font-weight: 500;
                line-height: 1.4;
                margin: 0px;
            }

            /* ticket handler limit css end */
        </style>
    @endpush
    @push('scripts')
        <script src="{!! CommonHelper::asset('assets/js/ticket_config.js') !!}"></script>
        <script src="{!! CommonHelper::asset('plugins/mdtimepicker/mdtimepicker.js') !!}"></script>
        <script src="{!! CommonHelper::asset('js/tickets/email_config/index.js') !!}"></script>
        <script src="{!! CommonHelper::asset('js/tickets/config.js') !!}"></script>
        <script>
            $(document).ready(function() {
                var config = new Object;
                config.url = new Object;
                config.url.email_config = "{{ url('tickets/email_config_list') }}";
                config.url.alias_account_list = "{{ url('tickets/alias_account_list') }}";
                config.url.departments_with_company = "{{ url('tickets/departments') }}";
                config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
                config.url.add_acc = "{{ url('tickets/ajax-auto-acc-create') }}";
                config.url.delete_acc = "{{ url('tickets/ajax-auto-acc-delete') }}";
                config.url.edit_acc = "{{ url('tickets/ajax-auto-acc-edit') }}";
                config.url.get_acc = "{{ url('tickets/ajax-auto-acc-get') }}";
                config.url.goBack = "{{ url('tickets/config') }}";

                config.url.add_alias_acc = "{{ url('tickets/ajax_add_alias_acc') }}";
                config.url.load_alias_acc = "{{ url('tickets/ajax_load_alias_acc') }}";
                config.url.update_alias_acc = "{{ url('tickets/ajax_update_alias_acc') }}";
                config.url.delete_alias_acc = "{{ url('tickets/ajax_delete_alias_acc') }}";
                config.token = "{{ csrf_token() }}";
                config.url.company_defulte = "{{ url('getCompanyByUserAccess') }}";


                // config for general config tab
                config.url.get_departments = "{{ url('tickets/config/ajax-departments') }}";
                config.url.update_department = "{{ url('tickets/config/update-department') }}";
                config.url.update_departments_bulk = "{{ url('tickets/config/update-departments-bulk') }}";
                config.url.get_users = "{{ url('tickets/config/ajax-user-privileges') }}";
                config.url.update_privilege = "{{ url('tickets/config/update-privilege') }}";
                config.url.update = "{{ url('tickets/update-config') }}";
                config.url.updateNotification = "{{ url('tickets/update-config-notification') }}";
                config.url.update_report_fields = "{{ url('tickets/update-report-fields') }}";
                config.url.email_based_ticketing = "{{ url('tickets/email-based-ticketing') }}";
                config.url.getCompanyByUserAccess = "{{ url('getCompanyByUserAccess') }}";
                config.url.department = "{{ url('tickets/departments') }}";
                config.url.getLocationByQuery = "{{ url('getLocationByQuery') }}";
                config.url.getInternalPlaceByLocation = "{{ url('getInternalPlaceByAjax') }}";
                config.url.departmentSLA = "{{ url('add-department-sla') }}";
                config.url.updateDepartmentSLA = "{{ url('update-department-sla') }}";
                config.url.ajaxDepartmentSLA = "{{ url('ajax-department-sla') }}";
                config.url.getDepartmentSLA = "{{ url('get-department-sla') }}";
                config.url.deleteDepartmentSLA = "{{ url('delete-department-sla') }}";
                config.url.get_user_privelege_data = "{{ url('/ticket/get-privilege') }}";
                config.url.download_url = "{{ url('tickets/config/export-config?search=') }}";
                config.url.autoUpdate = "{{ url('tickets/ticket-auto-update') }}";
                config.url.duplicateTicketUpdate = "{{ url('tickets/duplicate-ticket-update') }}";
                config.url.autoDuplicate = "{{ url('tickets/ticket-auto-duplicate-issues') }}";
                config.url.ajaxIndex = "{{ url('tickets/config/ajaxindex') }}";
                config.company_defulte = {!! json_encode($company_id) !!};
                config.company_user_detail = {!! json_encode($userDetail) !!};
                // department configuration url 
                config.url.add      = "{{ url('department/ajaxadd') }}";
                config.getUserByAjax = "{{ url('getCompanyUsersByQuery') }}";
                config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
                config.permissions = {!! json_encode($permissionArray) !!};
                config.url.getCustomFieldsetByModule = "{{ url('getCustomFieldsetByModule') }}";
                config.translations = {
                    something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
                    serach_option: '{{ trans('content.service_ticket_fields.serach_option') }}',
                    Edit_Config: '{{ trans('content.service_ticket_fields.Edit_Config') }}',
                    Delete_Config: '{{ trans('content.service_ticket_fields.Delete_Config') }}',
                    View_Config: '{{ trans('content.service_ticket_fields.View_Config') }}',
                    Select_Problem_Category: '{{ trans('content.service_ticket_fields.Select_Problem_Category') }}',
                    Select_Sub_Category: '{{ trans('content.service_ticket_fields.Select_Sub_Category') }}',
                    select_department: '{{ trans('content.service_ticket_fields.select_department') }}',
                    Select_Status: '{{ trans('content.service_ticket_fields.Select_Status') }}',
                    download_excel: '{{ trans('content.service_ticket_fields.download_excel') }}',
                    enabled_d: '{{ trans('content.service_ticket_fields.enabled_d') }}',
                    disabled_d: '{{ trans('content.service_ticket_fields.disabled_d') }}',
                    'user_not_found': '{{ trans("ticket.ticket_configuration.No_users_found") }}',
                    'Add_Email_Service_Status' : '{{ trans("ticket.ticket_configuration.Add_Email_Service_Status")}}',
                    'New_Alias_Account' : '{{ trans("ticket.ticket_configuration.New_Alias_Account")  }}',
                    'select_department_info' : '{{trans("ticket.ticket_configuration.select_department_info")}}',
                    'Select_initials' : '{{trans("ticket.ticket_configuration.Select_initials") }}',
                };
                 if (!config.company_defulte ||(Array.isArray(config.company_defulte) && config.company_defulte.length === 0)) 
                    {   sweetAlert("center", "warning", {
                            msg: "{{ trans('ticket.ticket_configuration.company_check_info') }}"
                        });
                        return;
                    }

                new TicketConfig(config);
                new EmailConfig(config);
                new AliasAccount(config);
            });

            // accrodian js start
            let chevronAngle = 90;

            function toggleAcc(header) {
                const body = header.nextElementSibling;
                const chev = header.querySelector('.amg-chevron');
                const isOpen = body.classList.toggle('amg-open');
                header.classList.toggle('amg-open', isOpen);
                chev.style.transform = isOpen ? 'rotate(90deg)' : 'rotate(0deg)';
            }

            function toggleAll(master) {
                document.querySelectorAll('#fields input[type="checkbox"]').forEach(cb => {
                    cb.checked = master.checked;
                });
            }

            

           
            let currentPage = 1;

            
            function selectUser(el, handle) {
                document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
                el.classList.add('active');
                document.getElementById('selectedUserName').textContent = handle;
            }

            function setPage(n) {
                currentPage = n;
                document.querySelectorAll('.page-num').forEach((b, i) => b.classList.toggle('active', i + 1 === n));
                document.getElementById('prevBtn').disabled = n === 1;
            }

            function toggleAll(master) {
                document.querySelectorAll('.main-content .form-check-input').forEach(cb => cb.checked = master.checked);
            }

            function toggleSection(id, master) {
                const section = document.getElementById(id);
                section.querySelectorAll('.form-check-input').forEach(cb => cb.checked = master.checked);
            }

        </script>
    @endpush

@endsection
