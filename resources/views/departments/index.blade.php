{{-- * ------------------------------------------------------------
* File: index.blade.php
* Module: Department Module
* DEPT/26/01
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}

{{-- * ------------------------------------------------------------
* File: index.blade.php
* Module: Department Module
* DEPT/26/01
* ------------------------------------------------------------
* Version: 1.0.1
* Author: Muzaffar Shaikh
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log: Datatable Improvements & Record Management Fixes
* [1.0.0] - Added a horizontal scrollbar indicator to datatables, making it clear when additional content is available.
* [1.0.0] - Updated the fallback avatar logic to display a single-letter initial for users without a profile image.
* [1.0.0] - Standardized datatable font styling for a consistent appearance.
* [1.0.0] - Added and fixed tooltips for datatable action buttons.
* [1.0.0] - Allowed reuse of a department name after the original department has been deleted.
* [1.0.0] - Restricted the Department Tag (Initials) field to accept only valid text input.
* [1.0.0] - Updated the Add/Edit form so changing the Company automatically clears the selected users in the Attender field.
* [1.0.0] - Improved handling of deleted departments across multiple browser tabs. If a department has already been deleted elsewhere, users now see the message: "Department is already deleted. Please refresh the page." The department list also refreshes automatically to keep the data in sync.
* ------------------------------------------------------------ --}}

{{-- * ------------------------------------------------------------
* File: index.blade.php
* Module: Department Module
* DEPT/26/01
* ------------------------------------------------------------
* Version: 1.0.2
* Author: Muzaffar Shaikh
* Page ID: #001
* Reviewed By: 
* ------------------------------------------------------------
* Change Log: 
* [1.0.2] - Reordered Excel export columns to match the listing, with Updated At moved to the last column.
* [1.0.2] - Fixed the Edit functionality.
* [1.0.2] - Updated Add/Edit Department to exclude inactive users and users with an expired Last Working Date from Department Head and Department Admin selection.
* ------------------------------------------------------------ --}}
@extends('layouts.layout1')
@section('title', trans("department.title"))
@section('content')
<div id="main-user-list-wrapper">
    <section class="content">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <h3 class="h3-text mb-0">{{ trans("department.title") }}</h3>

            <div class="d-flex gap-8">           

                {{-- Download Excel --}}
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-departments-export" data-bs-toggle="tooltip" title="{{ trans('department.form_fields_and_buttons.download') }}">
                   <svg viewBox="0 0 20 20" fill="none">
                        <path d="M10 3V13M10 13L6 9M10 13L14 9M3 17H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                    </svg>
                </button>

                {{-- Import --}}
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-import" data-bs-toggle="tooltip" title="{{ trans('department.form_fields_and_buttons.import') }}">
                    <svg viewBox="0 0 20 20" fill="none">
                        <g transform="translate(0 20) scale(1 -1)">
                            <path d="M10 3V13M10 13L6 9M10 13L14 9M3 17H17"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round" />
                        </g>
                    </svg>
                </button>

                {{-- Add Department --}}
                <button class="amg-btn amg-btn-primary amg-btn-sm open-add-modal" type="button" data-bs-toggle="tooltip" title="{{ trans('department.form_fields_and_buttons.add') }}">
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" />
                    </svg>
                    <span>{{ trans('department.form_fields_and_buttons.add_department') }}</span>
                </button>
            </div>
        </div>

        <main class="main-content" id="mainContent">
            <div class="container-fluid px-0">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="col-auto">
                                 <select id="user-list-page-length" class="amg-table-pagination-dropdown userModulePageLenth js-user-page-length" aria-label="Rows per page">
                                    <option value="10" selected>{{ trans("department.data_table.show") }}(10)</option>
                                    <option value="15">{{ trans("department.data_table.show") }}(15)</option>
                                    <option value="25">{{ trans("department.data_table.show") }}(25)</option>
                                    <option value="50">{{ trans("department.data_table.show") }}(50)</option>
                                </select>
                            </div>

                            <div class="flex-grow-1"></div>
                            <div>
                                <div class="amg-list-searchbar">
                                    <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor" />
                                    </svg>
                                    <input type="text" id="tableSearch" class="amg-list-searchbar__input searchbox" placeholder="{{ trans("department.data_table.search") }}">
                                </div>
                            </div>
                            <button class="amg-refresh-btn btn-reload-list">
                                <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                </svg>
                                <span>{{ trans("department.data_table.refresh") }}</span>
                            </button>
                        </div>

                        <div class="table-responsive gtable-cover">
                            <table id="mytable" class="table amg-datatable display app-data-table">
                                <thead>
                                    <tr>
                                        <th><h4>{{ trans("department.table_fields.id") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.department_name") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.department_tag") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.company_name") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.attender_name") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.department_head") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.fieldset") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.updated_at") }}</h4></th>
                                        <th><h4>{{ trans("department.table_fields.actions") }}</h4></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @include("departments.dept-modal")
    </section>
</div>
@endsection
@push('css')
<style>
   /* Applies to any DataTables scroll wrapper that contains a .amg-datatable table */
    .dataTables_scrollBody:has(.amg-datatable) {
        scrollbar-width: auto !important;
        -ms-overflow-style: auto !important;
    }

    .dataTables_scrollBody:has(.amg-datatable)::-webkit-scrollbar {
        height: 14px !important;
        display: block !important;
    }

    .dataTables_scrollBody:has(.amg-datatable)::-webkit-scrollbar-track {
        background: #f1f1f1 !important;
        border-radius: 20px !important;
    }

    .dataTables_scrollBody:has(.amg-datatable)::-webkit-scrollbar-thumb {
        background-color: #c1c1c1 !important;
        border-radius: 20px !important;
        border: 3px solid #f1f1f1 !important;
    }

    .dataTables_scrollBody:has(.amg-datatable)::-webkit-scrollbar-button {
        display: none !important;
    }

    .dataTables_scrollBody:has(.amg-datatable)::-webkit-scrollbar-button:horizontal:decrement:start {
        display: block !important;
        width: 16px !important;
        height: 14px !important;
        /* background-color: #f1f1f1 !important; */
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-size: 8px 10px !important;
        background-image: url("data:image/svg+xml,%3Csvg width='8' height='10' viewBox='0 0 8 10' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7.43109 1.49265L7.43109 8.50931C7.43149 8.78632 7.35343 9.05778 7.20596 9.29227C7.05848 9.52676 6.84761 9.71469 6.59776 9.83431C6.3013 9.97434 5.97148 10.0283 5.64587 9.98997C5.32025 9.95166 5.01195 9.82265 4.75609 9.61765L0.506089 6.10931C0.347231 5.97163 0.219826 5.80141 0.132508 5.61019C0.0451901 5.41896 0 5.2112 0 5.00098C0 4.79076 0.0451901 4.583 0.132508 4.39177C0.219826 4.20055 0.347231 4.03033 0.506089 3.89265L4.75609 0.384311C5.01195 0.179307 5.32025 0.0502994 5.64587 0.0119922C5.97148 -0.026315 6.3013 0.0276191 6.59776 0.167646C6.84761 0.287265 7.05848 0.475195 7.20596 0.709684C7.35343 0.944174 7.43149 1.21563 7.43109 1.49265Z' fill='%23DFDFE1'/%3E%3C/svg%3E") !important;
        border-top-left-radius: 20px !important;
        border-bottom-left-radius: 20px !important;
    }

    .dataTables_scrollBody:has(.amg-datatable)::-webkit-scrollbar-button:horizontal:increment:end {
        display: block !important;
        width: 16px !important;
        height: 14px !important;
        /* background-color: #f1f1f1 !important; */
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-size: 8px 10px !important;
        background-image: url("data:image/svg+xml,%3Csvg width='8' height='10' viewBox='0 0 8 10' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6.33145e-05 1.49265L6.30078e-05 8.50931C-0.000338024 8.78632 0.0777173 9.05778 0.225195 9.29227C0.372673 9.52676 0.583542 9.71469 0.833396 9.83431C1.12985 9.97434 1.45967 10.0283 1.78529 9.98997C2.1109 9.95166 2.4192 9.82265 2.67506 9.61765L6.92506 6.10931C7.08392 5.97163 7.21133 5.80141 7.29864 5.61019C7.38596 5.41896 7.43115 5.2112 7.43115 5.00098C7.43115 4.79076 7.38596 4.583 7.29864 4.39177C7.21133 4.20055 7.08392 4.03033 6.92506 3.89265L2.67506 0.384311C2.4192 0.179307 2.1109 0.0502994 1.78529 0.0119922C1.45967 -0.026315 1.12985 0.0276191 0.833396 0.167646C0.583543 0.287265 0.372673 0.475195 0.225196 0.709684C0.0777177 0.944174 -0.000337693 1.21563 6.33145e-05 1.49265Z' fill='%23DFDFE1'/%3E%3C/svg%3E") !important;
        border-top-right-radius: 20px !important;
        border-bottom-right-radius: 20px !important;
    }
</style>
@endpush
@push('scripts')
<script src="{!! CommonHelper::asset('js/department/index.js') !!}"></script>
<script>
	var config = new Object;
	config.url = new Object;
	config.url.list     = "{{ url('jx-departments') }}";
	config.url.add      = "{{ url('department/ajaxadd') }}";
	config.url.edit     = "{{ url('department/ajaxedit') }}";
	config.url.get      = "{{ url('department/ajaxget') }}";
	config.url.delete   = "{{ url('department/delete') }}";
	config.company_defulte = {!! json_encode($company) !!};
	config.company_user_detail = {!! json_encode($userDatail) !!};
	config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
	config.permissions = {!! json_encode($permissionArray) !!};
	config.getUserByAjax = "{{ url('getCompanyUsersByQuery') }}";
	config.url.download_url = "{{ url('export-departments') }}";
	config.url.getCustomFieldsetByModule = "{{ url('getCustomFieldsetByModule') }}";
	config.url.import = "{{ url('department-import') }}";
	config.url.getSubCategories = "{{ url('department/getSubCategories') }}";
	config.token = "{{ csrf_token() }}";
	config.roles = {!! json_encode($roles) !!};
	config.translations = {
        select2_no_results: '{{ trans('select2.select2_no_results') }}',
        select2_searching: '{{ trans('select2.select2_searching') }}',
        select2_input_too_short_template: '{{ trans('select2.select2_input_too_short_template') }}',
        select2_loading_more: '{{ trans('select2.select2_loading_more') }}',
        select2_error_loading: '{{ trans('select2.select2_error_loading') }}',
		are_you_delete: '{{ trans('department.alerts_and_messages.are_you_delete') }}',
		something_went_wrong: '{{ trans('department.alerts_and_messages.something_went_wrong') }}',
        create_department: '{{ trans('department.form_fields_and_buttons.create_department') }}',
		edit_department: '{{ trans('department.form_fields_and_buttons.edit_department') }}',
        save: '{{ trans('department.form_fields_and_buttons.save') }}',
		press_enter_with_Search: '{{ trans('config.user_fields.press_enter_with_Search') }}',
		Search:'{{ trans('department.data_table.Search') }}',
        Download:'{{ trans('department.form_fields_and_buttons.download') }}',
        add_department:'{{ trans('department.form_fields_and_buttons.add_department') }}',
		edit: '{{ trans('department.form_fields_and_buttons.edit_department') }}',
		delete: '{{ trans('department.form_fields_and_buttons.delete_department') }}',
		Refresh_List: '{{ trans('department.data_table.refresh_list') }}',
		department_import: '{{ trans('department.form_fields_and_buttons.departments_import') }}',
        previous: '{{ trans('department.data_table.previous') }}',
        next: '{{ trans('department.data_table.next') }}',
        showing_entries: '{{ trans('department.data_table.showing_entries') }}',
        no_entries: '{{ trans('department.data_table.no_entries') }}',
        filtered_from: '{{ trans('department.data_table.filtered_from') }}',
        no_matching_records: '{{ trans('config.data_table.no_matching_records') }}',
        no_data: '{{ trans('department.data_table.no_data') }}',
        search: '{{ trans('department.data_table.search') }}',
        length_menu: '{{ trans('department.data_table.length_menu') }}',
        select_subcategory: '{{ trans('department.placeholder_and_options.select_subcategory') }}',
        deleted_successfully: '{{ trans('department.alerts_and_messages.deleted_successfully') }}',
        saved_successfully: '{{ trans('department.alerts_and_messages.saved_successfully') }}',
        deleted:'{{ trans('department.alerts_and_messages.deleted') }}',
        ok:'{{ trans('department.alerts_and_messages.ok') }}',
        error:'{{ trans('department.alerts_and_messages.error') }}',
        success:'{{ trans('department.alerts_and_messages.success') }}',
        confirm_cancel:'{{ trans('department.alerts_and_messages.confirm_cancel') }}',
        confirm_yes:'{{ trans('department.alerts_and_messages.confirm_yes') }}',
        company_required:'{{ trans('department.alerts_and_messages.company_required') }}',
        valid_department:'{{ trans('department.alerts_and_messages.valid_department') }}',
        select_dept_admin:'{{ trans('department.placeholder_and_options.select_dept_admin') }}',
        select_dept_ast_admin:'{{ trans('department.placeholder_and_options.select_dept_ast_admin') }}',
        select_custom_fieldset:'{{ trans('department.placeholder_and_options.select_custom_fieldset') }}',
        select_attender:'{{ trans('department.placeholder_and_options.select_attender') }}',
        select_dept_head:'{{ trans('department.placeholder_and_options.select_dept_head') }}',
        select_company:'{{ trans('department.placeholder_and_options.select_company') }}',
        select_ast_dept:'{{ trans('department.placeholder_and_options.select_ast_dept') }}',
        select_role:'{{ trans('department.placeholder_and_options.select_role') }}',
        select_account:'{{ trans('department.placeholder_and_options.select_account') }}',
        select_prob_cat:'{{ trans('department.placeholder_and_options.select_prob_cat') }}',
        select_sub_cat:'{{ trans('department.placeholder_and_options.select_sub_cat') }}',

	};
	new leaseAdd(config);
</script>
@endpush
