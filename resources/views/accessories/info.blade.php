{{--
/**
------------------------------------------------------------
File: info.blade.php
Module: Accessories
ACC/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-015
Created On: 2026-05-12
Reviewed By: -
------------------------------------------------------------
Purpose:
Accessories Complete Information Management Page

------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('accessories.accessory_fields.accessory_info'))
@section('content')
<section class="content">
    <div id="main-user-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <button onclick="location.href='{{ url('accessories') }}'"
                class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">
                <svg width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z"
                        fill="currentColor" />
                </svg>
                <h3 class="h3-text mb-0">{{ trans('accessories.accessory_fields.accessory_info') }}</h3>
            </button>
            <div class="d-flex gap-8 align-items-center">
                {{-- EDIT --}}
                @can("AccessoriesEdit")
                <button class="header-icon-btn-only header-icon-btn-only-sm dtActEdit" type="button"
                    data-id="{{ $accessory->id }}" data-bs-toggle="tooltip"
                    title="{{ trans('accessories.accessory_fields.edit_accessory') }}">

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M3 17.25V21H6.75L17.81 9.94L14.06 6.19L3 17.25Z" fill="currentColor" />
                        <path
                            d="M20.71 7.04C21.1 6.65 21.1 6.02 20.71 5.63L18.37 3.29C17.98 2.9 17.35 2.9 16.96 3.29L15.13 5.12L18.88 8.87L20.71 7.04Z"
                            fill="currentColor" />
                    </svg>
                </button>
                @endcan
                {{-- CLONE --}}
                @can("AccessoriesAdd")
                <button class="header-icon-btn-only header-icon-btn-only-sm dtActClone" type="button"
                    data-id="{{ $accessory->id }}" data-bs-toggle="tooltip"
                    title="{{ trans('accessories.accessory_fields.Clone_Accessory') }}">

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M8 8H5C3.9 8 3 8.9 3 10V19C3 20.1 3.9 21 5 21H14C15.1 21 16 20.1 16 19V16"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <rect x="8" y="3" width="13" height="13" rx="2" stroke="currentColor" stroke-width="2" />
                    </svg>
                </button>
                @endcan
                {{-- DELETE --}}
                @can("AccessoriesDelete")
                <button class="header-icon-btn-only header-icon-btn-only-sm dtActDel" type="button"
                    data-id="{{ $accessory->id }}" data-bs-toggle="tooltip"
                    title="{{ trans('accessories.accessory_fields.Delete_Accessory') }}">

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M8 6V4C8 2.9 8.9 2 10 2H14C15.1 2 16 2.9 16 4V6" stroke="currentColor"
                            stroke-width="2" />
                        <path d="M19 6V20C19 21.1 18.1 22 17 22H7C5.9 22 5 21.1 5 20V6" stroke="currentColor"
                            stroke-width="2" />
                    </svg>
                </button>
                @endcan
                {{-- PRINT --}}
                @can("AccessoriesDownload")
                {{-- <button class="header-icon-btn-only header-icon-btn-only-sm btnActPrintLabel" type="button"
                    data-id="{{ $accessory->id }}" data-bs-toggle="tooltip"
                    title="{{ trans('accessories.accessory_fields.Print_History') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9V2H18V9" stroke="currentColor" stroke-width="2" />
                        <path d="M6 18H18V22H6V18Z" stroke="currentColor" stroke-width="2" />
                        <path d="M6 14H18" stroke="currentColor" stroke-width="2" />
                    </svg>
                </button> --}}
                @endcan
            </div>
        </div>
        <main class="main-content" id="mainContent">
            <div class="tab-bar">
                <ul class="nav nav-underline" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#info-tab">
                            <span>{{ trans('accessories.accessory_fields.basic_info') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#users-tab">
                            <span>{{ trans('accessories.accessory_fields.checkouts') }}</span>
                        </a>
                    </li>
                    @can("AccessoriesDocumentsUpload")
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#documents-tab">
                            <span>{{ trans('accessories.accessory_fields.documents') }}</span>
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#purchase-tab">
                            <span>{{ trans('accessories.accessory_fields.purchase') }}</span>
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link" data-bs-toggle="tab" href="#history-tab">
                            <span>{{ trans('accessories.accessory_fields.history') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content tabcontent-border p-3" id="myTabContent">
                {{-- BASIC INFO --}}
                <div class="tab-pane fade show active" id="info-tab">

                    <div class="tab-loader py-5 text-center">

                        <div class="spinner-border text-danger"></div>

                    </div>

                </div>

                {{-- USERS TAB --}}
                <div class="tab-pane fade" id="users-tab">

                    <div class="d-flex align-items-center gap-2 mb-4">

                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown userModulePageLenth users-page-length">
                                <option value="10">{{ trans('accessories.accessory_fields.show') }} (10)</option>
                                <option value="25">{{ trans('accessories.accessory_fields.show') }} (25)</option>
                                <option value="50">{{ trans('accessories.accessory_fields.show') }} (50)</option>
                                <option value="100">{{ trans('accessories.accessory_fields.show') }} (100)</option>
                            </select>
                        </div>

                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input users-search-input"
                                    placeholder="{{ trans('accessories.accessory_fields.Search') }}...">
                            </div>

                        </div>
                        <button type="button" class="amg-refresh-btn users-refresh-button">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="#7F7F7F"></path>
                            </svg>
                            <span>{{ trans('accessories.accessory_fields.Refresh_List') }}</span>
                        </button>


                        {{-- Download Excel --}}
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-users-export" type="button" id="btn_export"
                            data-bs-toggle="tooltip" title="{{ trans('accessories.accessory_fields.Download') }}">
                            <svg viewBox="0 0 18 18" fill="none">
                                <path
                                    d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z"
                                    fill="currentColor" />
                            </svg>
                        </button>

                        {{-- Download pdf --}}
                        <button class="header-icon-btn-only header-icon-btn-only-sm btn-export-accessories-pdf" id="btn_pdf"
                            type="button" data-bs-toggle="tooltip"
                            title="{{ trans('accessories.accessory_fields.Download_PDF') }}">
                            <svg width="17" height="20" viewBox="0 0 17 20" fill="none">
                                <path
                                    d="M16.2806 5.46938L11.0306 0.219375C10.9609 0.149749 10.8782 0.094539 10.7871 0.0568979C10.6961 0.0192569 10.5985 -7.72394e-05 10.5 2.31899e-07H1.5C1.10218 2.31899e-07 0.720644 0.158035 0.43934 0.43934C0.158035 0.720645 0 1.10218 0 1.5V18C0 18.3978 0.158035 18.7794 0.43934 19.0607C0.720644 19.342 1.10218 19.5 1.5 19.5H15C15.3978 19.5 15.7794 19.342 16.0607 19.0607C16.342 18.7794 16.5 18.3978 16.5 18V6C16.5001 5.90148 16.4807 5.80391 16.4431 5.71286C16.4055 5.62182 16.3503 5.53908 16.2806 5.46938ZM11.25 2.56031L13.9397 5.25H11.25V2.56031ZM15 18H1.5V1.5H9.75V6C9.75 6.19891 9.82902 6.38968 9.96967 6.53033C10.1103 6.67098 10.3011 6.75 10.5 6.75H15V18ZM11.0306 12.2194C11.1004 12.289 11.1557 12.3717 11.1934 12.4628C11.2312 12.5538 11.2506 12.6514 11.2506 12.75C11.2506 12.8486 11.2312 12.9462 11.1934 13.0372C11.1557 13.1283 11.1004 13.211 11.0306 13.2806L8.78063 15.5306C8.71097 15.6004 8.62825 15.6557 8.5372 15.6934C8.44616 15.7312 8.34856 15.7506 8.25 15.7506C8.15144 15.7506 8.05384 15.7312 7.96279 15.6934C7.87175 15.6557 7.78903 15.6004 7.71937 15.5306L5.46937 13.2806C5.32864 13.1399 5.24958 12.949 5.24958 12.75C5.24958 12.551 5.32864 12.3601 5.46937 12.2194C5.61011 12.0786 5.80098 11.9996 6 11.9996C6.19902 11.9996 6.38989 12.0786 6.53063 12.2194L7.5 13.1897V9C7.5 8.80109 7.57902 8.61032 7.71967 8.46967C7.86032 8.32902 8.05109 8.25 8.25 8.25C8.44891 8.25 8.63968 8.32902 8.78033 8.46967C8.92098 8.61032 9 8.80109 9 9V13.1897L9.96937 12.2194C10.039 12.1496 10.1217 12.0943 10.2128 12.0566C10.3038 12.0188 10.4014 11.9994 10.5 11.9994C10.5986 11.9994 10.6962 12.0188 10.7872 12.0566C10.8783 12.0943 10.961 12.1496 11.0306 12.2194Z"
                                    fill="currentColor" />
                            </svg>
                        </button>

                    </div>

                    <div class="table-responsive">
                        <table id="tblUser" class="table display app-data-table">
                            <thead>
                                <tr>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.user_place_device") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.checkout_to") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.expected_checkin_date") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.notes") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.actions") }}</h4>
                                    </th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                </div>

                {{-- DOCUMENT TAB --}}
                <div class="tab-pane fade" id="documents-tab">
                    <div class="d-flex align-items-center gap-2 mb-4">

                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown userModulePageLenth documents-page-length">
                                <option value="10">{{ trans('accessories.accessory_fields.show') }} (10)</option>
                                <option value="25">{{ trans('accessories.accessory_fields.show') }} (25)</option>
                                <option value="50">{{ trans('accessories.accessory_fields.show') }} (50)</option>
                                <option value="100">{{ trans('accessories.accessory_fields.show') }} (100)</option>
                            </select>
                        </div>

                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input documents-search-input"
                                    placeholder="{{ trans('accessories.accessory_fields.Search') }}...">
                            </div>

                        </div>
                        <button type="button" class="amg-refresh-btn documents-refresh-button">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="#7F7F7F"></path>
                            </svg>
                            <span>{{ trans('accessories.accessory_fields.Refresh_List') }}</span>
                        </button>


                        {{-- UPLOAD --}}
                        @can("AccessoriesDocumentsUpload")
                        <button class="amg-btn amg-btn-primary amg-btn-md btnUploadDocument btn-upload-document"
                            type="button" data-bs-toggle="tooltip" data-bs-original-title="{{ trans("accessories.accessory_fields.Upload_Document") }}">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans("accessories.accessory_fields.Upload_Document") }}</span>
                        </button>
                        @endcan

                    </div>

                    <div class="table-responsive">
                        <table id="tblDocument" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.document_name") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.uploaded_on") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.notes") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.actions") }}</h4>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>

                {{-- PURCHASE TAB --}}
                <div class="tab-pane fade" id="purchase-tab">
                    <div class="d-flex align-items-center gap-2 mb-4">

                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown userModulePageLenth purchase-page-length"
                                id="purchase-page-length">
                                <option value="10">{{ trans('accessories.accessory_fields.show') }} (10)</option>
                                <option value="25">{{ trans('accessories.accessory_fields.show') }} (25)</option>
                                <option value="50">{{ trans('accessories.accessory_fields.show') }} (50)</option>
                                <option value="100">{{ trans('accessories.accessory_fields.show') }} (100)</option>
                            </select>
                        </div>

                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input purchase-search-input"
                                    placeholder="{{ trans('accessories.accessory_fields.Search') }}">
                            </div>
                        </div>
                        <button type="button" class="amg-refresh-btn purchase-refresh-button">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="#7F7F7F"></path>
                            </svg>
                            <span>{{ trans('accessories.accessory_fields.Refresh_List') }}</span>
                        </button>
                        {{-- UPLOAD --}}
                        <button class="amg-btn amg-btn-primary amg-btn-md btn-add-purchase" type="button"
                            data-bs-toggle="tooltip" data-bs-original-title="Add Purchase">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('purchase.purchase_fields.add_purchase') }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tblPurchase" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.batch_no") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.purchase_date") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.po_number") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.purchase_by") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.received_date") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.qty") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.price") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.location") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.department") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.updated_on") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.actions") }}</h4>
                                    </th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                </div>

                {{-- HISTORY TAB --}}
                <div class="tab-pane fade" id="history-tab">
                    <div class="d-flex align-items-center gap-2 mb-4">

                        <div class="col-auto">
                            <select class="amg-table-pagination-dropdown userModulePageLenth history-page-length">
                                <option value="10">{{ trans('accessories.accessory_fields.show') }} (10)</option>
                                <option value="25">{{ trans('accessories.accessory_fields.show') }} (25)</option>
                                <option value="50">{{ trans('accessories.accessory_fields.show') }} (50)</option>
                                <option value="100">{{ trans('accessories.accessory_fields.show') }} (100)</option>
                            </select>
                        </div>

                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="20" height="20" viewBox="0 0 20 20"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"
                                        fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input history-search-input"
                                    placeholder="{{ trans('accessories.accessory_fields.Search') }}...">
                            </div>

                        </div>
                        <button type="button" class="amg-refresh-btn history-refresh-button">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z"
                                    fill="#7F7F7F"></path>
                            </svg>
                            <span>{{ trans('accessories.accessory_fields.Refresh_List') }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tblHistory" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.date") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.updater") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.actions") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.user_place_device") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.checkout_type") }}</h4>
                                    </th>
                                    <th>
                                        <h4>{{ trans("accessories.accessory_fields.notes") }}</h4>
                                    </th>
                                    <th>
                                        <h4>View</h4>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    @include("accessories.checkin_modal")
    @include("accessories.checkout_modal")
    @include("accessories.scrap_modal")
    @include("accessories.revert_scrap_modal")
    @include("accessories.expected_checkin_date")
    @include("accessories.modal_html")
    @include("documents.upload_modal")
    @include('accessories.purchase-modal')
    @include('accessories.change-info')

</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/plugins/print/jQuery.print.js') }}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/accessories/info/index.js') !!}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var config = new Object;
        config.url = new Object;
        config.imgviewpath = "{{ url('uploads/accessories') }}";
        config.url.accessories = "{{ url('accessories') }}";
        config.url.add = "{{ url('accessory/add') }}";
        config.url.edit = "{{ url('accessory/edit') }}";
        config.url.checkout = "{{ url('accessory/checkout') }}";
        config.url.checkin = "{{ url('accessory/checkin') }}";
        config.url.scrap = "{{ url('accessory/scrap') }}";
        config.url.revertScrap = "{{ url('accessory/revertScrap') }}";
		config.url.delete = "{{ url('accessory/delete') }}";
        config.url.attachment_view  = "{{ url('accessory/document') }}";
		config.url.get = "{{ url('accessory/get') }}";
        config.url.info = "{{ url('accessory/info') }}";
        config.url.accessory_basic_info = "{{ url('accessory/info-tab') }}";
        config.url.history = "{{ url('accessory/jx-history') }}";
        config.getInternalPlaceByLocation = "{{ url('getInternalPlaceByLocation') }}";
        config.url.users = "{{ url('accessory/jx-users') }}";
        config.url.user_info = "{{ url('user/info') }}";
        config.url.device_info = "{{ url('device/info') }}";
        config.url.export = "{{ url('jx-exportaccessory/'.$accessory->id) }}";
        config.url.exportpdf = "{{ url('jx-pdfexportaccessory/'.$accessory->id) }}";
        config.url.documents = "{{ url('document/jx-documents') }}";
        config.url.document_upload = "{{ url('document/jx-upload') }}";
        config.url.document_download = "{{ url('uploads/documents') }}";
        config.url.purchase_document_download = "{{ url('purchase_attachments') }}";
        config.url.document_delete = "{{ url('document/delete') }}";
        config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
        config.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
        config.getUserByAjax = "{{ url('getUserForDropDown') }}";
        config.getDeviceByAjax = "{{ url('getDeviceForDropDown') }}";
		config.getDeviceForCheckoutDropDown = "{{ url('getDeviceForCheckoutDropDown') }}";
        config.getManufacturerByAjax = "{{ url('getManufacturerByQuery') }}";
        config.getInvoiceByAjax = "{{ url('getInvoiceByQuery') }}";
        config.getSupplierByAjax = "{{ url('getSupplierByQuery') }}";
        config.getCustomFieldsByCategory = "{{ url('getCustomFieldsByCategory') }}";
        config.url.getAssetDepartments = "{{ route('getAssetDepartments') }}";
        config.url.attachment_view     = "{{ url('document-attachment/view') }}";
        config.ajaxGetInternalPlace = "{{url('ajaxGetInternalPlace')}}";
        config.url.getActivatedUsers = "{{ url('getActivatedUsers') }}";
		config.getPredefinedDropdownByQuery = "{{url('getByCustomDropDown')}}";
        config.url.list = "{{ url('purchase-accessory-list') }}";
        config.url.add_purchase = "{{ url('add-purchase-accessory') }}";
        config.url.get_purchase = "{{ url('get-purchase-accessory') }}";
        config.url.update_purchase = "{{ url('update-purchase-accessory') }}";
        config.url.purchase_delete = "{{url('delete-purchase-accessory')}}";
        config.url.purchase_attachment_view  = "{{ url('purchase-attachment-accessory-view') }}";
        config.url.purchase_attachment_download = "{{url('uploads/documents')}}";
        config.imgviewpathpur = "{{ url('uploads/documents') }}";
        config.url.update_expected_checkin = "{{ url('accessory/expected-checkin-date')}}";
        config.url.change_info ="{{ url('accessories/change-info')}}";

        config.token = "{{ csrf_token() }}";
        config.translations = {
            add_new_accessory: '{{ trans('accessories.accessory_fields.add_new_accessory') }}',
            edit_accessory: '{{ trans('accessories.accessory_fields.edit_accessory') }}',
            save: '{{ trans('accessories.accessory_fields.save_accessory') }}',
            press_enter_with_Search: '{{ trans('accessories.accessory_fields.press_enter_with_Search') }}',
			please_enter_valid_search: '{{ trans('accessories.accessory_fields.please_enter_valid_search') }}',
            select_internal_place: '{{ trans('accessories.accessory_fields.select_internal_place') }}',
			something_went_wrong: '{{ trans('accessories.accessory_fields.something_went_wrong') }}',
			Search: '{{ trans('accessories.accessory_fields.Search') }}',
			Refresh_List: '{{ trans('accessories.accessory_fields.Refresh_List') }}',
			Bulk_Checkout: '{{ trans('accessories.accessory_fields.Bulk_Checkout') }}',
			Download: '{{ trans('accessories.accessory_fields.Download') }}',
			Download_PDF: '{{ trans('accessories.accessory_fields.Download_PDF') }}',
            Reload: '{{ trans('accessories.accessory_fields.Reload') }}',
            Upload_Document: '{{ trans('accessories.accessory_fields.Upload_Document') }}',
            view: '{{ trans('accessories.accessory_fields.view') }}',
			select_the_model: '{{trans('accessories.accessory_fields.select_the_model')}}',
			select_the_internal_place: '{{trans('accessories.accessory_fields.select_the_internal_place')}}',
			select_the_project: '{{trans('accessories.accessory_fields.select_the_project')}}',
			select_the_contract:'{{trans('accessories.accessory_fields.select_the_contract')}}',
			select_the_component:'{{trans('accessories.accessory_fields.select_the_component')}}',
			select_the_license: '{{trans('accessories.accessory_fields.select_the_license')}}',
			select_the_task: '{{trans('accessories.accessory_fields.select_the_task')}}',
			select_the_change_management: '{{trans('accessories.accessory_fields.select_the_change_management')}}',
			select_the_ticket:'{{trans('accessories.accessory_fields.select_the_ticket')}}',
			select_the_ticket_procure_request: '{{trans('accessories.accessory_fields.select_the_ticket_procure_request')}}',
            Select_the_Location: '{{ trans('accessories.accessory_fields.Select_the_Location') }}',
			Select_the_Purchase_Invoice: '{{ trans('accessories.accessory_fields.Select_the_Purchase_Invoice') }}',
			Select_the_manufacturer: '{{ trans('accessories.accessory_fields.Select_the_manufacturer') }}',
			Select_the_User: '{{ trans('accessories.accessory_fields.Select_the_User') }}',
			Select_the_Device: '{{ trans('accessories.accessory_fields.Select_the_Device') }}',
			Select_the_Supplier: '{{ trans('accessories.accessory_fields.Select_the_Supplier') }}',
            Select_the_Department: '{{ trans('accessories.accessory_fields.Select_the_Department') }}',
            add_item: '{{ trans('accessories.accessory_fields.add_item') }}',
            edit: '{{ trans('accessories.accessory_fields.edit') }}',
            Delete: '{{ trans('accessories.accessory_fields.delete') }}',
            Select_Currency_Format: '{{trans('accessories.accessory_fields.Select_Currency_Format')}}',
            select_the_purchase_by: '{{trans('purchase.purchase_fields.select_the_purchase_by')}}',
            edit_checkin_date : '{{trans('accessories.accessory_fields.edit_checkin_date')}}',
            check_in:'{{ trans('accessories.accessory_fields.check_in') }}',
		};
        config.historyTranslations = {
            name: '{{ trans('accessories.accessory_fields.name') }}',
            quantity: '{{ trans('accessories.accessory_history.quantity') }}',
            purchase_date: '{{ trans('accessories.accessory_history.purchase_date') }}',
            purchase_cost: '{{ trans('accessories.accessory_history.purchase_cost') }}',
            purchase_currency: '{{ trans('accessories.accessory_history.purchase_currency') }}',
            order_number: '{{ trans('accessories.accessory_history.order_number') }}',
            batch_no: '{{ trans('accessories.accessory_fields.batch_no') }}',
            notes: '{{ trans('accessories.accessory_fields.notes') }}',
            requestable: '{{ trans('accessories.accessory_history.requestable') }}',
            requestable_accessory: '{{ trans('accessories.accessory_fields.requestable_accessory') }}',
            threshold: '{{ trans('accessories.accessory_fields.threshold') }}',
            threshold_alerts: '{{ trans('accessories.accessory_history.threshold_alerts') }}',
            reorder_limit: '{{ trans('accessories.accessory_fields.reorder_limit') }}',
            scrap_qty: '{{ trans('accessories.accessory_history.scrap_qty') }}',
            category: '{{ trans('accessories.accessory_fields.category') }}',
            location: '{{ trans('accessories.accessory_fields.location') }}',
            department: '{{ trans('accessories.accessory_fields.department') }}',
            manufacturer: '{{ trans('accessories.accessory_fields.manufacturer') }}',
            supplier: '{{ trans('accessories.accessory_fields.supplier') }}',
            company: '{{ trans('accessories.accessory_fields.company') }}',
            purchase: '{{ trans('accessories.accessory_fields.purchase') }}',
            place: '{{ trans('accessories.accessory_history.place') }}',
            no_changes_found: '{{ trans('accessories.accessory_history.no_changes_found') }}',
            unable_to_load_change_information: '{{ trans('accessories.accessory_history.unable_to_load_change_information') }}',
		};
        config.accessory_name = "{{ $accessory->name }}";
        config.accessory_id = {{ $accessory->id }};
        // config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFieldsLicence($companyFieldset->fields)) : json_encode('') !!};
        config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFields($companyFieldset->fields)) : json_encode('') !!};
        config.companies = {!! json_encode($companies) !!};
        config.categories = {!! json_encode($categories) !!};
        config.currencies = {!! json_encode($currencies) !!};
        config.client = @json(config('app.client'));
        config.places = {!! json_encode($vd->places) !!};
        config.permissions = {!! json_encode($permissionArray) !!};
        config.default_currency_format = "{{ CommonHelper::settings()->default_currency }}";
        config.accessory_block_checkin = {!! CommonHelper::settings()->accessory_block_checkin == 1 ? 1 : 0 !!};
        @if($isCheckinCall) 
        config.directChkin = {!! json_encode($isCheckinCall) !!};
        @endif
        new MyApp(config);
    });
</script>
@endpush