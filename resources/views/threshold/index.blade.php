{{-- * ------------------------------------------------------------
File: index.blade.php
Module: Threshold Module
THRE/26/01
------------------------------------------------------------
Version: 1.0.0
Author: Prithvi Pillai
Page ID: #001
Reviewed By:
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------ --}}

{{-- * ------------------------------------------------------------
------------------------------------------------------------
Version: 1.0.1
Author: Muzaffar Shaikh
Page ID: #001
Reviewed By:
------------------------------------------------------------
Change Log: Datatable Improvements & Record Management Fixes
[1.0.1] - Added dark mode support for the Threshold Settings table.
[1.0.1] - Fixed the Email Address input field where typed text was overflowing outside the text box.
[1.0.1] - Updated validation/error messages to display in red.
[1.0.1] - Added the missing mandatory (*) indicator for the Email Address field.
[1.0.1] - Fixed SVG icon visibility in form fields for dark mode.
[1.0.1] - Improved the modal UI by reducing the header text size, reducing the close icon size, and adding a border to the modal footer.
[1.0.1] - Fixed the Edit Threshold button tooltip.
[1.0.1] - removed + icons from Edit button used pen icon instead
[1.0.1] - Adjusted the Email Address "T" logo/icon size for proper alignment.
[1.0.1] - Reduced the header width and applied a lighter header background color based on feedback
------------------------------------------------------------ --}}

@extends('layouts.layout1')
@section('title', trans('threshold.view.header'))
@section('content')
    <main class="main-content" id="mainContent">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between px-4">
            <div class="mb-0">
                <h3 class="h3-text mb-0">{{ trans('threshold.view.page_heading') }}</h3>
            </div>
            @can('ThresholdEdit')
                <div class="d-flex gap-8">
                    <button class="amg-btn amg-btn-primary amg-btn-sm open-add-modal" type="button" data-bs-toggle="tooltip"
                        title="{{ trans('threshold.view.edit_button') }}">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path>
                        </svg>
                        <span>{{ trans('threshold.view.edit_button') }}</span>
                    </button>
                </div>
            @endcan
        </div>
        <div class="tab-pane fade show active" id="main-user-list-wrapper">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="mytable" class="table amg-datatable threshold-list display table-striped">
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td>{{ trans('threshold.view.threshold_module') }}</td>
                                <td>
                                    @if ($TS->threshold_enabled == 1)
                                        {{ trans('threshold.view.threshold_enabled') }}
                                    @else
                                        {{ trans('threshold.view.threshold_disabled') }}
                                    @endif
                                </td>
                            </tr>
                            @if ($TS->threshold_enabled == 1)
                                <tr>
                                    <td>{{ trans('threshold.view.alert_notification') }}</td>
                                    <td>
                                        @if ($TS->alerts_enabled == 1)
                                            {{ trans('threshold.view.notification_enabled') }}
                                        @else
                                            {{ trans('threshold.view.notification_disabled') }}
                                        @endif
                                    </td>
                                </tr>
                                @if ($TS->alerts_enabled == 1)
                                    <tr>
                                        <td>{{ trans('threshold.view.send_alerts_to') }}</td>
                                        <td>
                                            @if ($TS->send_alerts == 1)
                                                <span class="highlight-email">{{ $TS->email }}</span>
                                            @else
                                                {{ trans('threshold.view.general_settings_alert') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('threshold.modal_html')
    </main>
@endsection
@push('css')
<style>
    [data-bs-theme=dark] .threshold-list.table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-color-type: var(--bs-table-striped-color);
        --bs-table-bg-type: #2A2A2D !important;
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/support_validate.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/threshold/list.js') !!}"></script>

    <script type="text/javascript">
        var config = new Object;
        config.url = new Object;
        config.url.list = "{{ url('threshold') }}";
        config.url.edit = "{{ url('threshold/edit') }}";
        config.url.getUserNIByQuery = "{{ url('getUsersEmailSelect') }}";
        config.ts = {!! json_encode($TS) !!};
        config.token = "{{ csrf_token() }}";
        new Threshold(config);
    </script>
@endpush
