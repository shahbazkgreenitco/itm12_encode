{{-- * ------------------------------------------------------------
* File: index.blade.php
* Module: Problem Management
* PROBM/26/01
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
@section('title', trans("content.service_ticket_fields.Problem_Management"))
@section('content')
<div id="main-user-list-wrapper">
<section class="content">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{  trans("content.service_ticket_fields.Problem_Management") }}</h3>
        <div class="d-flex gap-8">
            <!-- Download -->
            @if (Auth::user()->hasPermissionTo('DownloadProblemManagement'))
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-download" type="button" data-bs-toggle="tooltip" title="{{  trans("content.problem_manager.download") }}">
                   <svg viewBox="0 0 18 18" fill="none" ><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor" /></svg>
                </button>
            @endif
            <button class="header-icon-btn header-icon-btn-sm btn-open-filter" type="button" data-bs-toggle="tooltip" title="{{ trans('content.problem_manager.Filter') }}">
                <svg viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z"
                        fill="currentColor" />
                </svg>
                <span class="b3-text opacity-50">{{ trans('content.problem_manager.Filter') }}</span>
                <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
            </button>
             <!-- Add -->
            @if(Auth::user()->hasPermissionTo('AddProblemManagement'))
                <button class="amg-btn amg-btn-primary amg-btn-sm open-add-modal" type="button" data-bs-toggle="tooltip" title="{{ trans("content.problem_manager.add_heading") }}">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" ><path d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z" fill="currentColor" /></svg>
                        <span>{{ trans('content.problem_manager.add_heading') }}</span>
                </button>
            @endif
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body">                   
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="col-auto">
                            <select id="user-list-page-length" class="amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                <option value="10" selected>{{ trans('content.dynamic_form.show') }} (10)</option>
                                <option value="25">{{ trans('content.dynamic_form.show') }} (25)</option>
                                <option value="50">{{ trans('content.dynamic_form.show') }} (50)</option>
                                <option value="100">{{ trans('content.dynamic_form.show') }} (100)</option>
                            </select>
                        </div>
                        <div class="flex-grow-1"></div>

                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input problem-manage-list-search searchbox plain-search" id="problem-manage-list-search" placeholder="{{ trans('content.problem_manager.press_enter_with_Search') }}">
                            </div>
                        </div>

                        <button class="amg-refresh-btn btn-reload-list">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('content.problem_manager.Refresh_List') }}</span>
                        </button>
                    </div>


                    <div class="js-user-list-view-panel">
                        <div class="table-responsive">
                            <table id="mytable" class="table display app-data-table">
                                <thead>
                                    <tr>
                                        <th><h4>{{ trans("content.service_ticket_fields.company") }}</h4></th>                                       
                                        <th><h4>{{ trans("content.problem_manager.Subject") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.Priority") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.Department") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.Category") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.Subcategory") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.Device_Completed") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.Ticket_Completed") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.CreatedAt") }}</h4></th>
                                        <th><h4>{{ trans("content.problem_manager.UpdatedAt") }}</h4></th>
                                        <th><h4>{{ trans("content.service_ticket_fields.Actions") }}</h4></th>
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
    @include('problem_manager.add_problem')

    <div class="amg-modal modal fade" id="advance-filters" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 900px;">
            <div class="modal-content rounded-5 bg-white">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">Problem Management Filter</h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="#7F7F7F" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 px-4">
                        <!-- Company -->
                        <div class="col-md-4">
                            <label class="form-label b5-text">Company</label>
                            <select id="filter_by_company" name="filter_by_company" class="form-control" multiple></select>
                        </div>
                        <!-- Department -->
                        <div class="col-md-4">
                            <label class="form-label b5-text">{{ trans("config.filter_heading.filter_by_department") }}</label>
                            <select id="filter_by_department" name="filter_by_department[]" class="form-control" multiple></select>
                        </div>
                        <!-- Priority -->
                        <div class="col-md-4">
                            <label class="form-label b5-text">{{ trans("config.filter_heading.Filter_By_Priority") }}</label>
                            <select id="filter_by_priority" name="filter_by_priority[]" class="form-control" multiple></select>
                        </div>
                        <!-- Problem Category -->
                        <div class="col-md-4">
                            <label class="form-label b5-text">{{ trans("config.filter_heading.Filter_By_Problem_Category") }}</label>
                            <select id="filter_by_problem_category" name="filter_by_problem_category[]" class="form-control" multiple></select>
                        </div>
                        <!-- Sub Category -->
                        <div class="col-md-4">
                            <label class="form-label b5-text">{{ trans("config.filter_heading.Filter_By_Sub_Category") }}</label>
                            <select id="filter_by_sub_category" name="filter_by_sub_category[]" class="form-control" multiple></select>
                        </div>
                        <!-- Handler -->
                        <div class="col-md-4">
                            <label class="form-label b5-text">{{ trans("content.filter_heading.Filter_By_Ticket_Handler") }}</label>
                            <select id="filter_by_handler" name="filter_by_handler[]" class="form-control" multiple></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start mb-4 pb-4 py-0">
                    <div class="amg-btn-group mt-3 gap-3 px-4">
                        <button type="button" class="amg-btn amg-btn-ghost s2-text bg-black text-white hover-opacity-80" id="btnClrFilter">
                            {{ trans("content.filter_heading.Clear") }}
                        </button>
                        <button type="button" class="amg-btn amg-btn-primary s2-text btn-filter" style="min-width: 200px;">
                            {{ trans("button.filter") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.css') !!}" rel="stylesheet" />
<style>
    .btn-open-filter {
        position: relative;
    }
    .filter-count-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #dc3545;
        color: #fff;
        border-radius: 10px;
        font-size: 10px;
        line-height: 1;
        min-width: 16px;
        height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
    }
   
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{!! CommonHelper::asset('js/common.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/problem_management/problem_list.js') !!}"></script>
<script type="text/javascript">
   $(document).ready(function() {
      var config = {};
      config.url = {};
      config.listview = "{{ $listType }}";
      config.url.ajaxlist = "{{ url('problem_management/ajaxlist') }}";
      config.url.edit = "{{ url('problem_management/edit') }}";
      config.url.delete = "{{ url('problem_management/delete') }}";
      config.getUserByQuery = "{{ url('getUserByQuery') }}";
      config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
      config.categories = {!!json_encode($categories) !!};
      config.models = {!!json_encode($models) !!};
      config.manufacturers = {!!json_encode($manufacturer) !!};
      config.impact_ticket = {!!json_encode($impact_ticket) !!};
      config.priorities = {!!json_encode($priorities) !!};
      config.departments = {!!json_encode($departments) !!};
      config.models = {!!json_encode($models) !!};
      config.locations = {!!json_encode($locations) !!};
      config.devices = {!!json_encode($device) !!};
      config.suppliers = {!!json_encode($suppliers) !!};
      config.user = {!! json_encode(Auth::user()->only("id", "first_name", "last_name", "username", "company_id")) !!};
      config.url.filters = "{{ url('problem_management/filterproblem') }}";
      config.url.searchFilteredDevice = "{{ url('problem_management/searchFilteredDevice') }}";
      config.url.filterdevice = "{{ url('problem_management/filterproblemdevice') }}";
      config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
      config.url.departments_by_company = "{{ url('departments/by-company') }}";
      config.url.departments_with_company = "{{ url('tickets/departments') }}";
      config.url.get_users_to_assign_by_dep_by_availability = "{{ url('ticket/get_users_to_assign_by_dep_by_availability') }}";
      config.url.getTechCurrentStatusById = "{{url('technician/get-tech-curren-status-id')}}";
      config.url.add_problem = "{{ url('problem_management/addproblem') }}";
      config.url.getUserDeviceByAjax = "{{ url('getUserDeviceForDropDown') }}";
      config.url.getTicketDetails = "{{ url('problem-management/getTickets') }}";
      config.url.update = "{{ url('problem_management/update') }}";
      config.url.download_url = "{{ url('problem_management/export-problem') }}";
      config.url.Problem_Manage = "{{ url('problem_management/view') }}";
      config.permissions = {!! json_encode($permissionArray) !!};
      config.datatable_translations =  @json(trans('datatable.datatable'));
      config.url.get_company_by_user_access = "{{ url('getCompanyByUserAccess') }}";
        config.company_defulte = {!! json_encode($company) !!};
        config.company_user_detail = {!! json_encode($userDatail) !!};
        config.translations = {
         something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
         up_heading: '{{ trans('content.problem_manager.edit_heading') }}',
         delete_record: '{{ trans('content.problem_manager.delete_record') }}',
         filter_by_department:'{{ trans('content.filter_heading.filter_by_department') }}',
         No_Filter: '{{ trans('content.report_fields.No_Filter') }}',
         Filter_By_Priority: '{{ trans('content.filter_heading.Filter_By_Priority') }}',
         Filter_By_Problem_Category: '{{ trans('content.filter_heading.Filter_By_Problem_Category') }}',
         Filter_By_Sub_Category: '{{ trans('content.filter_heading.Filter_By_Sub_Category') }}',
         press_enter_with_Search: '{{ trans('content.problem_manager.press_enter_with_Search') }}',
         Search: '{{ trans('content.problem_manager.Search') }}',
         Refresh_List: '{{ trans('content.problem_manager.Refresh_List') }}',
         Filter: '{{ trans('content.problem_manager.Filter') }}',
         edit_problem: '{{ trans('content.problem_manager.edit_problem') }}',
         Delete: '{{ trans('content.problem_manager.Delete') }}',
         select_priority: '{{ trans('content.problem_manager.select_priority') }}',
         Select_Problem_Category: '{{ trans('content.problem_manager.Select_Problem_Category') }}',
         Departement: '{{ trans('content.problem_manager.Department') }}',
         No_Filter: '{{ trans('content.problem_manager.No_Filter') }}',
         select_impacted_ticket: '{{ trans('content.problem_manager.select_impacted_ticket') }}',
         select_impacted_device: '{{ trans('content.problem_manager.select_impacted_device') }}',
         add_new_problem: '{{ trans('content.problem_manager.add') }}',
         add_problem_heading: '{{ trans('content.problem_manager.add_heading') }}',
         Download: '{{ trans('content.problem_manager.Download') }}',
         Select_the_problem_Handler: '{{ trans('content.problem_manager.Select_the_Problem_Handler') }}',
         Manage:'{{ trans('content.problem_manager.Manage_Problem') }}',
         asset_tag:'{{ trans('content.problem_manager.asset_tag') }}',
         device_name:'{{ trans('content.problem_manager.device_name') }}',
         device_type:'{{ trans('content.problem_manager.device_type') }}',
         Model:'{{ trans('content.problem_manager.Model') }}',
         serial_number:'{{ trans('content.problem_manager.serial_number') }}',
         action:'{{ trans('content.service_ticket_fields.Actions') }}',
         id:'{{ trans('content.problem_manager.Id') }}',
         tag:'{{ trans('content.problem_manager.Tag') }}',
         subject:'{{ trans('content.problem_manager.Subject') }}',
         department:'{{ trans('content.problem_manager.Department') }}',
         category:'{{ trans('content.problem_manager.Category') }}',
         subcategory:'{{ trans('content.problem_manager.Subcategory') }}',
         expires:'{{ trans('content.problem_manager.Expires') }}',
         updated_at:'{{ trans('content.problem_manager.UpdatedAt') }}',
         status:'{{ trans('content.problem_manager.Status') }}',
      };
      config.token = "{{ csrf_token() }}";
      new problemlist(config);
   });
</script>
@endpush


