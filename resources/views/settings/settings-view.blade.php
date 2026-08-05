{{-- * ------------------------------------------------------------
* File: settings-view.blade.php
* Module: Settings Module
* Setns/26/01
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
@extends('layouts.layout1')
@section('title', trans("header.application_setting_fields.application_settings"))
@section('content')
<div id="main-user-list-wrapper">
    <section class="content">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans("header.application_setting_fields.application_settings") }}</h3> 
            <div class="d-flex gap-8">
                <!-- edit settings -->
                @can("SettingEdit")
                    <button class="amg-btn amg-btn-primary amg-btn-sm open-add-modal"   onclick="window.location.href='{{ url('settings/edit') }}'" type="button" data-bs-toggle="tooltip" title="{{ trans("header.application_setting_fields.edit_settings") }}">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.7586 5.73262L14.268 2.24122C14.1519 2.12511 14.0141 2.03301 13.8624 1.97018C13.7107 1.90734 13.5482 1.875 13.384 1.875C13.2198 1.875 13.0572 1.90734 12.9056 1.97018C12.7539 2.03301 12.6161 2.12511 12.5 2.24122L2.86641 11.8756C2.74983 11.9912 2.65741 12.1289 2.59451 12.2806C2.5316 12.4323 2.49948 12.595 2.50001 12.7592V16.2506C2.50001 16.5821 2.6317 16.9001 2.86612 17.1345C3.10054 17.3689 3.41849 17.5006 3.75001 17.5006H7.24141C7.40563 17.5011 7.5683 17.469 7.71999 17.4061C7.87168 17.3432 8.00935 17.2508 8.12501 17.1342L17.7586 7.50059C17.8747 7.38452 17.9668 7.2467 18.0296 7.09503C18.0925 6.94335 18.1248 6.78078 18.1248 6.61661C18.1248 6.45243 18.0925 6.28986 18.0296 6.13819C17.9668 5.98651 17.8747 5.8487 17.7586 5.73262ZM7.24141 16.2506H3.75001V12.7592L10.625 5.88419L14.1164 9.37559L7.24141 16.2506ZM15 8.49122L11.5086 5.00059L13.3836 3.12559L16.875 6.61622L15 8.49122Z" fill="white"/>
                            </svg>

                            <span>{{ trans("header.application_setting_fields.edit_settings") }}</span>
                    </button>
                @endcan               
            </div>          
        </div>
        <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="list-view-panel">
                        <div class="table-responsive">
                            @php
                                $yesIcon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 10.8181 20.7672 9.64778 20.3149 8.55585C19.8626 7.46392 19.1997 6.47177 18.364 5.63604C17.5282 4.80031 16.5361 4.13738 15.4442 3.68508C14.3522 3.23279 13.1819 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12C3 14.3869 3.94821 16.6761 5.63604 18.364C7.32387 20.0518 9.61305 21 12 21ZM11.768 15.64L16.768 9.64L15.232 8.36L10.932 13.519L8.707 11.293L7.293 12.707L10.293 15.707L11.067 16.481L11.768 15.64Z" fill="#186B43"/>
                                            </svg>';
                                $noIcon = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 18C13.9707 18 18 13.9707 18 9C18 4.0293 13.9707 0 9 0C4.0293 0 0 4.0293 0 9C0 13.9707 4.0293 18 9 18ZM12.6594 5.3406C12.7858 5.46716 12.8568 5.63872 12.8568 5.8176C12.8568 5.99648 12.7858 6.16804 12.6594 6.2946L9.954 9L12.6585 11.7045C12.7777 11.8325 12.8426 12.0017 12.8396 12.1766C12.8365 12.3514 12.7656 12.5183 12.642 12.642C12.5183 12.7656 12.3514 12.8365 12.1766 12.8396C12.0017 12.8426 11.8325 12.7777 11.7045 12.6585L9 9.9558L6.2955 12.6603C6.2337 12.7266 6.15918 12.7798 6.07638 12.8167C5.99358 12.8536 5.9042 12.8734 5.81357 12.875C5.72294 12.8766 5.63291 12.86 5.54886 12.826C5.46481 12.7921 5.38846 12.7415 5.32437 12.6774C5.26027 12.6133 5.20974 12.537 5.17579 12.4529C5.14184 12.3689 5.12517 12.2789 5.12677 12.1882C5.12837 12.0976 5.1482 12.0082 5.1851 11.9254C5.22199 11.8426 5.27518 11.7681 5.3415 11.7063L8.0442 9L5.3406 6.2955C5.27428 6.2337 5.22109 6.15918 5.1842 6.07638C5.1473 5.99358 5.12747 5.9042 5.12587 5.81357C5.12427 5.72294 5.14094 5.63291 5.17489 5.54886C5.20884 5.46481 5.25937 5.38846 5.32347 5.32437C5.38756 5.26027 5.46391 5.20974 5.54796 5.17579C5.63201 5.14184 5.72204 5.12517 5.81267 5.12677C5.9033 5.12837 5.99268 5.1482 6.07548 5.1851C6.15828 5.22199 6.2328 5.27518 6.2946 5.3415L9 8.0442L11.7045 5.3397C11.8311 5.21329 12.0026 5.14229 12.1815 5.14229C12.3604 5.14229 12.5319 5.21329 12.6585 5.3397" fill="#F12F35"/>
                                            </svg>';
                            @endphp
                            <table id="mytable" class="table display table-striped">
                                <tbody>
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.site_name") }}</td>
                                        <td>{{ $settings->site_name }}</td>
                                    </tr>
                                    {{--
                                    <tr>
                                        <td>Allow Different Company Access</td>
                                        <td>{{ $settings->full_multiple_companies_support ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    --}}
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.default_currency") }}</td>
                                        <td>{{ $settings->default_currency }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.send_alerts_to") }}</td>
                                        <td>{{ $settings->alert_email }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td>{{ trans("header.application_setting_fields.alerts_enabled") }}</td>
                                        <td>{{ $settings->alerts_enabled ? 'Yes' : 'No' }}</td>
                                    </tr> --}}
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.alerts_enabled") }}</td>
                                        <td>
                                            <span class="d-inline-flex align-items-center">
                                               {!! $settings->alerts_enabled ? $yesIcon : $noIcon !!}
                                               <span class="ms-2">{{ $settings->alerts_enabled ? 'Yes' : 'No' }}</span>
                                           </span>
    
                                        </td>
                                    </tr>
                                    {{--
                                    <tr>
                                        <td>Header Color</td>
                                        <td>{{ $settings->header_color }}</td>
                                    </tr>
                                    --}}
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.generate_auto_incrementing_device_tag") }}</td>
                                        <td>
                                            <span class="d-inline-flex align-items-center">
                                               {!! $settings->auto_increment_assets ? $yesIcon : $noIcon !!}
                                               <span class="ms-2">{{ $settings->auto_increment_assets ? 'Yes' : 'No' }}</span>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.prefix_optional") }}</td>
                                        <td>{{ $settings->auto_increment_prefix }}</td>
                                    </tr>
                                    {{--
                                    <tr>
                                        <td>Results Per Page</td>
                                        <td>{{ $settings->per_page }}</td>
                                    </tr>
                                    --}}
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.display_QR_codes") }}</td>
                                        <td>
                                            @if ($settings->qr_code == 1)
                                                <span class="d-inline-flex align-items-center">
                                                    {!! $yesIcon !!}
                                                    <span class="ms-2">
                                                        Yes ({{ $settings->barcode_type }}) {{ $settings->qr_text }}
                                                    </span>
                                                </span>
                                            @else
                                                <span class="d-inline-flex align-items-center">
                                                    {!! $noIcon !!}
                                                    <span class="ms-2">No</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.default_EULA") }}</td>
                                        <td>{!! $settings->default_eula_text !!}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.accessory_checkin") }}</td>
                                        <td>{{ $settings->accessory_block_checkin == 1 ? "Don't Allow to Checkin Back" : "Allow to Checkin Back" }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans("header.application_setting_fields.LDAP_integration") }}</td>
                                        <td>
                                            <span class="d-inline-flex align-items-center">
                                               {!! $settings->ldap_enabled ? $yesIcon : $noIcon !!}
                                               <span class="ms-2">{{ $settings->ldap_enabled ? 'Yes' : 'No' }}</span>
                                           </span>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>  
    </section>
</div>
@endsection
@push('css')
@endpush
@push('scripts')
   
@endpush
