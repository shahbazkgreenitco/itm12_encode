@extends('layouts.layout1')
@section('title', trans("header.application_setting_fields.application_settings"))
@section('content')
    <section class="content">
        <div id="ticket-config-wrapper">
            <div class="header-actions-wrapper header-actions-white-wrapper d-flex align-items-center justify-content-between pe-4">           
                {{-- <h3 class="h3-text mb-0">
                    {{ trans("header.application_setting_fields.edit_settings") }}
                </h3>            --}}
                <div class="d-flex align-items-center gap-1">               
                    <button onclick="location.href='{{ url('settings') }}'" class="d-flex gap-3 align-items-center bg-transparent outline-none border-0" data-bs-toggle="tooltip" title="Back">
                        <svg width="16" height="21" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor"></path>
                        </svg>
                    </button>            
                    <h3 class="h3-text mb-0"> {{ trans("header.application_setting_fields.edit_settings") }}</h3>
                </div>
            </div>
            <main class="main-content" id="mainContent">
                <div class="tcf-body">
                    {{-- ── Tab Navigation ── --}}
                    <ul class="nav nav-underline" id="ticket-config-tabs" role="tablist">
                        <li class="nav-item border-0">
                            <a class="nav-link active" id="basic-tab" data-bs-toggle="tab" href="#general" role="tab"
                                aria-controls="general" aria-selected="true">
                                <span class="b1-text fw-normal">{{ trans("header.application_setting_fields.general_settings") }}</span>
                            </a>
                        </li>
                        <li class="nav-item border-0">
                            <a class="nav-link" id="notification-tab" data-bs-toggle="tab" href="#notification-section" role="tab"
                                aria-controls="notification" aria-selected="false">
                                <span class="b1-text fw-normal">{{ trans("header.application_setting_fields.notification_settings") }}</span>
                            </a>
                        </li>
                        <li class="nav-item border-0">
                            <a class="nav-link" id="auto-updates-tab" data-bs-toggle="tab" href="#auto-updates" role="tab"
                                aria-controls="auto-updates" aria-selected="false">
                                <span class="b1-text fw-normal">{{ trans("header.application_setting_fields.Device_Notification_Settings") }}</span>
                            </a>
                        </li>                   
                        <li class="nav-item border-0">
                            <a class="nav-link" id="email-config-tab" data-bs-toggle="tab" href="#email-config" role="tab"
                                aria-controls="email-config" aria-selected="false">
                                <span class="b1-text fw-normal">{{ trans("header.application_setting_fields.email_config_setting") }}</span>
                            </a>
                        </li>                   
                    </ul>      
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade show active" id="general"
                            aria-labelledby="basic-details-tab">
                            <div class="container-fluid py-3">
                                <div class="tab-body-wrapper">
                                    <div class="tcf-section" id="settingsmdl">
                                        <div class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                            <h2 class="s1-text mb-0">{{ trans("header.application_setting_fields.general_settings") }}</h2>
                                        </div>
                                        <form id="SettingForm" class="" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                            <div class="tcf-form-body">

                                                {{-- ========================================================== --}}
                                                {{--  GENERAL FIELDS – two columns, labels on top               --}}
                                                {{-- ========================================================== --}}

                                                {{-- Row 1: Site Name + Branding --}}
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="site_name" class="tcf-label mandatory">{{ trans("header.application_setting_fields.site_name") }}</label>
                                                        <div class="input-group">
                                                            <input type="text" name="site_name" value="{{ optional($settings)->site_name ?? '' }}" id="site_name" class="form-control input-sm" placeholder="Site Name" />
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="brand" class="tcf-label">{{ trans("header.application_setting_fields.branding") }}</label>
                                                        <div class="input-group">
                                                            <select class="form-control select2" id="brand" name="brand">
                                                                <option value="1" @if((optional($settings)->brand ?? 1) == 1) selected @endif>Text</option>
                                                                <option value="2" @if((optional($settings)->brand ?? 1) == 2) selected @endif>Logo</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 2: Default Currency + Accessory Checkin --}}
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="default_currency" class="tcf-label mandatory">{{ trans("header.application_setting_fields.default_currency") }}</label>
                                                        <div class="input-group">
                                                            <select name="default_currency" id="default_currency" class="form-control select2">
                                                                <option value="">Select Currency</option>
                                                                @foreach($currencies as $key => $value)
                                                                    <option value="{{ $key }}" @if((optional($settings)->default_currency ?? '') == $key) selected @endif>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="accessory_block_checkin" class="tcf-label">{{ trans("header.application_setting_fields.accessory_checkin") }}</label>
                                                        <div class="input-group">
                                                            <select name="accessory_block_checkin" id="accessory_block_checkin" class="form-control select2">
                                                                <option value="0" @if((optional($settings)->accessory_block_checkin ?? 0) == 0) selected @endif>Allow to Check In</option>
                                                                <option value="1" @if((optional($settings)->accessory_block_checkin ?? 0) == 1) selected @endif>Do not allow to Check In</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 3: Logo + Header Color (SuperUser) --}}
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="logo" class="tcf-label">{{ trans("header.application_setting_fields.Llogo") }}</label>
                                                        <div class="input-group">
                                                            <input name="logo" type="file" id="logo" value="{{ optional($settings)->logo ?? '' }}" class="form-control form-input equal-width-text-input">
                                                        </div>
                                                        @if(!empty($settings->logo))
                                                            <div class="mb-2 mt-2">
                                                                <img src="{{ asset('uploads/settings/'.$settings->logo_thumbnail) }}"
                                                                    alt="Logo"
                                                                    style="max-height:80px; border:1px solid #ddd; padding:5px;">
                                                            </div>
                                                        @endif
                                                        <label class="tcf-label cursor-pointer">
                                                            <input name="clear_logo" id="clear_logo" class="form-check-input" type="checkbox" value="1" @if((optional($settings)->clear_logo ?? 0) == 1) checked @endif> Remove
                                                        </label>
                                                    </div>
                                                    {{-- @if( Auth::user()->isSuperUser() )
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="header_color" class="tcf-label">Header Color</label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input name="header_color" type="color" value="{{ optional($settings)->header_color ?? '#000000' }}" id="header_color" class="form-control">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select name="css_theme" id="css_theme" class="form-control input-sm">
                                                                    @foreach($colors ?? [] as $color)
                                                                        <option value="{{ $color->id }}" @if((optional($settings)->css_theme ?? 0) == $color->id) selected @endif>
                                                                            {{ $color->color_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif --}}
                                                </div>

                                                {{-- Row 4: Alerts Enabled (toggle) + Alert Email --}}
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label class="tcf-label">{{ trans("header.application_setting_fields.send_alerts_to") }} {{ trans("header.application_setting_fields.enabled_disabled") }}</label>
                                                        <div class="input-group">
                                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                                <div class="tcf-toggle-wrap">
                                                                    <label class="tcf-toggle">
                                                                        <input name="alerts_enabled" id="alerts_enabled" type="checkbox" value="1" @if((optional($settings)->alerts_enabled ?? 0) == 1) checked @endif>
                                                                        <span class="tcf-toggle-slider"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6 alerts_type hide">
                                                        <label for="alerts_type" class="tcf-label">{{ trans("settings.notification.alert_type") }}</label>
                                                        <div class="input-group">
                                                            <select name="alerts_type" id="alerts_type" class="form-control select2">
                                                                <option value="null" @if((optional($settings)->alerts_type ?? 'null') == 'null') selected @endif>{{ trans("settings.notification.select_type") }}</option>
                                                                <option value="1" @if((optional($settings)->alerts_type ?? '') == 1) selected @endif>{{trans('settings.notification.device_role_based')}}</option>
                                                                <option value="2" @if((optional($settings)->alerts_type ?? '') == 2) selected @endif>{{trans('settings.notification.device_user_mail')}}</option>
                                                                <option value="3" @if((optional($settings)->alerts_type ?? '') == 3) selected @endif>{{trans('settings.notification.device_read_permission')}}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 5: Alert Role (hidden) + Alert User Emails (hidden) --}}
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6 form-group alert_value hide">
                                                        <label for="alert_value" class="tcf-label">{{ trans("settings.notification.Role") }}</label>
                                                        <div class="input-group">
                                                            <select name="alert_value[]" id="alert_value" class="form-control" multiple></select>
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6 form-group alert_users_email hide mt-3">
                                                        <label for="alert_email" class="tcf-label">{{ trans("settings.notification.alert_email") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control input-sm alert_email" placeholder="admin@yourcompany.com" name="alert_email" type="text" value="{{ optional($settings)->alert_email ?? '' }}" id="alert_email">
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6 form-group alert_users_email hide">
                                                        <label for="alert_users_email" class="tcf-label">{{ trans("settings.notification.user_emails") }}</label>
                                                        <div class="input-group">
                                                            <select name="alert_users_email[]" id="alert_users_email" class="form-control select2 bg-unset" placeholder="{{trans('header.application_setting_fields.enter_email_comma')}}" multiple>
                                                                @php
                                                                    $alertEmails = optional($settings)->alerts_users_email ?? '';
                                                                    $alertEmailsArray = $alertEmails ? explode(',', $alertEmails) : [];
                                                                @endphp
                                                                @foreach($alertEmailsArray as $option)
                                                                    <option value="{{ trim($option) }}" selected>{{ trim($option) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 6: Terms & Conditions (toggle) + Select Term --}}
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label class="tcf-label mandatory">{{ trans("settings.notification.Terms_and_Conditions") }} {{ trans("header.application_setting_fields.enable_disable") }}</label>
                                                        <div class="input-group">
                                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                                <div class="tcf-toggle-wrap">
                                                                    <label class="tcf-toggle">
                                                                        <input name="tnc_accept" type="checkbox" value="1" id="tnc_accept" @if((optional($settings)->tnc_accept ?? 0) == 1) checked @endif>
                                                                        <span class="tcf-toggle-slider"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="history_id" class="tcf-label">{{ trans("header.application_setting_fields.select_term_condition") }}</label>
                                                        <div class="input-group">
                                                            <select name="history_id" id="history_id" class="form-control">
                                                                @foreach($termsConditionDropdown ?? [] as $value)
                                                                    <option value="{{ $value->id }}" @if((optional($termsConditionObj)->history_id ?? 0) == $value->id) selected @endif>{{ $value->title }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                
                                            </div> {{-- end tcf-form-body --}}
                                            {{-- ========================================================== --}}
                                                {{--  2FA ACCORDION – labels on top, two columns                --}}
                                                {{-- ========================================================== --}}
                                                <div class="amg-accordion" id="acc-2fa">
                                                    <div class="amg-accordion-header justify-content-start gap-3 amg-open" onclick="toggleAcc(this)">
                                                        <span class="s2-text fw-medium">{{ trans("header.application_setting_fields.2fa_authentication_settings") }}</span>
                                                        <span class="amg-chevron amg-open">
                                                            <svg width="7" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z" fill="#7F7F7F"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="amg-accordion-body amg-open">
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("header.application_setting_fields.enable_2fa") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="enable_2fa_authentication" type="checkbox" value="1" id="enable_2fa_authentication" @if((optional($settings)->enable_2fa_authentication ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="enable_2fa_authentication_with" class="tcf-label">{{ trans("header.application_setting_fields.enable_2fa_with") }}</label>
                                                                <div class="input-group">
                                                                    <select multiple id="enable_2fa_authentication_with" name="enable_2fa_authentication_with[]" class="form-control">
                                                                        <option {{ in_array(1, $enable_2fa_authentication_with ?? []) ? 'selected' : '' }} value="1">Email</option>
                                                                        <option {{ in_array(2, $enable_2fa_authentication_with ?? []) ? 'selected' : '' }} value="2">Phone</option>
                                                                        <option {{ in_array(3, $enable_2fa_authentication_with ?? []) ? 'selected' : '' }} value="3">App</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ========================================================== --}}
                                                {{--  DEVICE SETTINGS ACCORDION – labels on top, two columns    --}}
                                                {{-- ========================================================== --}}
                                                <div class="amg-accordion" id="acc-device">
                                                    <div class="amg-accordion-header justify-content-start gap-3 amg-open" onclick="toggleAcc(this)">
                                                        <span class="s2-text fw-medium">{{ trans("header.application_setting_fields.device_settings_Optional") }}</span>
                                                        <span class="amg-chevron amg-open">
                                                            <svg width="7" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z" fill="#7F7F7F"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="amg-accordion-body amg-open">

                                                        {{-- Auto‑increment (toggle) + Prefix --}}
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("header.application_setting_fields.asset_IDs") }} ({{ trans("header.application_setting_fields.generate_auto_incrementing_assetIDs") }})</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="auto_increment_assets" type="checkbox" value="1" id="auto_increment_assets" @if((optional($settings)->auto_increment_assets ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="auto_increment_prefix" class="tcf-label mandatory">{{ trans("header.application_setting_fields.prefix_optional") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" name="auto_increment_prefix" type="text" value="{{ optional($settings)->auto_increment_prefix ?? '' }}" id="auto_increment_prefix">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Asset Tag + Separator --}}
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="asset_tag_type" class="tcf-label">{{ trans("settings.notification.Asset_Tag") }}</label>
                                                                <div class="input-group">
                                                                    <select name="asset_tag_type" class="form-control" id="asset_tag_type">
                                                                        <option value="0" @if((optional($assetTagData)['asset_tag_type'] ?? 0) == 0) selected @endif>{{ trans("settings.notification.select_type") }}</option>
                                                                        <option value="1" @if((optional($assetTagData)['asset_tag_type'] ?? 0) == 1) selected @endif>Prefix</option>
                                                                        <option value="2" @if((optional($assetTagData)['asset_tag_type'] ?? 0) == 2) selected @endif>Hostname</option>
                                                                        <option value="3" @if((optional($assetTagData)['asset_tag_type'] ?? 0) == 3) selected @endif>Custom Tag</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="asset_tag_separator" class="tcf-label">{{ trans("settings.notification.Asset_Tag_Separator") }}</label>
                                                                <div class="input-group">
                                                                    <select name="asset_tag_saperator" class="form-control" id="asset_tag_separator">
                                                                        <option value="0" @if((optional($assetTagData)['asset_tag_saperator'] ?? 0) == 0) selected @endif>{{ trans("settings.notification.select_type") }}</option>
                                                                        <option value="1" @if((optional($assetTagData)['asset_tag_saperator'] ?? 0) == 1) selected @endif>Hyphen (-)</option>
                                                                        <option value="2" @if((optional($assetTagData)['asset_tag_saperator'] ?? 0) == 2) selected @endif>Underscore (_)</option>
                                                                        <option value="3" @if((optional($assetTagData)['asset_tag_saperator'] ?? 0) == 3) selected @endif>Slash (/)</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Custom Tag Options (full width) --}}
                                                        <div class="row CustomTag mb-2 hide">
                                                            <div class="amg-form-field amg-form-field-row col-md-12">
                                                                <label class="tcf-label">{{ trans("settings.notification.Custom_Tag_Options") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex gap-3 flex-wrap">
                                                                        <label class="tcf-label cursor-pointer"><input name="asset_tag_location" type="checkbox" class="form-check-input" value="1" id="asset_tag_location" @if((optional($assetTagData)['asset_tag_location'] ?? 0) == 1) checked @endif> Location</label>
                                                                        <label class="tcf-label cursor-pointer"><input name="asset_tag_department" type="checkbox" class="form-check-input" value="1" id="asset_tag_department" @if((optional($assetTagData)['asset_tag_department'] ?? 0) == 1) checked @endif> Department</label>
                                                                        <label class="tcf-label cursor-pointer"><input name="asset_tag_pur_date" type="checkbox" class="form-check-input" value="1" id="asset_tag_pur_date" @if((optional($assetTagData)['asset_tag_pur_date'] ?? 0) == 1) checked @endif> Purchase Date</label>
                                                                        <label class="tcf-label cursor-pointer"><input name="asset_tag_year" type="checkbox" class="form-check-input" value="1" id="asset_tag_year" @if((optional($assetTagData)['asset_tag_year'] ?? 0) == 1) checked @endif> Year</label>
                                                                        <label class="tcf-label cursor-pointer"><input name="asset_tag_month" type="checkbox" class="form-check-input" value="1" id="asset_tag_month" @if((optional($assetTagData)['asset_tag_month'] ?? 0) == 1) checked @endif> Month</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Custom Fieldsets – 2 columns --}}
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="custom_fieldset_id" class="tcf-label">{{ trans("header.application_setting_fields.custom_fieldset_id") }}</label>
                                                                <div class="input-group">
                                                                    <select name="custom_fieldset_id" class="form-control" id="custom_fieldset_id">
                                                                        <option value="">{{ trans("settings.notification.Select_Custom_Fieldset") }}</option>
                                                                        @foreach($customFieldsetForDevice ?? [] as $fieldset)
                                                                            <option value="{{ $fieldset->id }}" @if((optional($settings)->custom_fieldset_id ?? 0) == $fieldset->id) selected @endif>{{ $fieldset->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="licence_custom_fieldset_id" class="tcf-label">{{ trans("header.application_setting_fields.licence_custom_fieldset_id") }}</label>
                                                                <div class="input-group">
                                                                    <select name="licence_custom_fieldset_id" class="form-control" id="licence_custom_fieldset_id">
                                                                        <option value="">{{ trans("settings.notification.Select_Custom_Fieldset") }}</option>
                                                                        @foreach($customFieldsetForLicense ?? [] as $fieldset)
                                                                            <option value="{{ $fieldset->id }}" @if((optional($settings)->licence_custom_fieldset_id ?? 0) == $fieldset->id) selected @endif>{{ $fieldset->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="accessories_custom_fieldset_id" class="tcf-label">{{ trans("header.application_setting_fields.accessories_custom_fieldset_id") }}</label>
                                                                <div class="input-group">
                                                                    <select name="accessories_custom_fieldset_id" class="form-control" id="accessories_custom_fieldset_id">
                                                                        <option value="">{{ trans("settings.notification.Select_Custom_Fieldset") }}</option>
                                                                        @foreach($customFieldsetForAccessories ?? [] as $fieldset)
                                                                            <option value="{{ $fieldset->id }}" @if((optional($settings)->accessories_custom_fieldset_id ?? 0) == $fieldset->id) selected @endif>{{ $fieldset->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="component_custom_fieldset_id" class="tcf-label">{{ trans("header.application_setting_fields.component_custom_fieldset_id") }}</label>
                                                                <div class="input-group">
                                                                    <select name="component_custom_fieldset_id" class="form-control" id="component_custom_fieldset_id">
                                                                        <option value="">{{ trans("settings.notification.Select_Custom_Fieldset") }}</option>
                                                                        @foreach($customFieldsetForComponent ?? [] as $fieldset)
                                                                            <option value="{{ $fieldset->id }}" @if((optional($settings)->component_custom_fieldset_id ?? 0) == $fieldset->id) selected @endif>{{ $fieldset->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="consumable_custom_fieldset_id" class="tcf-label">{{ trans("header.application_setting_fields.consumable_custom_fieldset_id") }}</label>
                                                                <div class="input-group">
                                                                    <select name="consumable_custom_fieldset_id" class="form-control" id="consumable_custom_fieldset_id">
                                                                        <option value="">{{ trans("settings.notification.Select_Custom_Fieldset") }}</option>
                                                                        @foreach($customFieldsetForConsumable ?? [] as $fieldset)
                                                                            <option value="{{ $fieldset->id }}" @if((optional($settings)->consumable_custom_fieldset_id ?? 0) == $fieldset->id) selected @endif>{{ $fieldset->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="sez_custom_fieldset_id" class="tcf-label">{{ trans("header.application_setting_fields.sez_custom_fieldset_id") }}</label>
                                                                <div class="input-group">
                                                                    <select name="sez_custom_fieldset_id" class="form-control" id="sez_custom_fieldset_id">
                                                                        <option value="">{{ trans("settings.notification.Select_Custom_Fieldset") }}</option>
                                                                        @foreach($customFieldsetForSez ?? [] as $fieldset)
                                                                            <option value="{{ $fieldset->id }}" @if((optional($settings)->sez_custom_fieldset_id ?? 0) == $fieldset->id) selected @endif>{{ $fieldset->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Block Device Movement + NI Not Detected --}}
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="block_device_movement_intimation_users" class="tcf-label">{{ trans("header.application_setting_fields.block_device_movement_intimation_users") }}</label>
                                                                <div class="input-group">
                                                                    <select name="block_device_movement_intimation_users[]" class="form-control" id="block_device_movement_intimation_users" multiple></select>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ni_not_detected" class="tcf-label mandatory">{{ trans("settings.notification.Days_for_NI_Device_not_Detected") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="Enter Days" name="ni_not_detected" type="number" id="ni_not_detected" value="{{ optional($settings)->ni_not_detected ?? '' }}" min="1">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Location / Department / Cost Earned (toggles) --}}
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("settings.notification.Location_Configuration") }} {{ trans("header.application_setting_fields.enabled_disabled") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="location_config" type="checkbox" value="1" id="location_config" @if((optional($settings)->location_config ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("settings.notification.Department_Configuration") }} {{ trans("header.application_setting_fields.enable_disable") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="department_config" type="checkbox" value="1" id="department_config" @if((optional($settings)->department_config ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("settings.notification.Cost_Earned") }} {{ trans("header.application_setting_fields.enable_disable") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="cost_earned" type="checkbox" value="1" id="cost_earned" @if((optional($settings)->cost_earned ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6 device_report_email">
                                                                <label for="device_report_email" class="tcf-label">{{ trans("settings.notification.Device_Report_Email") }}</label>
                                                                <div class="input-group">
                                                                    <select name="device_report_email[]" id="device_report_email" class="form-control" placeholder="{{trans('header.application_setting_fields.enter_email_comma')}}" multiple>
                                                                        @php
                                                                            $reportEmails = optional($ni_not_detected_set)->device_report_email ?? '';
                                                                            $reportEmailsArray = $reportEmails ? explode(',', $reportEmails) : [];
                                                                        @endphp
                                                                        @foreach($reportEmailsArray as $option)
                                                                            <option value="{{ trim($option) }}" selected>{{ trim($option) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Barcode Settings --}}
                                                        <h4 class="box-title mt-3">{{ trans("header.application_setting_fields.barcode_Settings") }}</h4>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("header.application_setting_fields.display_QR_codes") }} {{ trans("header.application_setting_fields.enable_disable") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="qr_code" type="checkbox" value="1" id="qr_code" @if((optional($settings)->qr_code ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="barcode_type" class="tcf-label">{{ trans("header.application_setting_fields.barcode_type") }}</label>
                                                                <div class="input-group">
                                                                    <select name="barcode_type" class="form-control" id="barcode_type">
                                                                        <option value="">{{ trans("settings.notification.Select_Barcode_Type") }}</option>
                                                                        <option value="C128" @if((optional($settings)->barcode_type ?? '') == 'C128') selected @endif>C128</option>
                                                                        <option value="DATAMATRIX" @if((optional($settings)->barcode_type ?? '') == 'DATAMATRIX') selected @endif>DATAMATRIX</option>
                                                                        <option value="PDF417" @if((optional($settings)->barcode_type ?? '') == 'PDF417') selected @endif>PDF417</option>
                                                                        <option value="QRCODE" @if((optional($settings)->barcode_type ?? '') == 'QRCODE') selected @endif>QRCODE</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="qr_text" class="tcf-label mandatory">{{ trans("header.application_setting_fields.QR_code_text") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="Property of Your Company" name="qr_text" type="text" id="qr_text" value="{{ optional($settings)->qr_text ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- EULA (full width) --}}
                                                        <h4 class="box-title mt-3">{{ trans("header.application_setting_fields.EULA_settings") }}</h4>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-12">
                                                                <label for="default_eula_text" class="tcf-label">{{ trans("header.application_setting_fields.default_EULA") }}</label>
                                                            
                                                                    <textarea class="form-control" placeholder="Add your default EULA text" name="default_eula_text" cols="50" rows="5" id="default_eula_text">{{ optional($settings)->default_eula_text ?? '' }}</textarea>
                                                            </div>
                                                        </div>

                                                    </div> {{-- end amg-accordion-body --}}
                                                </div> {{-- end amg-accordion device --}}

                                                {{-- ========================================================== --}}
                                                {{--  LDAP SETTINGS ACCORDION – labels on top, two columns      --}}
                                                {{-- ========================================================== --}}
                                                <div class="amg-accordion" id="acc-ldap">
                                                    <div class="amg-accordion-header justify-content-start gap-3 amg-open" onclick="toggleAcc(this)">
                                                        <span class="s2-text fw-medium">{{ trans("header.application_setting_fields.LDAP_settings_Optional") }}</span>
                                                        <span class="amg-chevron amg-open">
                                                            <svg width="7" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z" fill="#7F7F7F"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="amg-accordion-body amg-open">
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("header.application_setting_fields.LDAP_integration") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="ldap_enabled" type="checkbox" value="1" id="ldap_enabled" @if((optional($settings)->ldap_enabled ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                        <span class="tcf-label">{{ trans("header.application_setting_fields.LDAP_enabled") }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_server" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_server") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control input-sm" placeholder="http://ldap.example.com" name="ldap_server" type="text" value="{{ optional($settings)->ldap_server ?? '' }}" id="ldap_server">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label class="tcf-label">{{ trans("header.application_setting_fields.LDAP_SSL_certificate_validation") }}</label>
                                                                <div class="input-group">
                                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                                        <div class="tcf-toggle-wrap">
                                                                            <label class="tcf-toggle">
                                                                                <input name="ldap_server_cert_ignore" type="checkbox" value="1" id="ldap_server_cert_ignore" @if((optional($settings)->ldap_server_cert_ignore ?? 0) == 1) checked @endif>
                                                                                <span class="tcf-toggle-slider"></span>
                                                                            </label>
                                                                        </div>
                                                                        <span class="tcf-label">{{ trans("header.application_setting_fields.allow_invalid_SSL_certificate") }}</span>
                                                                    </div>
                                                                </div>
                                                                <span class="tcf-label">{{ trans("header.application_setting_fields.SSL_certificate") }}</span>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_uname" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_bind_username") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="bindusername" name="ldap_uname" type="text" value="{{ optional($settings)->ldap_uname ?? '' }}" id="ldap_uname">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_pword" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_bind_password") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="binduserpassword" name="ldap_pword" type="password" value="{{ optional($settings)->ldap_pword ?? '' }}" id="ldap_pword">
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_basedn" class="tcf-label mandatory">{{ trans("header.application_setting_fields.base_bind_DN") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="cn=users/authorized,dc=example,dc=com" name="ldap_basedn" type="text" value="{{ optional($settings)->ldap_basedn ?? '' }}" id="ldap_basedn">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_filter" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_filter") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="cn=users/authorized,dc=example,dc=com" name="ldap_filter" type="text" value="{{ optional($settings)->ldap_filter ?? '' }}" id="ldap_filter">
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_username_field" class="tcf-label mandatory">{{ trans("header.application_setting_fields.username_field") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="samaccountname" name="ldap_username_field" type="text" value="{{ optional($settings)->ldap_username_field ?? '' }}" id="ldap_username_field">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_lname_field" class="tcf-label mandatory">{{ trans("header.application_setting_fields.last_name") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="sn" name="ldap_lname_field" type="text" value="{{ optional($settings)->ldap_lname_field ?? '' }}" id="ldap_lname_field">
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_fname_field" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_first_name") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="givenname" name="ldap_fname_field" type="text" value="{{ optional($settings)->ldap_fname_field ?? '' }}" id="ldap_fname_field">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_auth_filter_query" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_authentication_query") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="&quot;uid=&quot;" name="ldap_auth_filter_query" type="text" value="{{ optional($settings)->ldap_auth_filter_query ?? '' }}" id="ldap_auth_filter_query">
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_version" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_version") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="3" name="ldap_version" type="text" value="{{ optional($settings)->ldap_version ?? '' }}" id="ldap_version">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_active_flag" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_active_flag") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="" name="ldap_active_flag" type="text" value="{{ optional($settings)->ldap_active_flag ?? '' }}" id="ldap_active_flag">
                                                                </div>
                                                            </div>
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_emp_num" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_employee_number") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="" name="ldap_emp_num" type="text" value="{{ optional($settings)->ldap_emp_num ?? '' }}" id="ldap_emp_num">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="amg-form-field amg-form-field-row col-md-6">
                                                                <label for="ldap_email" class="tcf-label mandatory">{{ trans("header.application_setting_fields.LDAP_email") }}</label>
                                                                <div class="input-group">
                                                                    <input class="form-control" placeholder="" name="ldap_email" type="text" value="{{ optional($settings)->ldap_email ?? '' }}" id="ldap_email">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> {{-- end amg-accordion-body --}}
                                                </div> {{-- end amg-accordion ldap --}}

                                                {{-- ========================================================== --}}
                                                {{--  SUBMIT BUTTON                                              --}}
                                                {{-- ========================================================== --}}
                                                <div class="row ms-2 mb-2">
                                                    <div class="col-md-12 text-start">
                                                        <br>
                                                        <button class="amg-btn amg-btn-primary" id="btnSubmit2" type="button">
                                                            <span>{{ trans("header.application_setting_fields.update_settings") }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                        </form>
                                    </div> {{-- end tcf-section --}}
                                </div> {{-- end tab-body-wrapper --}}
                            </div> {{-- end container-fluid --}}
                        </div> {{-- end tab-pane --}}      

                        {{-- ==================== NOTIFICATION TAB ==================== --}}
                        <div role="tabpanel" class="tab-pane fade" id="notification-section"
                            aria-labelledby="notification-tab">
                            <div class="container-fluid py-3">
                                <div class="tab-body-wrapper">
                                    <div class="tcf-section" id="settingsmdl2">
                                        <div class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                            <h2 class="s1-text mb-0">{{ trans("header.application_setting_fields.notification_settings") }}</h2>
                                        </div>

                                        {{-- Notification form – replace content with the accordion from your snippet --}}
                                        <form id="SettingForm2" class="" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                            <div class="panel-group mb-2" id="accordion1">

                                                {{-- Ticket Notifications --}}
                                                <div class="panel panel-default amg-accordion" id="acc-ticket">
                                                    <div class="amg-accordion-header justify-content-start gap-3 amg-open"
                                                        onclick="toggleAcc(this)">
                                                        <span class="s2-text fw-medium">{{ trans("header.application_setting_fields.Ticket_module_Notifications") }}</span>
                                                        <span class="amg-chevron amg-open">
                                                            <svg width="7" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z" fill="#7F7F7F"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="amg-accordion-body amg-open">
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <div class="d-flex flex-wrap gap-3">
                                                                    <label class="tcf-label cursor-pointer">
                                                                        <input name="New_ticket_created" type="checkbox" class="form-check-input" value="1"
                                                                            @if($set->new_ticket_created == 1) checked @endif>
                                                                        {{ trans("header.application_setting_fields.New_ticket_created") }}
                                                                    </label>
                                                                    <label class="tcf-label cursor-pointer">
                                                                        <input name="Ticket_reopened" type="checkbox" class="form-check-input" value="1"
                                                                            @if($set->ticket_reopened == 1) checked @endif>
                                                                        {{ trans("header.application_setting_fields.Ticket_reopened") }}
                                                                    </label>
                                                                    <label class="tcf-label cursor-pointer">
                                                                        <input name="Ticket_commented" type="checkbox" class="form-check-input" value="1"
                                                                            @if($set->ticket_commented == 1) checked @endif>
                                                                        {{ trans("header.application_setting_fields.Ticket_commented") }}
                                                                    </label>
                                                                    <label class="tcf-label cursor-pointer">
                                                                        <input name="sla_breached" type="checkbox" class="form-check-input" value="1"
                                                                            @if($set->sla_breached == 1) checked @endif>
                                                                        {{ trans("header.application_setting_fields.SLA_breacheed") }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- User Notifications --}}
                                                <div class="panel panel-default amg-accordion" id="acc-user">
                                                    <div class="amg-accordion-header justify-content-start gap-3 amg-open"
                                                        onclick="toggleAcc(this)">
                                                        <span class="s2-text fw-medium">{{ trans("header.application_setting_fields.User_module_Notifications") }}</span>
                                                        <span class="amg-chevron amg-open">
                                                            <svg width="7" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z" fill="#7F7F7F"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="amg-accordion-body amg-open">
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <div class="d-flex flex-wrap gap-3">
                                                                    <label class="tcf-label cursor-pointer">
                                                                        <input name="New_user_added" type="checkbox" class="form-check-input" value="1"
                                                                            @if($set->new_user_added == 1) checked @endif>
                                                                        {{ trans("header.application_setting_fields.New_user_added") }}
                                                                    </label>
                                                                    <label class="tcf-label cursor-pointer">
                                                                        <input name="user_deleted" type="checkbox" class="form-check-input" value="1"
                                                                            @if($set->user_deleted == 1) checked @endif>
                                                                        {{ trans("header.application_setting_fields.user_detected") }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- WhatsApp Notifications --}}
                                                <div class="panel panel-default amg-accordion" id="acc-whatsapp">
                                                    <div class="amg-accordion-header justify-content-start gap-3 amg-open"
                                                        onclick="toggleAcc(this)">
                                                        <span class="s2-text fw-medium">{{ trans("header.application_setting_fields.whatsapp_notifications") }}</span>
                                                        <span class="amg-chevron amg-open">
                                                            <svg width="7" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M1.28062 0.218782L8.78062 7.71878C8.85036 7.78844 8.90568 7.87115 8.94342 7.9622C8.98116 8.05325 9.00059 8.15085 9.00059 8.24941C9.00059 8.34797 8.98116 8.44556 8.94342 8.53661C8.90568 8.62766 8.85036 8.71038 8.78063 8.78003L1.28063 16.28C1.17573 16.385 1.04204 16.4566 0.89648 16.4856C0.750917 16.5145 0.600025 16.4997 0.462907 16.4429C0.32579 16.3861 0.208613 16.2898 0.12621 16.1664C0.0438069 16.043 -0.000116597 15.8978 1.99541e-07 15.7494L-4.5613e-07 0.749408C-0.000117266 0.600986 0.0438062 0.455866 0.126209 0.33242C0.208612 0.208975 0.325789 0.112757 0.462907 0.0559425C0.600024 -0.000873592 0.750916 -0.0157261 0.89648 0.0132618C1.04204 0.0422496 1.17573 0.113773 1.28062 0.218782Z" fill="#7F7F7F"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="amg-accordion-body amg-open">
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <label class="tcf-label cursor-pointer">
                                                                    <input name="whatsapp_notification_enabled" type="checkbox" class="form-check-input" value="1"
                                                                        @if($set->whatsapp_notification_enabled == 1) checked @endif>
                                                                    {{ trans("header.application_setting_fields.enable_whatsapp_notifications") }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> {{-- end panel-group --}}

                                            {{-- Submit & Cancel buttons --}}
                                            <div class="row ms-2 mb-2">
                                                <div class="col-md-12 text-start">
                                                    <button class="amg-btn amg-btn-primary" id="btnSubmit3" type="button">
                                                        <span>{{ trans("header.application_setting_fields.update_settings") }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>                                        
                                    </div>
                                </div>
                            </div>
                        </div>    
                        {{-- ==================== Device Notification Settings (flat layout) ==================== --}}
                        <div role="tabpanel" class="tab-pane fade" id="auto-updates" aria-labelledby="auto-updates-tab">
                            <div class="container-fluid py-3">
                                <div class="tab-body-wrapper">
                                    <div class="tcf-section" id="settingsmdl3">
                                        <div class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                            <h2 class="s1-text mb-0">{{ trans("settings.notification.device_notification_settings") }}</h2>
                                        </div>

                                        <form id="SettingForm3" class="" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                            <div class="tcf-form-body">

                                                {{-- ========================================================== --}}
                                                {{--  DEVICE CHECK‑IN / CHECK‑OUT (toggles)                       --}}
                                                {{-- ========================================================== --}}
                                                <div class="row mb-2">
                                                    <div class="col-md-6 form-group">
                                                        <label class="tcf-label">{{ trans("header.application_setting_fields.Device_check_in") }}</label>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="tcf-toggle-wrap">
                                                                <label class="tcf-toggle">
                                                                    <input name="device_checkin" type="checkbox" value="1" id="device_checkin"
                                                                        @if(isset($ni_not_detected_set) && $ni_not_detected_set->device_checkin == 1) checked @endif>
                                                                    <span class="tcf-toggle-slider"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label class="tcf-label">{{ trans("header.application_setting_fields.Device_checkout") }}</label>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="tcf-toggle-wrap">
                                                                <label class="tcf-toggle">
                                                                    <input name="device_checkout" type="checkbox" value="1" id="device_checkout"
                                                                        @if(isset($ni_not_detected_set) && $ni_not_detected_set->device_checkout == 1) checked @endif>
                                                                    <span class="tcf-toggle-slider"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ========================================================== --}}
                                                {{--  LIVE MONITOR (only if enabled)                             --}}
                                                {{-- ========================================================== --}}
                                                @if(config("services.live_monitor.enabled"))
                                                    <div class="row mb-2">
                                                        <div class="col-md-6 form-group">
                                                            <label for="live_monitor_auto_refresh" class="tcf-label">{{ trans("settings.notification.live_monitor_auto_refresh") }}</label>
                                                            <select name="live_monitor_auto_refresh" id="live_monitor_auto_refresh" class="form-control">
                                                                <option value="0" @if(isset($ni_not_detected_set) && $ni_not_detected_set->live_monitor_auto_refresh == 0) selected @endif>{{ trans("settings.notification.disabled") }}</option>
                                                                <option value="30000" @if(isset($ni_not_detected_set) && $ni_not_detected_set->live_monitor_auto_refresh == 30000) selected @endif>{{ trans("settings.notification.every_30_seconds") }}</option>
                                                                <option value="60000" @if(isset($ni_not_detected_set) && $ni_not_detected_set->live_monitor_auto_refresh == 60000) selected @endif>{{ trans("settings.notification.every_1_minutes") }}</option>
                                                                <option value="120000" @if(isset($ni_not_detected_set) && $ni_not_detected_set->live_monitor_auto_refresh == 120000) selected @endif>{{ trans("settings.notification.every_2_minutes") }}</option>
                                                                <option value="300000" @if(isset($ni_not_detected_set) && $ni_not_detected_set->live_monitor_auto_refresh == 300000) selected @endif>{{ trans("settings.notification.every_5_minutes") }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label for="live_monitor_auto_run_in_minute" class="tcf-label mandatory">{{ trans("settings.notification.live_monitor_auto_run_in_minute") }}</label>
                                                            <input type="text" name="live_monitor_auto_run_in_minute"
                                                                value="{{ $ni_not_detected_set->live_monitor_auto_run_in_minute ?? '' }}"
                                                                min="1" id="live_monitor_auto_run_in_minute"
                                                                class="form-control input-sm"
                                                                placeholder="{{ trans('settings.notification.enter_the_value') }}" />
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- ========================================================== --}}
                                                {{--  HIGH PRIORITY NI NOT DETECTED (toggle)                      --}}
                                                {{-- ========================================================== --}}
                                                <div class="row mb-2">
                                                    <div class="col-md-6 form-group">
                                                        <label class="tcf-label">{{ trans("settings.notification.high_priority_ni_not_detected") }}</label>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="tcf-toggle-wrap">
                                                                <label class="tcf-toggle">
                                                                    {{-- ADDED id="ni_not_detected_devices" --}}
                                                                    <input name="high_priority_ni_not_detected" type="checkbox" value="1" id="ni_not_detected_devices"
                                                                        @if(isset($ni_not_detected_set) && $ni_not_detected_set->high_priority_ni_not_detected == 1) checked @endif>
                                                                    <span class="tcf-toggle-slider"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 form-group ni_not_detected_day">
                                                        <label for="ni_not_detected_day" class="tcf-label mandatory">{{ trans("settings.notification.ni_not_detected") }}</label>
                                                        <input class="form-control" placeholder="{{ trans('settings.notification.enter_days') }}"
                                                            name="ni_not_detected" type="number" id="ni_not_detected_day"
                                                            value="{{ $ni_not_detected_set->ni_not_detected ?? '' }}" min="1">
                                                    </div>
                                                </div>

                                                {{-- ========================================================== --}}
                                                {{--  NI NOT DETECTED – TYPE, VALUE, EMAIL (hidden by default)  --}}
                                                {{-- ========================================================== --}}
                                                <div class="row mb-2 ni_not_detected_type">
                                                    <div class="col-md-6 form-group">
                                                        <label for="ni_not_detected_type" class="tcf-label">{{ trans("settings.notification.select_type") }}</label>
                                                        <select name="ni_not_detected_type" id="ni_not_detected_type" class="form-control">
                                                            <option value="null" @if(isset($ni_not_detected_set) && $ni_not_detected_set->ni_not_detected_type == null) selected @endif>{{ trans("settings.notification.select_type") }}</option>
                                                            <option value="1" @if(isset($ni_not_detected_set) && $ni_not_detected_set->ni_not_detected_type == 1) selected @endif>{{ trans('settings.notification.device_role_based') }}</option>
                                                            <option value="2" @if(isset($ni_not_detected_set) && $ni_not_detected_set->ni_not_detected_type == 2) selected @endif>{{ trans('settings.notification.device_user_mail') }}</option>
                                                            <option value="3" @if(isset($ni_not_detected_set) && $ni_not_detected_set->ni_not_detected_type == 3) selected @endif>{{ trans('settings.notification.device_read_permission') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group ni_not_detected_value">
                                                        <label for="ni_not_detected_value" class="tcf-label">{{ trans("settings.notification.select_role") }}</label>
                                                        <select name="ni_not_detected_value[]" id="ni_not_detected_value" class="form-control" multiple></select>
                                                    </div>
                                                </div>
                                                <div class="row mb-2 ni_not_detected_email">
                                                    <div class="col-md-6 form-group">
                                                        <label for="ni_not_detected_email" class="tcf-label">{{ trans("header.application_setting_fields.enter_email_comma") }}</label>
                                                        <select name="ni_not_detected_email[]" id="ni_not_detected_email" class="form-control" placeholder="{{ trans('header.application_setting_fields.enter_email_comma') }}" multiple>
                                                            @foreach(explode(',', (isset($ni_not_detected_set) ? $ni_not_detected_set->ni_not_detected_email : '')) as $option)
                                                                @if($option != '')
                                                                    <option value="{{ trim($option) }}" selected>{{ trim($option) }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-md-6 form-group">
                                                        <label class="tcf-label">{{ trans("settings.notification.do_you_want_to_send_a_reminder_to_users") }}</label>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="tcf-toggle-wrap">
                                                                <label class="tcf-toggle">
                                                                    {{-- ADDED id="send_reminder" --}}
                                                                    <input name="send_reminder" type="checkbox" value="1" id="send_reminder"
                                                                        @if(isset($ni_not_detected_set) && $ni_not_detected_set->send_reminder == 1) checked @endif>
                                                                    <span class="tcf-toggle-slider"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6 form-group number_of_devices">
                                                        <label for="number_of_devices" class="tcf-label mandatory">{{ trans("settings.notification.number_of_devices") }}</label>
                                                        <div class="input-group">
                                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                                <input class="form-control" placeholder="{{ trans('settings.notification.enter_number_devices') }}"
                                                                name="number_of_devices" type="number" id="number_of_devices"
                                                                value="{{ $ni_not_detected_set->number_of_devices ?? '' }}" min="1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ========================================================== --}}
                                                {{--  REMINDER – TYPE, VALUE, EMAIL (hidden by default)         --}}
                                                {{-- ========================================================== --}}
                                                <div class="row mb-2 send_reminder_users_type">
                                                    <div class="col-md-6 form-group">
                                                        <label for="send_reminder_users_type" class="tcf-label">{{ trans("settings.notification.select_type") }}</label>
                                                        <select name="send_reminder_users_type" id="send_reminder_users_type" class="form-control">
                                                            <option value="null" @if(isset($ni_not_detected_set) && $ni_not_detected_set->send_reminder_users_type == null) selected @endif>{{ trans("settings.notification.select_type") }}</option>
                                                            <option value="1" @if(isset($ni_not_detected_set) && $ni_not_detected_set->send_reminder_users_type == 1) selected @endif>{{ trans('settings.notification.device_role_based') }}</option>
                                                            <option value="2" @if(isset($ni_not_detected_set) && $ni_not_detected_set->send_reminder_users_type == 2) selected @endif>{{ trans('settings.notification.device_user_mail') }}</option>
                                                            <option value="3" @if(isset($ni_not_detected_set) && $ni_not_detected_set->send_reminder_users_type == 3) selected @endif>{{ trans('settings.notification.device_read_permission') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group send_reminder_users_value">
                                                        <label for="send_reminder_users_value" class="tcf-label">{{ trans("settings.notification.select_role") }}</label>
                                                        <select name="send_reminder_users_value[]" id="send_reminder_users_value" class="form-control" multiple></select>
                                                    </div>
                                                </div>
                                                <div class="row mb-2 send_reminder_users_email d-none">
                                                    <div class="col-md-6 form-group">
                                                        <label for="send_reminder_users_email" class="tcf-label">{{ trans("header.application_setting_fields.enter_email_comma") }}</label>
                                                        <select name="send_reminder_users_email[]" id="send_reminder_users_email" class="form-control" placeholder="{{ trans('header.application_setting_fields.enter_email_comma') }}" multiple>
                                                            @foreach(explode(',', (isset($ni_not_detected_set) ? $ni_not_detected_set->send_reminder_users_email : '')) as $option)
                                                                @if($option != '')
                                                                    <option value="{{ trim($option) }}" selected>{{ trim($option) }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div> {{-- end tcf-form-body --}}

                                            {{-- ========================================================== --}}
                                            {{--  SUBMIT BUTTON                                              --}}
                                            {{-- ========================================================== --}}
                                            <div class="row ms-2 mb-2">
                                                <div class="col-md-12 text-start">
                                                    <button class="amg-btn amg-btn-primary" id="btnSubmit3" type="button">
                                                        <span>{{ trans("header.application_setting_fields.update_settings") }}</span>
                                                    </button>
                                                    
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <!-- ========================================================== -->
                        <!--  TAB PANE: EMAIL CONFIGURATION (flat, no accordion)        -->
                        <!-- ========================================================== -->
                        <div role="tabpanel" class="tab-pane fade" id="email-config" aria-labelledby="email-config-tab">
                            <div class="container-fluid py-3">
                                <div class="tab-body-wrapper">
                                    <div class="tcf-section" id="settingsmdl4">
                                        <div class="ticket-confg-tab-header px-4 py-3 d-flex justify-content-between align-items-center">
                                            <h2 class="s1-text mb-0">{{ trans("header.application_setting_fields.email_config_setting") }}</h2>
                                        </div>

                                        <form id="SettingForm4" class="" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                            <div class="tcf-form-body">

                                                <!-- Row 1: Enable Mail Service + Mail Driver -->
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label class="tcf-label">{{ trans("settings.notification.Enable_Mail_Service") }}</label>
                                                        <div class="input-group">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <div class="tcf-toggle-wrap">
                                                                    <label class="tcf-toggle">
                                                                        <input name="mail_service_enabled" type="checkbox" value="1" id="mail_service_enabled"
                                                                            @if(isset($smtp_config) && $smtp_config->mail_service_enabled == 1) checked @endif>
                                                                        <span class="tcf-toggle-slider"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_driver" class="tcf-label mandatory">{{ trans("settings.notification.Mail_Driver") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="smtp" name="mail_driver"
                                                                type="text" value="{{ $smtp_config->mail_driver ?? '' }}" id="mail_driver">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 2: Mail Host + Mail Port -->
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_host" class="tcf-label mandatory">{{ trans("settings.notification.Mail_Host") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="smtp.mailgun.org" name="mail_host"
                                                                type="text" value="{{ $smtp_config->mail_host ?? '' }}" id="mail_host">
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_port" class="tcf-label mandatory">{{ trans("settings.notification.Mail_Port") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="587" name="mail_port"
                                                                type="number" value="{{ $smtp_config->mail_port ?? '' }}" id="mail_port">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 3: Mail Username + Mail Password -->
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_username" class="tcf-label">{{ trans("settings.notification.Mail_Username") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="user@example.com" name="mail_username"
                                                                type="text" value="{{ $smtp_config->mail_username ?? '' }}" id="mail_username">
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_password" class="tcf-label">{{ trans("settings.notification.Mail_Password") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="Enter new password to change" name="mail_password"
                                                                type="password" value="{{ !empty($smtp_config->mail_password) ? '********' : '' }}" id="mail_password">
                                                            
                                                        </div>
                                                        @if(isset($smtp_config) && !empty($smtp_config->mail_password))                                                                
                                                                <small class="text-muted ms-2">{{ trans("settings.notification.password_info") }}</small>
                                                            @endif
                                                    </div>
                                                </div>

                                                <!-- Row 4: Mail Encryption + From Address -->
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_encryption" class="tcf-label">{{ trans("settings.notification.Mail_Encryption") }}</label>
                                                        <div class="input-group">
                                                            <select name="mail_encryption" id="mail_encryption" class="form-control">
                                                                <option value="tls" @if(isset($smtp_config) && $smtp_config->mail_encryption == 'tls') selected @endif>TLS</option>
                                                                <option value="ssl" @if(isset($smtp_config) && $smtp_config->mail_encryption == 'ssl') selected @endif>SSL</option>
                                                                <option value="" @if(isset($smtp_config) && $smtp_config->mail_encryption == '') selected @endif>None</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_from_address" class="tcf-label mandatory">{{ trans("settings.notification.From_Address") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="noreply@yourdomain.com" name="mail_from_address"
                                                                type="email" value="{{ $smtp_config->mail_from_address ?? '' }}" id="mail_from_address">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Row 5: From Name -->
                                                <div class="row mb-2">
                                                    <div class="amg-form-field amg-form-field-row col-md-6">
                                                        <label for="mail_from_name" class="tcf-label mandatory">{{ trans("settings.notification.From_Name") }}</label>
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="Your App Name" name="mail_from_name"
                                                                type="text" value="{{ $smtp_config->mail_from_name ?? '' }}" id="mail_from_name">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!-- end tcf-form-body -->

                                            <!-- Submit Button -->
                                            <div class="row ms-2 mb-2">
                                                <div class="col-md-12 text-start">
                                                    <button class="amg-btn amg-btn-primary" id="btnSubmit4" type="button">
                                                        <span>{{ trans("header.application_setting_fields.update_settings") }}</span>
                                                    </button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>            
            </main>
        </div>
    </section>
@endsection
@push('css')
    <link rel="stylesheet" href="{!! CommonHelper::asset('assets/css/ticket_config.css') !!}">
    <link href="{!! CommonHelper::asset('plugins/mdtimepicker/mdtimepicker.css') !!}" rel="stylesheet" />
    <link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
    <style>
        .select2-container--open {
            z-index: 1060 !important;
        }
        .select2-container--default .select2-search--inline .select2-search__field {
            width: none !important;
        }

        #ticket-config-wrapper .ticket-confg-tab-header {
            background: #eff2fa;
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

        department table pagination start
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

        /* .search-input-wrap input:focus {
            border-color: #b0b8d0;
            background: #fff;
        } */

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
        [data-bs-theme="dark"] #ticket-config-wrapper .amg-form-field input[type="password"],
        [data-bs-theme="dark"] #ticket-config-wrapper .amg-form-field .select2,
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
        [data-bs-theme=dark] .select2-container .select2-search{
            background : unset !important;
        }
        [data-bs-theme=dark] .select2-container--default .select2-selection--multiple{
            background-color: #191919;
        }

        /* ticket handler limit css end */
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/settings/index.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
    <script type="text/javascript">
        var coll = document.getElementsByClassName("plus");
        var i;
        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            var config = new Object;
            config.url = new Object;
            config.url.add = "{{ url('new-settings/save_changes') }}";
            //config.url.add = "{{ url('settings/editsettings') }}";
            config.url.getUserByAjax = "{{ url('getUserForDropDown') }}";
            config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
            config.url.getUserNIByQuery = "{{ url('getUsersEmailSelect') }}";
            config.url.getCustomFieldsetByModule = "{{ url('getCustomFieldsetByModule') }}";
            config.url.edit = "{{ url('new-settings/save_notification') }}";
            config.url.getAssetDepartments = "{{ route('getAssetDepartments') }}";

            config.url.ni_not_detected_notification = "{{ url('new-settings/ni-not-detected-notification') }}";
            config.url.smtp_config = "{{ url('new-settings/smtp-config') }}";
            config.token = "{{ csrf_token() }}";
            config.data = {!! json_encode($settings) !!};
            config.roles = {!! json_encode($roles) !!};
            config.edit_role = {!! json_encode($editRole) !!};
            config.editRoleReminder = {!! json_encode($editRoleReminder) !!};
            config.editAlertRoleReminder = {!! json_encode($editAlertRoleReminder) !!};
            config.ni_not_detected_set = {!! json_encode($ni_not_detected_set) !!};
            config.assetTagData = {!! json_encode($assetTagData) !!};
            config.blockDeviceIntimationUsers = {!! json_encode($blockDeviceIntimationUsers) !!};
            config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFieldsLicence($companyFieldset->fields)) : json_encode('') !!};
            config.company_defulte = {!! json_encode($company_id) !!};
            config.translations = {
                Select_the_custom_fieldset: '{{ trans('header.custom_tab_fields.select_the_custom_fieldset') }}',
                something_went_wrong: '{{ trans('header.custom_tab_fields.something_went_wrong') }}',
                select_role: '{{ trans("settings.notification.select_role") }}',
                enter_email_comma: '{{trans('settings.notification.enter_email_comma')}}',
            };
            new SettingsAdd(config);
            new SettingsEdit(config);
            new DeviceSettings(config);
            new EmailSettings(config);


            $('#css_theme').select2({
                templateResult: formatColorOption,
                templateSelection: formatColorOption,
                escapeMarkup: function(m) { return m; }
            });

            function formatColorOption(option) {
                if (!option.id) {
                    return option.text;
                }

                return $(
                    '<div style="display: flex; align-items:center">' +
                    '<span style="width: 20px; height: 20px; background-color: ' + option.text + '; margin-right: 5px;border:1px solid;"></span>' +
                    '<span>' + option.text + '</span> </div>'
                );
            }

        });

        $(document).on('click', '.go-cancel', function(e) {
            e.preventDefault();
            window.location.href = "{{ url('settings') }}";
        });

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
