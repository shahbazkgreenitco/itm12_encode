{{--
/**
* ------------------------------------------------------------
* File: info.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-16
* Created On: 2026-01-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* [1.0.1] - Changes for the search and pagination not working in checkout tab and translation changes
* [1.0.2] - Changes for adding the baseurl and translation changes
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('consumables.consumables_info.view.consumables_info_header'))

@section('content')
<section class="content">
    <div id="main-user-list-wrapper">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
            <button onclick="location.href='{{ url('consumables') }}'" class="d-flex gap-3 align-items-center bg-transparent outline-none border-0">
                <svg width="25" height="21" viewBox="0 0 25 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.5662 0.440759C11.706 0.580116 11.817 0.745702 11.8927 0.928029C11.9684 1.11036 12.0074 1.30584 12.0074 1.50326C12.0074 1.70068 11.9684 1.89616 11.8927 2.07848C11.817 2.26081 11.706 2.4264 11.5662 2.56576L5.12993 9.00201L23.5049 9.00201C23.9028 9.00201 24.2843 9.16004 24.5656 9.44135C24.8469 9.72265 25.0049 10.1042 25.0049 10.502C25.0049 10.8998 24.8469 11.2814 24.5656 11.5627C24.2843 11.844 23.9028 12.002 23.5049 12.002L5.12993 12.002L11.5662 18.4408C11.848 18.7226 12.0063 19.1047 12.0063 19.5033C12.0063 19.9018 11.848 20.284 11.5662 20.5658C11.2844 20.8475 10.9022 21.0059 10.5037 21.0059C10.1052 21.0059 9.72298 20.8475 9.44118 20.5658L0.441182 11.5658C0.301343 11.4264 0.190387 11.2608 0.114679 11.0785C0.0389711 10.8962 -4.50154e-07 10.7007 -4.58778e-07 10.5033C-4.67401e-07 10.3058 0.038971 10.1104 0.114679 9.92803C0.190387 9.7457 0.301342 9.58011 0.441182 9.44076L9.44118 0.440759C9.58054 0.30092 9.74613 0.189964 9.92845 0.114255C10.1108 0.0385471 10.3063 -0.000425789 10.5037 -0.000425798C10.7011 -0.000425806 10.8966 0.038547 11.0789 0.114255C11.2612 0.189964 11.4268 0.30092 11.5662 0.440759Z" fill="currentColor" />
                </svg>
                <h2 class="h2-text mb-0">{{ trans('consumables.consumables_info.view.consumables_info_heading') }} - {{ $consumable->name }} - ({{ $consumable->unique_tag }})</h2>
            </button>
            <div class="icon-btn-group d-flex gap-8">
                @can("ConsumableEdit")
                    <button class="header-icon-btn-only header-icon-btn-only-sm dtActEdit" data-bs-toggle="modal" data-bs-target="#consumablemodal" data-id="{{ $consumable->id }}" action="edit" type="button" data-original-title="{{ trans('consumables.consumables_info.view.edit_consumable') }}" title="{{ trans('consumables.consumables_info.view.edit_consumable') }}">
                        <svg width="40" height="38" viewBox="9 8 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M29.3103 13.8791L25.1216 9.68946C24.9823 9.55014 24.8169 9.43962 24.6349 9.36421C24.4529 9.28881 24.2578 9.25 24.0608 9.25C23.8638 9.25 23.6687 9.28881 23.4867 9.36421C23.3047 9.43962 23.1393 9.55014 23 9.68946L11.4397 21.2507C11.2998 21.3895 11.1889 21.5547 11.1134 21.7367C11.0379 21.9188 10.9994 22.114 11 22.311V26.5007C11 26.8985 11.158 27.2801 11.4393 27.5614C11.7207 27.8427 12.1022 28.0007 12.5 28.0007H16.6897C16.8868 28.0013 17.082 27.9628 17.264 27.8873C17.446 27.8118 17.6112 27.7009 17.75 27.561L29.3103 16.0007C29.4496 15.8614 29.5602 15.696 29.6356 15.514C29.711 15.332 29.7498 15.1369 29.7498 14.9399C29.7498 14.7429 29.711 14.5478 29.6356 14.3658C29.5602 14.1838 29.4496 14.0184 29.3103 13.8791ZM16.6897 26.5007H12.5V22.311L20.75 14.061L24.9397 18.2507L16.6897 26.5007ZM26 17.1895L21.8103 13.0007L24.0603 10.7507L28.25 14.9395L26 17.1895Z" fill="#7F7F7F" stroke="#7F7F7F" stroke-width="0.5" stroke-linejoin="round" />
                        </svg>
                    </button>
                @endcan
                @can("ConsumableDelete")
                    <button class="header-icon-btn-only header-icon-btn-only-sm dtActDel" type="button" data-bs-toggle="tooltip" title="{{ trans('consumables.consumables_info.view.delete_consumable') }}" data-indent="consumable" data-id="{{ $consumable->id }}">
                        <svg width="40" height="38" viewBox="9 8 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28.25 11.5H24.5V10.75C24.5 10.1533 24.2629 9.58097 23.841 9.15901C23.419 8.73705 22.8467 8.5 22.25 8.5H17.75C17.1533 8.5 16.581 8.73705 16.159 9.15901C15.7371 9.58097 15.5 10.1533 15.5 10.75V11.5H11.75C11.5511 11.5 11.3603 11.579 11.2197 11.7197C11.079 11.8603 11 12.0511 11 12.25C11 12.4489 11.079 12.6397 11.2197 12.7803C11.3603 12.921 11.5511 13 11.75 13H12.5V26.5C12.5 26.8978 12.658 27.2794 12.9393 27.5607C13.2206 27.842 13.6022 28 14 28H26C26.3978 28 26.7794 27.842 27.0607 27.5607C27.342 27.2794 27.5 26.8978 27.5 26.5V13H28.25C28.4489 13 28.6397 12.921 28.7803 12.7803C28.921 12.6397 29 12.4489 29 12.25C29 12.0511 28.921 11.8603 28.7803 11.7197C28.6397 11.579 28.4489 11.5 28.25 11.5ZM17 10.75C17 10.5511 17.079 10.3603 17.2197 10.2197C17.3603 10.079 17.5511 10 17.75 10H22.25C22.4489 10 22.6397 10.079 22.7803 10.2197C22.921 10.3603 23 10.5511 23 10.75V11.5H17V10.75ZM26 26.5H14V13H26V26.5ZM18.5 16.75V22.75C18.5 22.9489 18.421 23.1397 18.2803 23.2803C18.1397 23.421 17.9489 23.5 17.75 23.5C17.5511 23.5 17.3603 23.421 17.2197 23.2803C17.079 23.1397 17 22.9489 17 22.75V16.75C17 16.5511 17.079 16.3603 17.2197 16.2197C17.3603 16.079 17.5511 16 17.75 16C17.9489 16 18.1397 16.079 18.2803 16.2197C18.421 16.3603 18.5 16.5511 18.5 16.75ZM23 16.75V22.75C23 22.9489 22.921 23.1397 22.7803 23.2803C22.6397 23.421 22.4489 23.5 22.25 23.5C22.0511 23.5 21.8603 23.421 21.7197 23.2803C21.579 23.1397 21.5 22.9489 21.5 22.75V16.75C21.5 16.5511 21.579 16.3603 21.7197 16.2197C21.8603 16.079 22.0511 16 22.25 16C22.4489 16 22.6397 16.079 22.7803 16.2197C22.921 16.3603 23 16.5511 23 16.75Z" fill="#7F7F7F" stroke="#7F7F7F" stroke-width="0.5" stroke-linejoin="round" />
                        </svg>
                    </button>
                @endcan
                @can("ConsumableAdd")
                    <button class="header-icon-btn-only header-icon-btn-only-sm dtActClone" type="button" id="courier_details_modal_btn" data-bs-toggle="modal" data-bs-target="#consumablemodal" data-id="{{ $consumable->id }}" data-original-title="{{ trans('consumables.consumables_info.view.clone_consumable') }}" title="{{ trans('consumables.consumables_info.view.clone_consumable') }}" action="clone">
                        <svg viewBox="0 0 16 16" fill="none" ><path d="M14.375 0H4.375C4.20924 0 4.05027 0.0658481 3.93306 0.183058C3.81585 0.300269 3.75 0.45924 3.75 0.625V3.75H0.625C0.45924 3.75 0.300269 3.81585 0.183058 3.93306C0.0658481 4.05027 0 4.20924 0 4.375V14.375C0 14.5408 0.0658481 14.6997 0.183058 14.8169C0.300269 14.9342 0.45924 15 0.625 15H10.625C10.7908 15 10.9497 14.9342 11.0669 14.8169C11.1842 14.6997 11.25 14.5408 11.25 14.375V11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V0.625C15 0.45924 14.9342 0.300269 14.8169 0.183058C14.6997 0.0658481 14.5408 0 14.375 0ZM10 13.75H1.25V5H10V13.75ZM13.75 10H11.25V4.375C11.25 4.20924 11.1842 4.05027 11.0669 3.93306C10.9497 3.81585 10.7908 3.75 10.625 3.75H5V1.25H13.75V10Z" fill="#7F7F7F"/></svg>
                    </button>
                @endcan
                {{-- @can("ConsumableDownload")
                    <button class="header-icon-btn-only header-icon-btn-only-sm btnActPrintLabel" type="button" data-bs-toggle="tooltip" data-original-title="{{ trans('consumables.consumables_info.view.print_history') }}" title="{{ trans('consumables.consumables_info.view.print_history') }}" data-id="{{ $consumable->id }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M6 9V2H18V9" stroke="#7F7F7F" stroke-width="2"></path>
                            <path d="M6 18H18V22H6V18Z" stroke="#7F7F7F" stroke-width="2"></path>
                            <path d="M6 14H18" stroke="#7F7F7F" stroke-width="2"></path>
                        </svg>
                    </button>
                @endcan --}}
            </div>
        </div>
        <main class="main-content">
            <div class="tab-bar mb-3">
                <ul class="nav nav-underline" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#basic-info-tab">
                            {{ trans('consumables.consumables_info.view.basic_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link checkouts-tab" data-bs-toggle="tab" href="#checkouts-tab">
                            {{ trans('consumables.consumables_info.view.checkouts') }}
                        </a>
                    </li>
                    @can("ConsumableDocumentsUpload")
                        <li class="nav-item">
                            <a class="nav-link documents-tab" data-bs-toggle="tab" href="#documents-tab">
                                {{ trans('consumables.consumables_info.view.documents') }}
                            </a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link purchase-history-tab" data-bs-toggle="tab" href="#purchase-history-tab">
                            {{ trans('consumables.consumables_info.view.purchase') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link history-tab" data-bs-toggle="tab" href="#history-tab">
                            {{ trans('consumables.consumables_info.view.history') }}
                        </a>
                    </li>
                    @if (config('app.client') == "rolepermission")
                        <li class="nav-item">
                            <a class="nav-link activity-tab" data-bs-toggle="tab" href="#activity-tab">
                                {{ trans('consumables.consumables_info.view.activity') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="basic-info-tab">@include('consumables.basic-info')</div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 pt-0" id="checkouts-tab">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                    <select id="showSelectCheckouts" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                        <option value="10" selected>{{ trans('consumables.view.show') }} (10)</option>
                                        <option value="25">{{ trans('consumables.view.show') }} (25)</option>
                                        <option value="50">{{ trans('consumables.view.show') }} (50)</option>
                                        <option value="100">{{ trans('consumables.view.show') }} (100)</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1"></div>
                                <div>
                                    <div class="amg-list-searchbar">
                                        <svg class="amg-list-searchbar__icon-checkouts" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input" placeholder="{{ trans('consumables.consumables_info.view.search') }}" id="checkoutsSearch">
                                    </div>
                                </div>
                                <button class="amg-refresh-btn btn-reload-list" data-table="accounts">
                                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                    </svg>
                                    <span>{{ trans('consumables.consumables_info.view.refresh') }}</span>
                                </button>
                                <button class="header-icon-btn-only header-icon-btn-only-sm" data-bs-toggle="tooltip" title="{{ trans('consumables.view.download') }}" id="btn_export">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 21.4V2.6C4 2.26863 4.26863 2 4.6 2H16.2515C16.4106 2 16.5632 2.06321 16.6757 2.17574L19.8243 5.32426C19.9368 5.43679 20 5.5894 20 5.74853V21.4C20 21.7314 19.7314 22 19.4 22H4.6C4.26863 22 4 21.7314 4 21.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        </path>
                                        <path d="M8 10L16 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M8 18L16 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M8 14L12 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M16 2V5.4C16 5.73137 16.2686 6 16.6 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                                <button class="header-icon-btn-only header-icon-btn-only-sm" data-bs-toggle="tooltip" title="{{ trans('consumables.consumables_info.view.download_pdf') }}" id="btn_pdf">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 21.4V2.6C4 2.26863 4.26863 2 4.6 2H16.2515C16.4106 2 16.5632 2.06321 16.6757 2.17574L19.8243 5.32426C19.9368 5.43679 20 5.5894 20 5.74853V21.4C20 21.7314 19.7314 22 19.4 22H4.6C4.26863 22 4 21.7314 4 21.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        </path>
                                        <path d="M16 2V5.4C16 5.73137 16.2686 6 16.6 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>
                            <table id="checkoutsTable" class="table w-100">
                                <thead>
                                    <tr>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.id') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.assigned_for') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.assigned_to') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.checkout_date') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.admin') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.ticket_id') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.action') }}
                                            </h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 pt-0" id="documents-tab">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                    <select id="showSelectDocuments" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                        <option value="10" selected>{{ trans('consumables.view.show') }} (10)</option>
                                        <option value="25">{{ trans('consumables.view.show') }} (25)</option>
                                        <option value="50">{{ trans('consumables.view.show') }} (50)</option>
                                        <option value="100">{{ trans('consumables.view.show') }} (100)</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1"></div>
                                <div>
                                    <div class="amg-list-searchbar">
                                        <svg class="amg-list-searchbar__icon-documents" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input" placeholder="{{ trans('consumables.consumables_info.view.search') }}" id="documentsSearch">
                                    </div>
                                </div>
                                <button class="amg-refresh-btn btn-reload-list" id="documents-button-reload" data-table="accounts">
                                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                    </svg>
                                    <span>{{ trans('consumables.consumables_info.view.refresh') }}</span>
                                </button>
                                <button class="amg-btn amg-btn-primary btn-upload-document" type="button" data-bs-toggle="tooltip" title="{{ trans('consumables.consumables_info.view.add') }}" id="btn_upload_document">
                                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                        <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" />
                                    </svg>
                                    <span>{{ trans('consumables.consumables_info.view.add') }}</span>
                                </button>
                            </div>
                            <table id="tblDocument" class="table w-100">
                                <thead>
                                    <tr>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.id') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.document_name') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.uploaded_on') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.notes') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.actions') }}</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 pt-0" id="purchase-history-tab">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                    <select id="showSelectPurchase" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                        <option value="10" selected>{{ trans('consumables.view.show') }} (10)</option>
                                        <option value="25">{{ trans('consumables.view.show') }} (25)</option>
                                        <option value="50">{{ trans('consumables.view.show') }} (50)</option>
                                        <option value="100">{{ trans('consumables.view.show') }} (100)</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1"></div>
                                <div>
                                    <div class="amg-list-searchbar">
                                        <svg class="amg-list-searchbar__icon-purchase" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input" placeholder="{{ trans('consumables.consumables_info.view.search') }}" id="purchaseSearch">
                                    </div>
                                </div>
                                <button class="amg-refresh-btn btn-reload-list" data-table="accounts">
                                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                    </svg>
                                    <span>{{ trans('consumables.consumables_info.view.refresh') }}</span>
                                </button>
                                <button class="amg-btn amg-btn-primary btn-add-purchase" type="button" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('consumables.consumables_info.view.add_item') }}">
                                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none">
                                        <path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor"></path>
                                    </svg>
                                    <span>{{ trans('consumables.consumables_info.view.add') }}</span>
                                </button>
                            </div>
                            <table id="tblPurchase" class="table w-100">
                                <thead>
                                    <tr>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.id') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.batch_no') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.purchase_date') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.po_number') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.purchase_from') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.received_date') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.expire_date') }}
                                            </h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.quantity') }}
                                            </h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.price') }}
                                            </h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.location') }}
                                            </h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.department') }}
                                            </h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.updated_on') }}
                                            </h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.action') }}
                                            </h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 pt-0" id="history-tab">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                    <select id="showSelectHistory" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                        <option value="10" selected>{{ trans('consumables.view.show') }} (10)</option>
                                        <option value="25">{{ trans('consumables.view.show') }} (25)</option>
                                        <option value="50">{{ trans('consumables.view.show') }} (50)</option>
                                        <option value="100">{{ trans('consumables.view.show') }} (100)</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1"></div>
                                <div>
                                    <div class="amg-list-searchbar">
                                        <svg class="amg-list-searchbar__icon-history" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input" placeholder="{{ trans('consumables.consumables_info.view.search') }}" id="historySearch">
                                    </div>
                                </div>
                                <button class="amg-refresh-btn btn-reload-list-history" data-table="accounts">
                                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                    </svg>
                                    <span>{{ trans('consumables.consumables_info.view.refresh') }}</span>
                                </button>
                            </div>
                            <table id="tblHistory" class="table w-100">
                                <thead>
                                    <tr>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.date') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.admin') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.actions') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.assigned_for') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.assigned_to') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.notes') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.ticket_id') }}</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 pt-0" id="activity-tab">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                                <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                                    <select id="showSelectActivity" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                        <option value="10" selected>{{ trans('consumables.view.show') }} (10)</option>
                                        <option value="25">{{ trans('consumables.view.show') }} (25)</option>
                                        <option value="50">{{ trans('consumables.view.show') }} (50)</option>
                                        <option value="100">{{ trans('consumables.view.show') }} (100)</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1"></div>
                                <div>
                                    <div class="amg-list-searchbar">
                                        <svg class="amg-list-searchbar__icon-activity" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                        </svg>
                                        <input type="text" class="amg-list-searchbar__input" placeholder="{{ trans('consumables.consumables_info.view.search') }}" id="activitySearch">
                                    </div>
                                </div>
                                <button class="amg-refresh-btn btn-reload-list-activity" data-table="accounts">
                                    <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                                    </svg>
                                    <span>{{ trans('consumables.consumables_info.view.refresh') }}</span>
                                </button>
                            </div>
                            <table id="tblActivity" class="table w-100">
                                <thead>
                                    <tr>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.id') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.actions') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.unique_tag') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.name') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.location') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.place') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.order_no') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.purchase_cost') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.qty') }}</h4>
                                        </th>
                                        <th>
                                            <h4>{{ trans('consumables.consumables_info.view.created_at') }}</h4>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    @include("documents.upload_modal")
    @include('consumables.checkin_modal')
    @include('consumables.consumable-modal')
    @include('consumables.checkout-consumable-modal')
    @include('consumables.note-modal')
    @include('consumables.purchase-modal')
    @include("consumables.scrap_modal")
    @include("consumables.revert_scrap_modal")
    <a id="print_label_starter" target="_blank" href="" class="hidden"></a>
</section>
@endsection

@push('scripts')
<script>var baseURL = '{{ URL::to('/') }}';</script>
<script src="{{ asset('assets/js/plugins/print/jQuery.print.js') }}"></script>
<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/support_validate.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/consumables/info.js') !!}"></script>

<script type="text/javascript">
    var requestOngoing = false;
    $(document).ready(function() {
        var config = new Object;
        config.url = new Object;
        config.imgviewpath = "{{ url('uploads/consumable') }}";
        config.url.getSupplierByAjax = "{{ url('getSupplierByQuery') }}";
        config.url.getInvoiceByAjax = "{{ url('getInvoiceByQuery') }}";
        config.url.consumableUsers = "{{ url( 'jx-consumable-users/'.$consumable->id ) }}";
        config.url.consumableHistory = "{{ url( 'consumable-history/'.$consumable->id ) }}";
        config.url.revoke = "{{ url( 'consumable-revoke-assignment' ) }}";
		config.url.infourl = "{{ url('consumable-info') }}";
		config.url.infocontent = "{{ url('consumable/info-summery/'.$consumable->id) }}";
		config.url.add = "{{ url('consumables') }}";
		config.url.edit = "{{ url('consumables') }}";
        config.url.scrap = "{{ url('consumable/scrap') }}";
        config.url.revertScrap = "{{ url('consumable/revertScrap') }}";
		config.url.update = "{{ url('update-consumables') }}";
		config.url.delete = "{{ url('delete-consumable') }}";
        config.url.checkout = "{{ url('checkout-consumable') }}";
        config.url.documents = "{{ url('document/jx-documents') }}";
        config.url.document_upload = "{{ url('document/jx-upload') }}";
        config.url.document_download = "{{ url('uploads/documents') }}";
        config.url.purchase_document_download = "{{ url('purchase_attachments') }}";
        config.url.document_delete = "{{ url('document/delete') }}";
        config.url.getCustomFieldsByCategory = "{{ url('getCustomFieldsByCategory') }}";
        config.getUserByAjax    = "{{ url('getUserByQuery') }}";
        config.getLocationByAjax = "{{ url('getLocationByQuery') }}";
        config.getInternalPlaceByAjax = "{{ url('getInternalPlaceByAjax') }}";
        config.ajaxGetInternalPlace = "{{url('ajaxGetInternalPlace')}}",
        config.getPredefinedDropdownByQuery = "{{url('getByCustomDropDown')}}",
        config.getCategoryByQuery = "{{url('getCategoryByQuery')}}";
        config.url.export = "{{ url('jx-exportconsumables/'.$consumable->id) }}";
        config.url.export_pdf = "{{ url('jx-exportpdfconsumables/'.$consumable->id) }}";
        config.url.getAssetDepartments = "{{ route('getAssetDepartments') }}";
        config.url.attachment_view     = "{{ url('document-attachment/view') }}";
        config.url.getUnitsByAjax = "{{ url('getUnitsByAjax') }}";
        config.url.getActivatedUsers = "{{ url('getActivatedUsers') }}";
        config.ajaxGetInternalPlace = "{{ url('ajaxGetInternalPlace') }}";
        config.permissions = {!! json_encode($permissionArray) !!};
        config.consumable_name = "{{ $consumable->name }}";
        config.categories = {!! json_encode($categories) !!};
        config.url.list = "{{ url('purchase-consumable-list') }}";
        config.url.activity_history_list = "{{ url('activity-history-list') }}";
        config.currencies = {!! json_encode($currencies) !!};
        config.client = @json(config('app.client'));
        config.consumable_id = {{$consumable->id}};
        config.consumable_unique_tag = '{{$consumable->unique_tag }}';
        config.url.add_purchase = "{{ url('add-purchase-consumable') }}";
        config.url.get_purchase = "{{ url('get-purchase-consumable') }}";
        config.url.update_purchase = "{{ url('update-purchase-consumable') }}";
        config.url.purchase_delete = "{{url('delete-purchase-consumable')}}";
        config.url.purchase_attachment_view  = "{{ url('purchase-attachment-consumable-view') }}";
        config.url.purchase_attachment_download = "{{url('storage/uploads/documents')}}";
        config.url.consumable_basic_info = "{{ url('consumable/info-tab') }}"
        config.url.getCompanyUsers = "{{ url('getCompanyUsers') }}";
        config.url.getManufacturerByQuery = "{{ url('getManufacturerByQuery') }}";
        config.url.image_delete = "{{ url('consumable-image-delete') }}";
        config.imgviewpathpur = "{{ url('uploads/documents') }}";
        config.getSupplierByAjax = "{{url('getSupplierByQuery')}}";
        config.getDeviceForCheckoutDropDown = "{{ url('getDeviceForCheckoutDropDown') }}";
        config.assignedForOptions = {!! json_encode($vd->assignedForOptions) !!};
	    config.places = {!! json_encode($vd->places) !!};
        config.translations = {
            add_item: '{{ trans('consumables.consumables_info.view.add_item') }}',
            press_enter_with_Search: '{{ trans('consumables.consumables_info.view.press_enter_with_Search') }}',
            Export_PDF: '{{ trans('consumables.consumables_info.view.Export_PDF') }}',
            Reload: '{{ trans('consumables.consumables_info.view.Reload') }}',
            Export: '{{ trans('consumables.consumables_info.view.Export') }}',
            please_enter_valid_search: '{{ trans('consumables.consumables_info.view.please_enter_valid_search') }}',
            New_Document_Upload: '{{ trans('consumables.consumables_info.view.New_Document_Upload') }}',
            something_went_wrong:'{{ trans('consumables.consumables_info.view.something_went_wrong') }}',
            Delete_Document:'{{ trans('consumables.consumables_info.view.Delete_Document') }}',
            Download_Document:'{{ trans('consumables.consumables_info.view.Download_Document') }}',
            Upload_Document:'{{ trans('consumables.consumables_info.view.Upload_Document') }}',
            add: '{{ trans('consumables.consumables_info.view.add') }}',
            edit: '{{ trans('consumables.consumables_info.view.edit') }}',
            are_you_Want_delete:'{{ trans('consumables.consumables_info.view.are_you_Want_delete') }}',
            Edit: '{{ trans('consumables.consumables_info.view.Edit') }}',
            Delete: '{{ trans('consumables.consumables_info.view.Delete') }}',
            Restore_Consumable: '{{ trans('consumables.consumables_info.view.Restore_Consumable') }}',
            select_internal_place: '{{ trans('consumables.consumables_info.view.select_internal_place') }}',
			Edit_Consumable: '{{ trans('consumables.consumables_info.view.Edit_Consumable') }}',
			Clone_Consumable: '{{ trans('consumables.consumables_info.view.Clone_Consumable') }}',
			Delete_Consumable: '{{ trans('consumables.consumables_info.view.Delete_Consumable') }}',
            Consumable_Detail: '{{ trans('consumables.consumables_info.view.Consumable_Detail') }}',
            Batch_No: '{{ trans('consumables.consumables_info.view.Batch_No') }}',
			Category: '{{ trans('consumables.consumables_info.view.Category') }}',
			Company_Name: '{{ trans('consumables.consumables_info.view.Company_Name') }}',
			Search: '{{ trans('consumables.consumables_info.view.Search') }}',
			Refresh_List: '{{ trans('consumables.consumables_info.view.Refresh_List') }}',
			Bulk_Checkout: '{{ trans('consumables.consumables_info.view.Bulk_Checkout') }}',
			Download: '{{ trans('consumables.consumables_info.view.Download') }}',
			Download_PDF: '{{ trans('consumables.consumables_info.view.Download_PDF') }}',
			Select_the_Supplier: '{{ trans('consumables.consumables_info.view.Select_the_Supplier') }}',
			Select_the_Purchase_Invoice: '{{ trans('consumables.consumables_info.view.Select_the_Purchase_Invoice') }}',
			Select_the_User: '{{ trans('consumables.consumables_info.view.Select_the_User') }}',
			are_you_want_delete: '{{ trans('consumables.consumables_info.view.are_you_want_delete') }}',
            check_in_consumable: '{{trans('consumables.consumables_info.view.check_in_consumable')}}',
            are_you_sure_check_in:'{{trans('consumables.consumables_info.view.are_you_sure_check_in')}}',
            select_the_location: '{{trans('consumables.consumables_info.view.Select_Location')}}',
            select_the_unit: '{{trans('consumables.consumables_info.view.select_the_unit')}}',
            select_the_department: '{{trans('consumables.consumables_info.view.select_the_department')}}',
            select_the_category:'{{trans('consumables.consumables_info.view.Select_Category')}}',
            select_the_device:'{{trans('consumables.consumables_info.view.Select_the_Device')}}',
            select_the_model: '{{trans('consumables.consumables_info.view.select_the_model')}}',
            select_the_internal_place: '{{trans('consumables.consumables_info.view.select_the_internal_place')}}',
            select_the_project: '{{trans('consumables.consumables_info.view.select_the_project')}}',
            select_the_contract:'{{trans('consumables.consumables_info.view.select_the_contract')}}',
            select_the_component:'{{trans('consumables.consumables_info.view.select_the_component')}}',
            select_the_license: '{{trans('consumables.consumables_info.view.select_the_license')}}',
            select_the_task: '{{trans('consumables.consumables_info.view.select_the_task')}}',
            select_the_change_management: '{{trans('consumables.consumables_info.view.select_the_change_management')}}',
            select_the_manufacturer:'{{ trans('consumables.consumables_info.view.Select_Manufacturer') }}',
            select_the_ticket:'{{trans('consumables.consumables_info.view.select_the_ticket')}}',
            select_the_ticket_procure_request: '{{trans('consumables.consumables_info.view.select_the_ticket_procure_request')}}',
            save: '{{ trans('consumables.consumables_info.view.save') }}',
            Select_Currency_Format: '{{trans('consumables.consumables_info.view.Select_Currency_Format')}}',
            select_the_purchase_by: '{{trans('consumables.consumables_info.view.select_the_purchase_by')}}',
            select_the_purchase_from: '{{trans('consumables.consumables_info.modal.select_the_purchase_from')}}',
            are_you_delete_attachment: '{{ trans('consumables.config.are_you_delete_attachment') }}',
        };
        config.custom_fields = {!! !empty($companyFieldset->fields) ? json_encode(CommonHelper::formCustomFields($companyFieldset->fields)) : json_encode('') !!};
        config.default_currency_format = "{{ CommonHelper::settings()->default_currency }}";
        config.consumable_id = {{ $consumable->id }};
        config.token = "{{ csrf_token() }}";
        @if($isCheckinCall)
            config.directChkin = {!! json_encode($isCheckinCall) !!};
        @endif

        var openpop = false;
        @if(app('request')->input('popup'))
            var evalpopup = 'openRevokePopUp({{app('request')->input('open')}})';
            $('a[href="#user-tab"]').click()
        openpop = true;
        @endif

        function openRevokePopUp(EntryId) {
            var element = $('button[data-id="'+EntryId+'"]');
            if(element.length) {
                if( element.attr('action') == 'revoke' ) {
                    element.click();
                } else {
                    var data ={
                        'mag':'Provided information not found to revoke .',
                    }
                    sweetAlert('center', 'error', data);
                }
            } else {
                var data ={
                        'mag':'Provided information not found for revoke .',
                    }
                    sweetAlert('center', 'error', data);
            }
        }
        new TmpSln(config, openpop, requestOngoing);
    });
</script>

@endpush
