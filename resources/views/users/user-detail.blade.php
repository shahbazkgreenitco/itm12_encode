{{-- @page-meta
{
  "page_no": "USR13D-26",
  "file": "user-detail.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    },
    {
      "version": "1.0.1",
      "writer": "Prithvi Pillai",
      "from": "2026-06",
      "reviewer": null,
      "description": "Changes for adding the back button for ui consistency, search spacing issues, table columns changes and upload document and filter functionality implementation"
    },
    {
      "version": "1.0.2",
      "writer": "safdar Ali",
      "from": "2026-06",
      "reviewer": null,
      "description": "add buttons for edit, clone, delete, print label and print eula"
    }

  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('user.user_detail.page_title'))
@section('content')
    <style>
        .tab-loader {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 220px;
        }

        .tab-loader .spinner {
            width: 36px;
            height: 36px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #111c2d;
            ;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* #myTabContent table.dataTable thead th { 
            text-align: center !important;
            vertical-align: middle;
        }

        #myTabContent table.dataTable thead th h4 {
            margin: 0;
            text-align: center !important;
        } */

        .amg-list-searchbar__icon-btn {
            border: 0;
            background: transparent;
            padding: 0;
            color: inherit;
            line-height: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            cursor: pointer;
        }

        .amg-list-searchbar__icon-btn .btn-searchbox {
            background: none;
            border: none;
        }

        .amg-list-searchbar__icon-btn:focus-visible {
            outline: 2px solid #111c2d;
            outline-offset: 2px;
            border-radius: 999px;
        }
        #user-mdl-frm .country_code+.select2 .select2-selection__clear {
            display: inline-block !important;
        }

        #user-mdl-frm .country_code+.select2-container {
            flex: 0 0 100px !important;
            width: 100px !important;
        }

        #user-mdl-frm .country_code+.select2-container .select2-selection--single {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
    </style>

    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
    <button onclick="location.href='{{ url('users') }}'"
        class="d-flex gap-2 align-items-center bg-transparent outline-none border-0">
                    <svg width="20" height="20" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                fill="currentColor" />
                    </svg>
        <h2 class="h3-text mb-0">{{ trans('user.user_detail.header_title') }}</h2>
                </button>
    <div class="d-flex gap-8 align-items-center">
        @can("UserEdit")
        @if(is_null($user->merge_primary) && gettype($user->merge_primary) == "NULL" && empty($user->merge_primary))
        <button class="header-icon-btn header-icon-btn-sm dtActEditUser" type="button" data-id="{{ $user->id }}"
            data-bs-toggle="tooltip" title="{{ trans('content.user_fields.Edit_User') }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M21.3103 6.87915L17.1216 2.68946C16.9823 2.55014 16.8169 2.43962 16.6349 2.36421C16.4529 2.28881 16.2578 2.25 16.0608 2.25C15.8638 2.25 15.6687 2.28881 15.4867 2.36421C15.3047 2.43962 15.1393 2.55014 15 2.68946L3.43969 14.2507C3.2998 14.3895 3.18889 14.5547 3.11341 14.7367C3.03792 14.9188 2.99938 15.114 3.00001 15.311V19.5007C3.00001 19.8985 3.15804 20.2801 3.43935 20.5614C3.72065 20.8427 4.10218 21.0007 4.50001 21.0007H8.6897C8.88675 21.0013 9.08197 20.9628 9.26399 20.8873C9.44602 20.8118 9.61122 20.7009 9.75001 20.561L21.3103 9.00071C21.4496 8.86142 21.5602 8.69604 21.6356 8.51403C21.711 8.33202 21.7498 8.13694 21.7498 7.93993C21.7498 7.74292 21.711 7.54784 21.6356 7.36582C21.5602 7.18381 21.4496 7.01844 21.3103 6.87915ZM8.6897 19.5007H4.50001V15.311L12.75 7.06102L16.9397 11.2507L8.6897 19.5007ZM18 10.1895L13.8103 6.00071L16.0603 3.75071L20.25 7.93946L18 10.1895Z"
                    fill="#7F7F7F" />
            </svg>


            <span class="b6-text opacity-50">{{ trans('content.user_fields.Edit_User') }}</span>
        </button>
        @endif
        @endcan

        @can("UserPrintLabel")
        <button class="header-icon-btn header-icon-btn-sm dtActPrintLabel" type="button" data-id="{{ $user->id }}"
            data-bs-toggle="tooltip" title="{{ trans('content.user_fields.print_label') }}">

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M9 6.6V8.4C9 8.73137 8.73137 9 8.4 9H6.6C6.26863 9 6 8.73137 6 8.4V6.6C6 6.26863 6.26863 6 6.6 6H8.4C8.73137 6 9 6.26863 9 6.6Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M6 12H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M15 12V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M12 18H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M12 12.0111L12.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M18 12.0111L18.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M12 15.0111L12.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M18 15.0111L18.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M18 18.0111L18.01 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M12 9.01111L12.01 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M12 6.01111L12.01 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path
                    d="M9 15.6V17.4C9 17.7314 8.73137 18 8.4 18H6.6C6.26863 18 6 17.7314 6 17.4V15.6C6 15.2686 6.26863 15 6.6 15H8.4C8.73137 15 9 15.2686 9 15.6Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path
                    d="M18 6.6V8.4C18 8.73137 17.7314 9 17.4 9H15.6C15.2686 9 15 8.73137 15 8.4V6.6C15 6.26863 15.2686 6 15.6 6H17.4C17.7314 6 18 6.26863 18 6.6Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M18 3H21V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M18 21H21V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M6 3H3V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                <path d="M6 21H3V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>

            <span class="b6-text opacity-50">
                {{ trans('content.user_fields.print_label') }}
            </span>
        </button>
        @endcan

        @can("UserAdd")
        <button class="header-icon-btn header-icon-btn-sm dtActClone" type="button" data-id="{{ $user->id }}"
            data-bs-toggle="tooltip" title="{{ trans('content.user_fields.Clone_User') }}">

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M20.25 3H8.25C8.05109 3 7.86032 3.07902 7.71967 3.21967C7.57902 3.36032 7.5 3.55109 7.5 3.75V7.5H3.75C3.55109 7.5 3.36032 7.57902 3.21967 7.71967C3.07902 7.86032 3 8.05109 3 8.25V20.25C3 20.4489 3.07902 20.6397 3.21967 20.7803C3.36032 20.921 3.55109 21 3.75 21H15.75C15.9489 21 16.1397 20.921 16.2803 20.7803C16.421 20.6397 16.5 20.4489 16.5 20.25V16.5H20.25C20.4489 16.5 20.6397 16.421 20.7803 16.2803C20.921 16.1397 21 15.9489 21 15.75V3.75C21 3.55109 20.921 3.36032 20.7803 3.21967C20.6397 3.07902 20.4489 3 20.25 3ZM15 19.5H4.5V9H15V19.5ZM19.5 15H16.5V8.25C16.5 8.05109 16.421 7.86032 16.2803 7.71967C16.1397 7.57902 15.9489 7.5 15.75 7.5H9V4.5H19.5V15Z"
                    fill="#7F7F7F" />
            </svg>


            <span class="b6-text opacity-50">
                {{ trans('content.user_fields.Clone_User') }}
            </span>
        </button>
        @endcan

        @can("UserDelete")
        <button class="header-icon-btn header-icon-btn-sm dtActDel" type="button" data-id="{{ $user->id }}"
            data-bs-toggle="tooltip" title="{{ trans('content.user_fields.Delete_User') }}">

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M20.25 4.5H16.5V3.75C16.5 3.15326 16.2629 2.58097 15.841 2.15901C15.419 1.73705 14.8467 1.5 14.25 1.5H9.75C9.15326 1.5 8.58097 1.73705 8.15901 2.15901C7.73705 2.58097 7.5 3.15326 7.5 3.75V4.5H3.75C3.55109 4.5 3.36032 4.57902 3.21967 4.71967C3.07902 4.86032 3 5.05109 3 5.25C3 5.44891 3.07902 5.63968 3.21967 5.78033C3.36032 5.92098 3.55109 6 3.75 6H4.5V19.5C4.5 19.8978 4.65804 20.2794 4.93934 20.5607C5.22064 20.842 5.60218 21 6 21H18C18.3978 21 18.7794 20.842 19.0607 20.5607C19.342 20.2794 19.5 19.8978 19.5 19.5V6H20.25C20.4489 6 20.6397 5.92098 20.7803 5.78033C20.921 5.63968 21 5.44891 21 5.25C21 5.05109 20.921 4.86032 20.7803 4.71967C20.6397 4.57902 20.4489 4.5 20.25 4.5ZM9 3.75C9 3.55109 9.07902 3.36032 9.21967 3.21967C9.36032 3.07902 9.55109 3 9.75 3H14.25C14.4489 3 14.6397 3.07902 14.7803 3.21967C14.921 3.36032 15 3.55109 15 3.75V4.5H9V3.75ZM18 19.5H6V6H18V19.5ZM10.5 9.75V15.75C10.5 15.9489 10.421 16.1397 10.2803 16.2803C10.1397 16.421 9.94891 16.5 9.75 16.5C9.55109 16.5 9.36032 16.421 9.21967 16.2803C9.07902 16.1397 9 15.9489 9 15.75V9.75C9 9.55109 9.07902 9.36032 9.21967 9.21967C9.36032 9.07902 9.55109 9 9.75 9C9.94891 9 10.1397 9.07902 10.2803 9.21967C10.421 9.36032 10.5 9.55109 10.5 9.75ZM15 9.75V15.75C15 15.9489 14.921 16.1397 14.7803 16.2803C14.6397 16.421 14.4489 16.5 14.25 16.5C14.0511 16.5 13.8603 16.421 13.7197 16.2803C13.579 16.1397 13.5 15.9489 13.5 15.75V9.75C13.5 9.55109 13.579 9.36032 13.7197 9.21967C13.8603 9.07902 14.0511 9 14.25 9C14.4489 9 14.6397 9.07902 14.7803 9.21967C14.921 9.36032 15 9.55109 15 9.75Z"
                    fill="#7F7F7F" />
            </svg>


            <span class="b6-text opacity-50">
                {{ trans('content.user_fields.Delete_User') }}
            </span>
        </button>
        @endcan


        <button class="header-icon-btn header-icon-btn-sm dtActPrint" type="button" data-id="{{ $user->id }}"
            data-bs-toggle="tooltip" title="{{ trans('content.user_fields.print_eula') }}">

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M20.1253 6.75H18.75V3.75C18.75 3.55109 18.671 3.36032 18.5303 3.21967C18.3897 3.07902 18.1989 3 18 3H6C5.80109 3 5.61032 3.07902 5.46967 3.21967C5.32902 3.36032 5.25 3.55109 5.25 3.75V6.75H3.87469C2.565 6.75 1.5 7.75969 1.5 9V16.5C1.5 16.6989 1.57902 16.8897 1.71967 17.0303C1.86032 17.171 2.05109 17.25 2.25 17.25H5.25V20.25C5.25 20.4489 5.32902 20.6397 5.46967 20.7803C5.61032 20.921 5.80109 21 6 21H18C18.1989 21 18.3897 20.921 18.5303 20.7803C18.671 20.6397 18.75 20.4489 18.75 20.25V17.25H21.75C21.9489 17.25 22.1397 17.171 22.2803 17.0303C22.421 16.8897 22.5 16.6989 22.5 16.5V9C22.5 7.75969 21.435 6.75 20.1253 6.75ZM6.75 4.5H17.25V6.75H6.75V4.5ZM17.25 19.5H6.75V15H17.25V19.5ZM21 15.75H18.75V14.25C18.75 14.0511 18.671 13.8603 18.5303 13.7197C18.3897 13.579 18.1989 13.5 18 13.5H6C5.80109 13.5 5.61032 13.579 5.46967 13.7197C5.32902 13.8603 5.25 14.0511 5.25 14.25V15.75H3V9C3 8.58656 3.39281 8.25 3.87469 8.25H20.1253C20.6072 8.25 21 8.58656 21 9V15.75ZM18.75 10.875C18.75 11.0975 18.684 11.315 18.5604 11.5C18.4368 11.685 18.2611 11.8292 18.0555 11.9144C17.85 11.9995 17.6238 12.0218 17.4055 11.9784C17.1873 11.935 16.9868 11.8278 16.8295 11.6705C16.6722 11.5132 16.565 11.3127 16.5216 11.0945C16.4782 10.8762 16.5005 10.65 16.5856 10.4445C16.6708 10.2389 16.815 10.0632 17 9.9396C17.185 9.81598 17.4025 9.75 17.625 9.75C17.9234 9.75 18.2095 9.86853 18.4205 10.0795C18.6315 10.2905 18.75 10.5766 18.75 10.875Z"
                    fill="#7F7F7F" />
            </svg>


            <span class="b6-text opacity-50">
                {{ trans('content.user_fields.print_eula') }}
            </span>
        </button>

       </div>
    </div>
  
    <main class="main-content" id="mainContent">
        <div class="tab-bar">
            <ul class="nav nav-underline" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-expanded="true">
                        <span>{{ trans('user.user_detail.tabs.overview') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="permissions-tab" data-bs-toggle="tab" href="#permissions" role="tab" aria-controls="link1">
                        <span>{{ trans('user.user_detail.tabs.permissions') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="assets-tab" data-bs-toggle="tab" href="#assets" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.assets') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="license-tab" data-bs-toggle="tab" href="#license" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.license') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="accessories-tab-link" data-bs-toggle="tab" href="#accessories" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.accessories') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="consumables-tab-link" data-bs-toggle="tab" href="#consumables" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.consumables') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="components-tab-link" data-bs-toggle="tab" href="#components" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.components') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab-link" data-bs-toggle="tab" href="#documents" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.documents') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="history-tab-link" data-bs-toggle="tab" href="#history" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.history') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="user-history-tab-link" data-bs-toggle="tab" href="#user-history" role="tab" aria-controls="link2">
                        <span>{{ trans('user.user_detail.tabs.user_history') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content tabcontent-border p-3" id="myTabContent">
            <!-- overview tab 1 -->
            <div role="tabpanel" class="tab-pane fade show active" id="overview" aria-labelledby="overview-tab">
                <div class="tab-loader d-flex justify-content-center">
                    <div class="spinner"></div>
                </div>
            </div>

            <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
                <div class="tab-loader d-flex justify-content-center">
                    <div class="spinner"></div>
                </div>
            </div>

            <div class="tab-pane fade" id="assets" role="tabpanel" aria-labelledby="assets-tab">
                <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth assets-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>

                    <!-- spacer -->
                    <div class="flex-grow-1"></div>
                    <!-- searchbar -->
                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="assets" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input assets-search-input js-user-detail-search-input" data-tab-key="assets" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn assets-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="assets">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>

                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="device-tab" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><h4>{{ trans('user.user_detail.assets_table.device_tag') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.device_name') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.model_name') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.manufacturer') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.serial') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.project_name') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.checkout_date') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.ni_updated') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.assets_table.actions') }}</h4></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="license" role="tabpanel" aria-labelledby="license-tab">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth license-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>
                    <div class="flex-grow-1"></div>

                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="license" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input license-search-input js-user-detail-search-input" data-tab-key="license" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn license-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="license">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="licenses-tab" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><h4>{{ trans('user.user_detail.license_table.license') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.license_table.serial') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.license_table.checkout_via') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.license_table.actions') }}</h4></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="accessories" role="tabpanel" aria-labelledby="accessories-tab-link">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <!-- show select -->
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth accessories-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>
                    <div class="flex-grow-1"></div>

                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="accessories" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input accessories-search-input js-user-detail-search-input" data-tab-key="accessories" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn accessories-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="accessories">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="accessories-tab" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><h4>{{ trans('user.user_detail.accessories_table.name') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.accessories_table.expected_checkin_date') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.accessories_table.note') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.accessories_table.action') }}</h4></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="consumables" role="tabpanel" aria-labelledby="consumables-tab-link">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <!-- show select -->
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth consumables-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>
                    <!-- spacer -->
                    <div class="flex-grow-1"></div>

                    <!-- searchbar -->
                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="consumables" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input consumables-search-input js-user-detail-search-input" data-tab-key="consumables" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn consumables-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="consumables">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="consumables-tab" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><h4>{{ trans('user.user_detail.consumables_table.name') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.consumables_table.updated_at') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.consumables_table.action') }}</h4></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="components" role="tabpanel" aria-labelledby="components-tab-link">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <!-- show select -->
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth components-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>
                    <div class="flex-grow-1"></div>

                    <!-- searchbar -->
                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="components" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input components-search-input js-user-detail-search-input" data-tab-key="components" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn components-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="components">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="components-tab" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><h4>{{ trans('user.user_detail.components_table.name') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.components_table.expected_checkin_date') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.components_table.action') }}</h4></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab-link">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <!-- show select -->
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth documents-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>
                    <div class="flex-grow-1"></div>
                    @can('UserDocumentsUpload')
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-upload-document" type="button" data-bs-toggle="tooltip" aria-label="Upload Document" data-bs-original-title="Upload Document" id="btn_upload_document">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 20L18 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M12 16V4M12 4L15.5 7.5M12 4L8.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    @endcan
                    <!-- searchbar -->
                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="documents" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input documents-search-input js-user-detail-search-input" data-tab-key="documents" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn documents-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="documents">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>

                <div class="table-responsive" id="main-user-list-wrapper" style="overflow-y:hidden;">
                    <table id="documents-tab" class="table display" style="width:100%">
                        <thead>
                            <tr>
                                <th><h4>{{ trans('user.user_detail.documents_table.document_name') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.documents_table.updated_on') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.documents_table.notes') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.documents_table.tagging') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.documents_table.action') }}</h4></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab-link">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <!-- show select -->
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth history-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>

                    <!-- spacer -->
                    <div class="flex-grow-1"></div>
                        {{-- filter --}}
                        <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip" data-bs-original-title="Filter">
                            <svg viewBox="0 0 20 18" fill="none">
                                <path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z" fill="currentColor"></path>
                            </svg>
                            <span class="b3-text opacity-50">Filter</span>
                            <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                        </button>
                    <!-- searchbar -->
                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="history" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input history-search-input js-user-detail-search-input" data-tab-key="history" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn history-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="history">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>

                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="history-tab" class="table display history-table" style="width:100%">
                        <thead>
                            <tr>
                                <th><h4>{{ trans('user.user_detail.history_table.date') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.history_table.admin') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.history_table.asset_type') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.history_table.actions_type') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.history_table.entity') }}</h4></th>
                                <th><h4>{{ trans('user.user_detail.history_table.notes') }}</h4></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="user-history" role="tabpanel" aria-labelledby="user-history-tab-link">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <!-- show select -->
                    <div class="col-auto">
                        <select class="amg-table-pagination-dropdown userModulePageLenth user-history-page-length">
                            <option value="10" selected>Show (10)</option>
                            <option value="25">Show (25)</option>
                            <option value="50">Show (50)</option>
                            <option value="100">Show (100)</option>
                        </select>
                    </div>
                    <!-- spacer -->
                    <div class="flex-grow-1"></div>
                    <!-- searchbar -->
                    <div>
                        <div class="amg-list-searchbar">
                            <button type="button" class="amg-list-searchbar__icon-btn js-user-detail-search-button" data-tab-key="user-history" aria-label="{{ trans('user.user_toolbar.search') }}" title="{{ trans('user.user_toolbar.search') }}">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <input type="text" class="amg-list-searchbar__input user-history-search-input js-user-detail-search-input" data-tab-key="user-history" placeholder="Search..." autocomplete="off">
                        </div>
                    </div>

                    <!-- refresh button -->
                    <button type="button" class="amg-refresh-btn user-history-refresh-button btn-reload-list js-user-detail-refresh-button" data-tab-key="user-history">
                        <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="#7F7F7F"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
                <div class="table-responsive" style="overflow-y:hidden;">
                    <table id="user-history-tab" class="amg-datatable table display" style="width:100%">
                        <thead>
                            <tr>
                                <th>
                                    <h4>{{ trans('user.user_detail.user_history_table.event') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('user.user_detail.user_history_table.performed_by') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('user.user_detail.user_history_table.date') }}</h4>
                                </th>
                                <th>
                                    <h4>{{ trans('user.user_detail.user_history_table.actions') }}</h4>
                                </th>

                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
        @include("documents.upload_modal")
        @include('users.asset-history-filter-modal')
        @include('users.add-modal')
        @include("users.user_history_modal") 
        <a id="print_label_starter" target="_blank" href="" class="hidden"></a>
        <input type="hidden" id="auth_user" value="{{ json_encode(Auth::check() ? Auth::user() : null) }}">
        @include('users.print-modal')
        @include('users.company-modal')
    </main>



@endsection

@push('scripts')
    <script src="{!! CommonHelper::asset('js/users/info.js') !!}"></script>
    <script src="{{ asset('assets/scripts/plugins/toastr-init.js') }}"></script>
    <script src="{{ CommonHelper::asset('public\assets\libs\sweetalert2\dist\sweetalert2.all.min.js') }}"></script>
    <script>
        var config = {};
        config.url = {};
        config.url.add = "{{ url('user/add') }}";
        config.url.checkuser = "{{ url('user/checkUser') }}";
        config.imgviewpath = "{{ url('storage/avatar') }}";
        config.url.edit = "{{ url('user/edit') }}";
        config.url.userDelete = "{{ url('user/delete') }}";
        config.url.getUser = "{{ url('user/getUserForEdit') }}";
        config.url.departments = "{{ url('departments/by-company') }}";
        config.url.users = "{{ url('users') }}";
        config.url.user_info = "{{ url('user/info') }}";
        config.url.user_basic_info = "{{ url('user/basic-info') }}";
        config.url.assigned_devices = "{{ url('user/jx-assigned-devices') }}";
        config.url.device_info = "{{ url('device/info') }}";
        config.url.component_info = "{{ url('component/info') }}";
        config.url.assigned_licenses = "{{ url('user/jx-assigned-licenses') }}";
        config.url.license_checkin = "{{ url('licenseseat/checkin') }}";
        config.url.license_detail = "{{ url('license/detail') }}";
        config.url.assigned_accessories = "{{ url('user/jx-assigned-accessories') }}";
        config.url.accessory_info = "{{ url('accessory/info') }}";
        config.url.assigned_consumables = "{{ url('user/jx-assigned-consumables') }}";
        config.url.assigned_components = "{{ url('user/jx-assigned-components') }}";
        config.url.consumable_info = "{{ url('consumable-info') }}";
        config.url.licenseSeatCheckin = "{{ url('licenseseat/checkin')}}";
        config.url.documents = "{{ url('document/jx-documents') }}";
        config.url.document_upload = "{{ url('document/jx-upload') }}";
        config.url.document_download = "{{ url('uploads/documents') }}";
        config.url.doc_download = "{{ url('download/documents') }}";
        config.url.document_delete = "{{ url('document/delete') }}";
        config.url.user_history = "{{ url('user/history') }}";
        config.url.make_logout = "{{ route('makeLogout', [$user->id, ':session_id']) }}";
        config.url.load_visits = "{{ url('api/visits/load-visits') }}";
        config.url.print = "{{ config('app.client') }}" == "knightfrank" ? "{{ url('user/printInOut') }}" :
            "{{ url('user/print') }}";
        config.url.print_user_barcode = "{{ url('print-user-barcode') }}";
        config.url.print_user_onecol = "{{ url('print-user-one-col') }}";
        config.url.print_user_twocol = "{{ url('print-user-two-col') }}";
        config.url.print_user_verticalcol = "{{ url('print-user-vertical-col') }}";    
        config.getManagerByAjax = "{{ url('getUserByQuery') }}";
        config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
        config.getGroupByAjax = "{{ url('getGroupByQuery') }}";
        config.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
        config.companies = {!! json_encode($companies) !!};
        config.countries_data = @json($country_data);
        config.url.getCompanyUsers = "{{ url('getCompanyUsers') }}";
        config.token = "{{ csrf_token() }}";
        config.user_id = "{{ $user->id }}";
        config.company_id = "{{ isset($_GET['company_id']) ? $_GET['company_id'] : '' }}";
        config.url.jx_tickets = "{{ url('tickets/ajax-lists') }}";
        config.url.init_ticket = "{{ url('tickets/init') }}";
        config.url.create_ticket = "{{ url('tickets/create') }}";
        config.url.update_status = "{{ url('ticket/update_status') }}";
        config.url.delete = "{{ url('ticket/delete') }}";
        config.url.delete_multiple = "{{ url('ticket/delete_multiple_ticket') }}";
        config.url.resolved_multiple = "{{ url('ticket/resolved_multiple_ticket') }}";
        config.url.select_multiple = "{{ url('ticket/select_multiple_ticket') }}";
        config.url.add_comment = "{{ url('ticket/add_comment') }}";
        config.url.mytickets = "{{ url('tickets/newlist/my-tickets') }}";
        config.url.get_tickets = "{{ url('tickets/list/jx-ticket-detail') }}";
        config.url.transfer = "{{ url('ticket/transfer') }}";
        config.url.get_self_assign_mode = "{{ url('ticket/get-self-assign-mode') }}";
        config.url.add_feedback = "{{ url('ticket/feedback/add') }}";
        config.url.get_data_for_transfer = "{{ url('ticket/get-data-for-transfer') }}";
        config.url.get_timeline = "{{ url('ticket/get_timeline') }}";
        config.url.create_ticket_by_user = "{{ url('tickets/create-by-user') }}";
        config.url.get_users_to_assign = "{{ url('ticket/get_users_to_assign') }}";
        config.url.get_users_to_assign_by_dep = "{{ url('ticket/get_users_to_assign_by_dep') }}";
        config.url.assign_to = "{{ url('ticket/assign_to') }}";
        config.url.self_assign = "{{ url('ticket/self_assign') }}";
        config.url.change_creator = "{{ url('ticket/change_creator') }}";
        config.url.reopen = "{{ url('ticket/reopen') }}";
        config.url.staring = "{{ url('ticket/staring') }}";
        config.url.spam = "{{ url('ticket/spam') }}";
        config.url.current_page = "{{ url('ticket/') }}";
        config.url.view_ticket = "{{ url('ticket') }}";
        config.url.export_tickets = "{{ url('tickets/export') }}";
        config.url.user_basic_info = "{{ url('user/basic-info') }}";
        config.url.departments_by_company = "{{ url('departments/by-company') }}";
        config.url.departments_with_company = "{{ url('tickets/departments') }}";
        config.url.departments_based_on_privilage = "{{ url('departments/by-company/privilage') }}";
        config.url.service_types_by_company = "{{ url('tickets/service-types/by-dept') }}";
        config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
        config.url.attachment_add = "{{ url('ticket/attachment/add') }}";
        config.url.attachment_remove = "{{ url('ticket/attachment/remove') }}";
        config.url.getUserByAjax = "{{ url('getUserByQuery') }}";
        config.url.get_users_to_assign_by_dep_by_availability =
            "{{ url('ticket/get_users_to_assign_by_dep_by_availability') }}";
        config.url.getDeviceByAjax = "{{ url('getDeviceForDropDown') }}";
        config.url.getUserDeviceByAjax = "{{ url('getUserDeviceForDropDown') }}";
        config.url.getDeviceFilterByAjax = "{{ url('getDeviceForTicketDropDown') }}";
        config.url.merge_tickets = "{{ url('tickets/merge_tickets') }}";
        config.url.transfer = "{{ url('ticket/transfer') }}";
        config.url.getTagDetails = "{{ url('ticket/getTagDetails') }}";
        config.url.departments_new = "{{ url('getDepartmentsWithCompanyByQuery') }}";
        config.url.editTicket = "{{ url('ticket/edit') }}";
        config.url.getTechCurrentStatusById = "{{ url('technician/get-tech-curren-status-id') }}";
        config.url.get_articles = "{{ url('tickets/list/jx-article-detail') }}";
        config.url.user_permissions = "{{ url('user-permissions') }}";
        config.url.user_base_history = "{{ url('user-history') }}";
        config.url.user_history_details = "{{ url('user-history-details') }}";
        config.url.service_request_form = "{{ url('tickets/serviceRequestForm') }}";
        config.url.requested_form = "{{ url('requested_form') }}";
        config.url.requestInfo = "{{ url('tickets/requestInfo') }}";
        config.url.getUserCCByAjax = "{{ url('getUserCCByQuery') }}";
        config.url.ticket_type = "{{ url('tickets/getTicketTypeByDepartment') }}";
        config.url.getAssetDepartments = "{{ route('getAssetDepartments') }}";
        config.url.getUsersParameterBase = "{{ url('getUsersParameterBase') }}";
        config.main_filter = "{{ $main_filter }}";
        config.token = "{{ csrf_token() }}";
        config.ac_email_accounts = {!! json_encode($ac_email_accounts) !!};
        config.url.update_master_cc = "{{ url('tickets/update-master-cc') }}";
        config.url.ticket_history = "{{ url('ticket/ticket_history') }}";
        config.url.attachment_view = "{{ url('document-attachment/view') }}";
        config.create_form = {!! isset($form) && $form ? "'a';" : "'m';" !!}
        config.user = {!! json_encode(Auth::user()->only('id', 'first_name', 'last_name', 'username', 'company_id')) !!};
        config.auth = {!! json_encode(Auth::user()->only('id')) !!};
        config.user.limits = "{{ Auth::user()->hasPermission('service_tickets') }}";
        config.user.action_controls = {!! json_encode($action_controls) !!};
        config.companies = {!! json_encode($companies) !!};
        {{-- config.device = {!! json_encode($device) !!}; --}}
        config.statuses = {!! json_encode($statuses) !!};
        config.statuses1 = {!! json_encode($statuses1) !!};
        config.priorities = {!! json_encode($priorities) !!};
        config.ticket_creator = {!! json_encode($ticket_creators) !!};
        config.ticket_handlers = {!! json_encode($ticket_handlers) !!};
        config.tkt_config = {!! json_encode($tkt_config) !!};
        config.holidays = {!! json_encode($holidays) !!};
        config.sort_fields = {!! json_encode($sort_fields) !!};
        config.action_controls = {!! json_encode($action_controls) !!};
        config.url.site_url = "{{ url('/') }}";
        config.departments = {!! json_encode($departments) !!};
        config.created_via = {!! json_encode($created_via) !!};
        config.is_admin = {!! json_encode($is_admin) !!};
        config.roles = {!! json_encode($roles) !!};
        config.created_via_filter = {!! json_encode($created_via_filter) !!};
        config.locations = {!! json_encode($locations) !!};
        config.base_locations = {!! json_encode($base_locations) !!};
        config.sort_dir = {
            id: 7,
            dir: 2
        };
        config.translations = {
            Accessory_Name:'{{ trans('user.user_detail.translations.Accessory_Name') }}',
            Accessory_Tag:'{{ trans('user.user_detail.translations.Accessory_Tag') }}',
            Device_Tag:'{{ trans('user.user_detail.translations.Device_Tag') }}',
            Device_Model:'{{ trans('user.user_detail.translations.Device_Model') }}',
            License_Tag:'{{ trans('user.user_detail.translations.License_Tag') }}',
            License_Name:'{{ trans('user.user_detail.translations.License_Name') }}',
            Consumable_Tag:'{{ trans('user.user_detail.translations.Consumable_Tag') }}',
            Consumable_Name:'{{ trans('user.user_detail.translations.Consumable_Name') }}',
            Component_Tag: '{{ trans('user.user_detail.translations.Component_Tag')}}',
            Component_Name: '{{ trans('user.user_detail.translations.Component_Name')}}',
            New_Document_Upload: '{{ trans('user.user_detail.translations.new_document_upload') }}',
            something_went_wrong: '{{ trans('user.user_detail.translations.something_went_wrong') }}',
            Download_Document: '{{ trans('user.user_detail.translations.Download_Document') }}',
            Delete_Document: '{{ trans('user.user_detail.translations.Delete_Document') }}',
            are_you_sure_delete_msg: '{{ trans("user.user_detail.translations.are_you_sure_delete_msg") }}'
        }
        new MyApp(config);
    </script>
@endpush
