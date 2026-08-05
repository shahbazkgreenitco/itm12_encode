{{-- @page-meta
{
"page_no": "Report-23-26",
"file": "report.blade.php",
"versions": [
{
"version": "1.0",
"writer": "Shivam Kumar",
"from": "2026-06",
"reviewer": null,
"description": "Initial setup"
}
]
}
--}}
@extends('layouts.layout1')
@section('title', trans("custom_report.custom_reports"))
@section('content')
    <div id="customReportAdd">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans("custom_report.custom_reports") }}</h3>
            <div class="d-flex gap-8">

                <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip"
                    title='{{ trans("custom_report.filter")}}'>
                    <svg viewBox="0 0 20 18" fill="none">
                        <path
                            d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                            fill="currentColor" />
                    </svg>
                    <span class="b6-text opacity-50">{{ trans("custom_report.filter")}}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>
                <button class="amg-btn amg-btn-primary amg-btn-sm" id="act-add-report" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Add">
                    <svg width="14" height="14" viewBox="0 0 19 19" fill="none">
                        <path
                            d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                            fill="currentColor" />
                    </svg>
                    <span>{{ trans("custom_report.add")}}</span>
                </button>
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="col-auto">
                                <select class="userModulePageLenth amg-table-pagination-dropdown" id="report-list">
                                    <option value="10" selected>{{trans('custom_report.show_10')}}</option>
                                    <option value="25">{{trans('custom_report.show_25')}}</option>
                                    <option value="50">{{trans('custom_report.show_50')}}</option>
                                    <option value="100">{{trans('custom_report.show_100')}}</option>
                                </select>
                            </div>
                            <div class="flex-grow-1"></div>

                            <div>
                                <div class="amg-list-searchbar">
                                    <button type="button" class="amg-list-searchbar__icon-btn btn-searchbox"
                                        aria-label="Search Ticket Status"
                                        title="{{trans('custom_report.search_ticket_status')}}">
                                        <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </button>
                                    <input type="text" name="search"
                                        class="amg-list-searchbar__input searchbox plain-search"
                                        placeholder="{{trans('custom_report.search_ticket_status')}}"
                                        autocomplete="off">
                                </div>
                            </div>

                            <button class="amg-refresh-btn btn-reload-list btn-reload-list" data-bs-toggle="tooltip" data-bs-placement="top" title="{{trans('custom_report.refresh')}}">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                        fill="currentColor"></path>
                                </svg>
                                <span>{{trans('custom_report.refresh')}}</span>
                            </button>
                        </div>
                        <div class="list-view-panel" id="main-role-permission-wrapper">
                            <div class="table-responsive">
                                <table id="mytable" class="amg-datatable mytable table display app-data-table">
                                    <thead>
                                        <tr>
                                            <th><h4>{{ trans('custom_report.company_name') }}</h4></th>
                                            <th><h4>{{ trans('custom_report.report_title') }}</h4></th>
                                            <th><h4>{{ trans("custom_report.report_status") }}</h4></th>
                                            <th><h4>{{ trans("custom_report.report_type") }}</h4></th>
                                            <th><h4>{{ trans("custom_report.count_fields") }}</h4></th>
                                            <th><h4>{{ trans('custom_report.updated_at') }}</h4></th>
                                            <th><h4>{{ trans('custom_report.action') }}</h4></th>
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
        </main>
        @include('reports.custom.title_mdl')
        @include('reports.custom.field_mdl')
        @include('reports.custom.rule_mdl')
    </div>
@endsection

@push('css')
    <style>
        /* CSS Variables for Theming */
        :root {
            /* Light Mode (Default) */
            --bg-primary: #ffffff;
            --bg-secondary: #fafafa;
            --bg-tertiary: #e1e1e1;
            --bg-hover: #f5f5f5;
            --bg-field: #e9e9e9;
            --bg-input: #ffffff;
            --bg-addon: #555555;
            
            --text-primary: #444444;
            --text-secondary: #777777;
            --text-muted: #aba7a7;
            --text-inactive: #e8ded7;
            --text-white: #ffffff;
            
            --border-primary: #e3e1e1;
            --border-secondary: #d9d8da;
            --border-addon: #555555;
            
            --shadow-color: rgba(0, 0, 0, 0.1);
            
            --link-color: #1137fa;
            --danger-color: #ff4141;
            --hover-bg: #c6c6c6;
            --hover-text: #222222;
            --hover-logic: #dfdddd;
            
            --modal-overflow: visible;
            --field-height: 350px;
            --max-height-table: 250px;
        }

        /* Dark Mode */
        [data-bs-theme="dark"] {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --bg-tertiary: #3a3a3a;
            --bg-hover: #3d3d3d;
            --bg-field: #2a2a2a;
            --bg-input: #2d2d2d;
            --bg-addon: #6c757d;
            
            --text-primary: #e0e0e0;
            --text-secondary: #aaaaaa;
            --text-muted: #888888;
            --text-inactive: #665544;
            --text-white: #ffffff;
            
            --border-primary: #4a4a4a;
            --border-secondary: #4a4a4a;
            --border-addon: #6c757d;
            
            --shadow-color: rgba(0, 0, 0, 0.3);
            
            --link-color: #4d7cff;
            --danger-color: #ff6b6b;
            --hover-bg: #4a4a4a;
            --hover-text: #ffffff;
            --hover-logic: #4a4a4a;
            
            --modal-overflow: visible;
            --field-height: 350px;
            --max-height-table: 250px;
        }

        /* Base Styles with Variables */
        #titleMdl .modal-body {
            overflow-y: var(--modal-overflow) !important;
            height: auto !important;
        }

        .colga {
            width: 200px;
        }

        .fields-box {
            height: var(--field-height);
            overflow: auto;
            background: var(--bg-tertiary);
        }

        .list-group li {
            color: var(--text-primary);
        }

        .fields-box li.na {
            background: var(--bg-field);
            color: var(--text-muted);
        }

        .field i {
            font-size: 18px;
            padding: 2px 8px;
            font-weight: bold;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }

        .field i:hover {
            background: var(--hover-bg);
            color: var(--hover-text);
            border-radius: 3px;
        }

        .field i.fa-times {
            font-size: 16px;
            font-weight: 100;
        }

        .field.na i,
        .field.na i:hover {
            color: var(--text-inactive);
            background: transparent;
            cursor: not-allowed;
        }

        .field span {
            margin: -2px -12px 0px 0px;
        }

        .table a:hover {
            text-decoration: underline;
        }

        .table a {
            color: var(--link-color);
        }

        .bootstrap-table {
            max-height: var(--max-height-table) !important;
        }

        /* Rule Dictation Box */
        .rule-dictation-box {
            min-height: 51px;
            list-style: none;
            padding: 0;
            border: 1px solid var(--border-primary);
            border-radius: 3px;
            background: var(--bg-primary);
        }

        .rule-dictation-box li {
            float: left;
            border-radius: 2px;
            min-width: 45px;
            text-align: center;
            height: 50px;
            transition: background 0.2s ease;
        }

        .rule-dictation-box li span.code {
            font-size: 16px;
            padding: 14px 0 13px 0;
            display: block;
            transition: all 0.7s;
            color: var(--text-primary);
        }

        .rule-dictation-box li:hover {
            background: var(--bg-hover);
        }

        .rule-dictation-box li:hover span.code {
            font-size: 13px;
            padding: 7px 0 5px 0;
        }

        .rule-dictation-box li .rmvbox {
            margin: 0 auto;
            width: 18px;
            height: 18px;
            background: var(--danger-color);
            display: none;
            color: var(--text-white);
            border-radius: 10px;
            line-height: 17px;
            font-size: 10px;
            border-color: transparent;
            transition: all 0.7s;
            cursor: pointer;
        }

        .rule-dictation-box li:hover .rmvbox {
            display: block;
        }

        /* Lister */
        #lister {
            padding: 10px 12px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-secondary);
            border-radius: 3px;
            font-size: 13px;
            color: var(--text-primary);
        }

        /* Logic Adder */
        span.logic-adder {
            display: inline-block;
            min-width: 22px;
            margin-left: 15px;
            border-radius: 2px;
            text-align: center;
            padding: 2px 4px 2px 4px;
            cursor: pointer;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        span.logic-adder:hover {
            background: var(--hover-logic);
            color: var(--hover-text);
        }

        /* Modal Buttons */
        #ruleMdl button {
            border-radius: 3px;
        }

        /* Input Group Addon */
        .input-group-addon {
            border: 1px solid var(--border-addon);
            border-radius: 5px 0px 0px 5px;
            padding: 6px 12px;
            font-size: 14px;
            font-weight: 400;
            line-height: 1;
            color: var(--text-white);
            text-align: center;
            background-color: var(--bg-addon);
        }

        /* Bootstrap 5 Form Group */
        .form-group .col-md-3 {
            text-align: right;
        }

        /* User Status */
        .inactive-user,
        .active-user {
            margin-left: 0px !important;
            margin-right: 3px !important;
        }

        /* Field Count Button */
        .field-count-btn {
            text-decoration: none !important;
            color: var(--link-color);
        }

        /* Additional Dark Mode Overrides for Bootstrap Components */
        [data-bs-theme="dark"] .modal-content {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: var(--border-primary);
        }

        [data-bs-theme="dark"] .form-control {
            background-color: var(--bg-input);
            color: var(--text-primary);
            border-color: var(--border-primary);
        }

        [data-bs-theme="dark"] .form-control:focus {
            background-color: var(--bg-input);
            color: var(--text-primary);
            border-color: var(--link-color);
        }

        [data-bs-theme="dark"] .list-group-item {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            border-color: var(--border-primary);
        }

        [data-bs-theme="dark"] .btn-close {
            filter: invert(1);
        }

        [data-bs-theme="dark"] .table {
            color: var(--text-primary);
        }

        [data-bs-theme="dark"] .table td,
        [data-bs-theme="dark"] .table th {
            border-color: var(--border-primary);
        }

        /* Scrollbar Styling for Dark Mode */
        [data-bs-theme="dark"] .fields-box::-webkit-scrollbar {
            width: 8px;
        }

        [data-bs-theme="dark"] .fields-box::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }

        [data-bs-theme="dark"] .fields-box::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 4px;
        }

        [data-bs-theme="dark"] .fields-box::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{!! CommonHelper::asset('plugins/jqueryui-nodatepicker/jquery-ui.min.js') !!}"></script>
    <script type="text/javascript" src="{!! CommonHelper::asset('js/reports/custom/reports.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var config = {};
            config.url = {};
            config.url.get_reports = "{{ url('reports/custom-reports/get-reports') }}";
            config.url.add_report = "{{ url('reports/custom-reports/add-report') }}";
            config.url.edit_report = "{{ url('reports/custom-reports/edit-report') }}";
            config.url.delete_report = "{{ url('reports/custom-reports/delete-report') }}";
            config.url.update_name = "{{ url('reports/custom-reports/update-name') }}";
            config.url.get_fields = "{{ url('reports/custom-reports/get-fields') }}";
            config.url.update_fields = "{{ url('reports/custom-reports/update-fields') }}";
            config.url.view_report = "{{ url('reports/custom-reports/view') }}";
            config.url.get_criteria = "{{ url('reports/custom-reports/get-criteria') }}";
            config.url.get_rules = "{{ url('reports/custom-reports/get-rules') }}";
            config.url.get_rule = "{{ url('reports/custom-reports/get-rule') }}";
            config.url.update_rule = "{{ url('reports/custom-reports/update-rule') }}";
            config.url.delete_rule = "{{ url('reports/custom-reports/delete-rule') }}";
            config.url.update_dictation = "{{ url('reports/custom-reports/update-dictation') }}";
            config.url.get_models = "{{ url('reports/custom-reports/get-models') }}";
            config.url.get_manufacturers = "{{ url('reports/custom-reports/get-manufacturers') }}";
            config.url.get_status_labels = "{{ url('reports/custom-reports/get-status-labels') }}";
            config.url.get_locations = "{{ url('reports/custom-reports/get-locations') }}";
            config.url.get_places = "{{ url('reports/custom-reports/get-places') }}";
            config.url.get_purchase_references = "{{ url('reports/custom-reports/get-purchase-references') }}";
            config.url.get_categories = "{{ url('reports/custom-reports/get-categories') }}";
            config.url.get_account_types = "{{ url('reports/custom-reports/get-account-types') }}";
            config.url.get_suppliers = "{{ url('reports/custom-reports/get-suppliers') }}";
            config.url.get_departments = "{{ url('reports/custom-reports/get-departments') }}";
            config.url.get_lease_types = "{{ url('reports/custom-reports/get-lease-types') }}";
            config.url.get_maintenance_incharge = "{{ url('reports/custom-reports/get-maintenance-incharge') }}";
            config.url.get_status_list = "{{ url('reports/custom-reports/get-status-list') }}";
            config.url.get_priority = "{{ url('reports/custom-reports/get-priority') }}";
            config.url.get_problemCategory = "{{ url('reports/custom-reports/get-problemcategory') }}";
            config.url.get_subCategory = "{{ url('reports/custom-reports/get-subcategory') }}";
            config.url.getUser = "{{ url('reports/custom-reports/getUser') }}";
            config.url.getDevice = "{{ url('reports/custom-reports/getDevice') }}";
            config.url.getTags = "{{ url('reports/custom-reports/getTags') }}";
            config.url.getTicketType = "{{ url('reports/custom-reports/getTicketType') }}";
            config.url.getCompanyByUserAccess = "{{ url('getCompanyByUserAccess') }}";
            config.available_fields = {!! json_encode($vd->available_fields) !!};
            config.comparison_codes = {!! json_encode($vd->comparison_codes) !!};
            config.opts_yes_no_na = {!! json_encode($vd->opts_yes_no_na) !!};
            config.opts_yes_no = {!! json_encode($vd->opts_yes_no) !!};
            config.opts_expired_not = {!! json_encode($vd->opts_expired_not) !!};
            config.opts_lease_type = {!! json_encode($vd->opts_lease_type) !!};
            config.opts_maintenance_incharges = {!! json_encode($vd->opts_maintenance_incharges) !!};
            config.opts_device_from = {!! json_encode($vd->opts_device_from) !!};
            config.opts_assigned_for = {!! json_encode($vd->opts_assigned_for) !!};
            config.permissions = {!! json_encode($permissionArray, true) !!};
            config.translations = {
                serach_option: '{{ trans('custom_report.serach_option') }}',
                No_Filter: '{{ trans('custom_report.no_filter') }}',
                edit_custom_report: '{{ trans('custom_report.edit_custom_report') }}',
                New_Custom_Report: '{{ trans('custom_report.new_custom_report') }}',
                system_not_allowed: '{{ trans('custom_report.system_not_allowed') }}',
                are_you_delete: '{{ trans('custom_report.are_you_delete') }}',
                search: '{{ trans('custom_report.search') }}',
                add_custom_report: '{{ trans('custom_report.add_custom_report') }}',
                Refresh_List: '{{ trans('custom_report.refresh_list') }}',
                filter: '{{ trans('custom_report.filter') }}'
            };
            new MyApp(config);
        });
    </script>
@endpush