@extends('layouts.layout1')
@section('title', trans("content.ticket_task_report.ticket_task_report"))

@section('content')
<main class="main-content" id="mainContent">
    <div class="container-fluid px-0">
        <div class="header-actions-wrapper d-flex align-items-center justify-content-between px-4">
            <div class="mb-0">
                 <h3 class="h3-text mb-0">{{ trans("content.ticket_task_report.ticket_task_report") }}</h3>
            </div>
            <div class="d-flex gap-8">
                <button class="header-icon-btn header-icon-btn-sm btn-filter-open" type="button" data-bs-toggle="tooltip" title="{{ trans("problem_category.service_ticket_fields.Filter") }}">
                    <svg viewBox="0 0 20 18" fill="none" ><path d="M19.3658 0.893462C19.2504 0.626753 19.0591 0.399864 18.8157 0.241013C18.5723 0.0821617 18.2877 -0.00164211 17.997 2.43793e-05H1.49705C1.20673 0.000596404 0.922821 0.0854027 0.679756 0.244155C0.436692 0.402908 0.244919 0.628785 0.127702 0.894384C0.0104851 1.15998 -0.0271385 1.45389 0.0193952 1.74045C0.065929 2.02701 0.19462 2.29391 0.389858 2.50877L0.397358 2.51721L6.74705 9.29721V16.5C6.74698 16.7715 6.8206 17.0379 6.96004 17.2708C7.09948 17.5038 7.29953 17.6945 7.53885 17.8227C7.77816 17.9508 8.04778 18.0117 8.31894 17.9986C8.59011 17.9856 8.85266 17.8993 9.07861 17.7488L12.0786 15.7481C12.2843 15.6112 12.4529 15.4255 12.5695 15.2076C12.6861 14.9898 12.7471 14.7465 12.747 14.4994V9.29721L19.0977 2.51721L19.1052 2.50877C19.3025 2.29489 19.4324 2.02764 19.4788 1.74037C19.5251 1.4531 19.4858 1.15854 19.3658 0.893462ZM11.4514 8.4919C11.3217 8.62945 11.2487 8.81094 11.247 9.00002V14.4994L8.24705 16.5V9.00002C8.2471 8.80958 8.17471 8.62624 8.04455 8.48721L1.49705 1.50002H17.997L11.4514 8.4919Z" fill="currentColor" /></svg>
                    <span class="b6-text">{{ trans("problem_category.service_ticket_fields.Filter") }}</span>
                    <span class="filter-count-badge d-none" aria-label="Active filters">0</span>
                </button>
                <div id="short_wraper" class="position-relative">
                    <button type="button" class="header-icon-btn header-icon-btn-sm " id="srqSortDrop">
                        <span class="sort-action">
                            <i id="sortDirectionIcon" class="bi bi-sort-down"></i>
                        </span>
                        <span class="dropdown-action">
                            <i class="bi bi-caret-down-fill"></i>
                        </span>
                    </button>
                    <ul class="dropdown-menu" id="short_items"></ul>
                </div>
                <button class="header-icon-btn-only header-icon-btn-only-sm btn-download"
                    data-bs-toggle="tooltip" data-bs-original-title="{{ trans('internal_place.view.download') }}">
                    <svg  width="17" height="20" viewBox="0 0 17 20" fill="none"><path d="M16.2806 5.46938L11.0306 0.219375C10.9609 0.149749 10.8782 0.094539 10.7871 0.0568979C10.6961 0.0192569 10.5985 -7.72394e-05 10.5 2.31899e-07H1.5C1.10218 2.31899e-07 0.720644 0.158035 0.43934 0.43934C0.158035 0.720645 0 1.10218 0 1.5V18C0 18.3978 0.158035 18.7794 0.43934 19.0607C0.720644 19.342 1.10218 19.5 1.5 19.5H15C15.3978 19.5 15.7794 19.342 16.0607 19.0607C16.342 18.7794 16.5 18.3978 16.5 18V6C16.5001 5.90148 16.4807 5.80391 16.4431 5.71286C16.4055 5.62182 16.3503 5.53908 16.2806 5.46938ZM11.25 2.56031L13.9397 5.25H11.25V2.56031ZM15 18H1.5V1.5H9.75V6C9.75 6.19891 9.82902 6.38968 9.96967 6.53033C10.1103 6.67098 10.3011 6.75 10.5 6.75H15V18ZM11.0306 12.2194C11.1004 12.289 11.1557 12.3717 11.1934 12.4628C11.2312 12.5538 11.2506 12.6514 11.2506 12.75C11.2506 12.8486 11.2312 12.9462 11.1934 13.0372C11.1557 13.1283 11.1004 13.211 11.0306 13.2806L8.78063 15.5306C8.71097 15.6004 8.62825 15.6557 8.5372 15.6934C8.44616 15.7312 8.34856 15.7506 8.25 15.7506C8.15144 15.7506 8.05384 15.7312 7.96279 15.6934C7.87175 15.6557 7.78903 15.6004 7.71937 15.5306L5.46937 13.2806C5.32864 13.1399 5.24958 12.949 5.24958 12.75C5.24958 12.551 5.32864 12.3601 5.46937 12.2194C5.61011 12.0786 5.80098 11.9996 6 11.9996C6.19902 11.9996 6.38989 12.0786 6.53063 12.2194L7.5 13.1897V9C7.5 8.80109 7.57902 8.61032 7.71967 8.46967C7.86032 8.32902 8.05109 8.25 8.25 8.25C8.44891 8.25 8.63968 8.32902 8.78033 8.46967C8.92098 8.61032 9 8.80109 9 9V13.1897L9.96937 12.2194C10.039 12.1496 10.1217 12.0943 10.2128 12.0566C10.3038 12.0188 10.4014 11.9994 10.5 11.9994C10.5986 11.9994 10.6962 12.0188 10.7872 12.0566C10.8783 12.0943 10.961 12.1496 11.0306 12.2194Z" fill="currentColor"/></svg>
                </button>
            </div>
        </div>

        <div class="tab-pane fade show active" id="main-user-list-wrapper">
            <div class="card">
                <div class="card-body table-responsive">
                    <div class="d-flex align-items-center gap-2 mb-1" data-select2-id="select2-data-5-1om7">
                        <!-- show select -->
                        <div id="customLengthContainer" class="col-auto" data-select2-id="select2-data-4-sbib">
                            <select id="showSelect" class="showSelect amg-table-pagination-dropdown userModulePageLenth user-list-page-length">
                                <option value="10" selected>{{ trans('internal_place.view.show') }} (10)</option>
                                <option value="25">{{ trans('internal_place.view.show') }} (25)</option>
                                <option value="50">{{ trans('internal_place.view.show') }} (50)</option>
                                <option value="100">{{ trans('internal_place.view.show') }} (100)</option>
                            </select>
                        </div>

                        <!-- spacer -->
                        <div class="flex-grow-1"></div>

                        <!-- searchbar -->
                        <div>
                            <div class="amg-list-searchbar">
                                <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                </svg>
                                <input type="text" class="amg-list-searchbar__input" id="tableSearch" placeholder="{{ trans('internal_place.view.search') }}...">
                            </div>
                        </div>

                        <!-- refresh button -->
                        <button class="amg-refresh-btn btn-reload-list">
                            <svg class="amg-refresh-btn__icon" width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                            </svg>
                            <span>{{ trans('internal_place.view.refresh') }}</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="mytable" class="mytable amg-datatable table display app-data-table" style="width:100%">
                            <thead>
                                <tr id="tHeadeRow">
                                    <th>{{ trans("content.ticket_task_report.task_id") }}</th>
                                    <th>{{ trans("content.ticket_task_report.task_name") }}</th>
                                    <th>{{ trans("content.ticket_task_report.task_content") }}</th>
                                    <th>{{ trans("content.ticket_task_report.status") }}</th>
                                    <th>{{ trans("content.ticket_task_report.priority") }}</th>
                                    <th>{{ trans("content.ticket_task_report.start_date") }}</th>
                                    <th>{{ trans("content.ticket_task_report.end_date") }}</th>
                                    <th>{{ trans("content.ticket_task_report.actual_end_date") }}</th>
                                    <th>{{ trans("content.ticket_task_report.cost") }}</th>
                                    <th>{{ trans("content.ticket_task_report.assign_to") }}</th>
                                    <th>{{ trans("content.ticket_task_report.ticket_id") }}</th>
                                    <th>{{ trans("content.ticket_task_report.ticket_subject") }}</th>
                                    <th>{{ trans("content.ticket_task_report.ticket_department") }}</th>
                                    <th>{{ trans("content.ticket_task_report.ticket_pc") }}</th>
                                    <th>{{ trans("content.ticket_task_report.ticket_sc") }}</th>
                                    <th>{{ trans("content.ticket_task_report.task_created_at") }}</th>
                                    <th>{{ trans("content.ticket_task_report.task_updated_at") }}</th>
                                </tr>
                            </thead>
                            <colgroup>
                            </colgroup>
                            <tbody id="lg"></tbody>
                        </table>
                    </div>
                    <div class="row mar-top">
                        <div class="col-lg-5">
                            <div id="page-btm-summary" style="margin-top: 9px"></div>
                        </div>
                        <div class="col-lg-7">
                            <div id="pagebtns"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Filter Modal -->
    <div class="amg-modal modal fade" id="FilterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h5 class="modal-title" id="filterModalLabel">
                        Filter Tickets
                    </h5>
                    <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31"
                            fill="none">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="filter-group row g-3 mb-3" data-filter-name="department category">
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                                {{ trans("content.filter_heading.filter_by_department") }}
                            </label>
                            <select id="filter_by_department" name="filter_by_department[]" multiple="multiple" class="form-select select2-modal"></select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                            {{ trans("content.filter_heading.Filter_By_Problem_Category") }}
                            </label>
                            <select id="filter_by_problem_category" name="filter_by_problem_category[]" multiple="multiple" class="form-select select2-modal"></select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                                {{ trans("content.filter_heading.Filter_By_Sub_Category") }}
                            </label>
                            <select id="filter_by_sub_category" name="filter_by_sub_category[]" multiple="multiple" class="form-select select2-modal"></select>
                        </div>
                    </div>

                    <!-- Row 2: Status, Date, Date Range -->
                    <div class="filter-group row g-3 mb-3" data-filter-name="status date">
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                                {{ trans("content.filter_heading.filter_by_status") }}
                            </label>
                            <select id="filter_by_status" name="filter_by_status[]" multiple class="form-select select2-modal"></select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                                {{ trans("content.filter_heading.filter_by_date") }}
                            </label>
                            <select name="filter_by_date" id="filter_by_date" autocomplete="off" class="form-select">
                                <option value="null">{{ trans("content.report_fields.No_Filter") }}</option>
                                <option value="1">{{ trans("content.ticket_task_report.start_date") }}</option>
                                <option value="2">{{ trans("content.ticket_task_report.end_date") }}</option>
                                <option value="3">{{ trans("content.ticket_task_report.actual_end_date") }}</option>
                                <option value="4">{{ trans("content.ticket_task_report.task_created_at") }}</option>
                                <option value="5">{{ trans("content.ticket_task_report.task_updated_at") }}</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                                {{ trans("content.filter_heading.filter_by_daterange") }}
                            </label>
                            <div id="reportrange" class="form-control daterange-picker" style="cursor: pointer;">
                                <i class="bi bi-calendar3 me-2"></i>
                                <span></span>
                                <i class="bi bi-chevron-down float-end"></i>
                                <input type="hidden" name="daterange" id="daterange">
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Priority, Assigned To -->
                    <div class="filter-group row g-3 mb-3" data-filter-name="priority assignee">
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                                {{ trans("content.filter_heading.Filter_By_Priority") }}
                            </label>
                            <select name="filter_by_priority[]" id="filter_by_priority" multiple="multiple" class="form-select select2-modal"></select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">
                                {{ trans("content.filter_heading.filter_by_assigned_to") }}
                            </label>
                            <select name="filter_by_handler[]" id="filter_by_handler" multiple="multiple" class="form-select select2-modal"></select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end mb-4 pb-4 py-0">
                    <div class="amg-btn-group mt-3 gap-3 px-4">
                        <button type="button" id="btnClrFilter"
                            class="amg-btn amg-btn-ghost bg-black text-white s2-text" style="min-width: 200px;">
                            {{ trans('content.filter_heading.Clear') }}
                        </button>

                        <button type="button" class="amg-btn amg-btn-primary s2-text btn-filter"
                            style="min-width: 200px;">
                        {{ trans('user.user_filter.apply_filter') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@push('css')
<style>
    #short_items .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 10px;
        font-size: 12px;
    }

    #short_items .flex-grow-1 {
        flex: 1;
    }

    #short_items .like-radio {
        width: 16px;
        height: 16px;
        border: 2px solid #cbd5e1;
        border-radius: 50%;
        flex-shrink: 0;
        position: relative;
    }

    #short_items .dropdown-item.active {
        background: #fef2f2;
        color: #dc2626;
    }

    #short_items .dropdown-item.active .like-radio {
        border-color: currentColor;
    }

    #short_items .dropdown-item.active .like-radio::after {
        content: "";
        position: absolute;
        inset: 50%;
        width: 8px;
        height: 8px;
        background: currentColor;
        border-radius: 50%;
        transform: translate(-50%, -50%);
    }

    /* Sort Button */
    #short_wraper .header-icon-btn {
        display: flex;
        align-items: center;
        padding: 0;
        overflow: hidden;
    }

    #short_wraper .sort-action,
    #short_wraper .dropdown-action {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 34px;
    }

    #short_wraper .sort-action {
        padding: 0 8px;
        cursor: pointer;
        font-size: 18px
    }

    #short_wraper .dropdown-action {
        padding: 0 8px;
        border-left: 1px solid #d1d5db;
        cursor: pointer;
    }

    #short_wraper { 
        position: relative;
        display: inline-block;
    }

    #short_items.show {
        display: block;
        position: absolute;
        left: -3rem;
    }

    #short_wraper .sort-action:hover,
    #short_wraper .dropdown-action:hover {
        background: rgba(0, 0, 0, .05);
    }

    /* Dark Mode */
    html[data-bs-theme="dark"] #short_items .dropdown-item.active {
        background: rgba(220, 38, 38, .25);
        color: #fff;
    }

    html[data-bs-theme="dark"] #short_wraper .dropdown-action {
        border-left-color: #4b5563;
    }

    html[data-bs-theme="dark"] #short_wraper .sort-action:hover,
    html[data-bs-theme="dark"] #short_wraper .dropdown-action:hover {
        background: rgba(255, 255, 255, .08);
    }
</style>
@endpush
@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('js/reports/tickets/ticket_task_report.js') !!}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var config = {};
        config.url = {};
        config.url.list_tickets = "{{ url('reports/tickets/jx-ticket-task-report') }}";
        config.url.export_task_xls = "{{ url('reports/export-ticket-task-report') }}";
        config.url.departments_by_company = "{{ url('departments/by-company/privilage') }}";
        config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
        config.url.task_info = "{{ url('task-management/info') }}";
        config.url.ticket_info = "{{ url('ticket') }}";
        config.user = {!! json_encode(Auth::user()->only("id", "first_name", "last_name", "username", "company_id")) !!};
        config.sort_fields = {!! json_encode($sort_fields) !!};
        config.company = {!! json_encode($dashboardCompanyId) !!};
        config.sort_dir = {id:1, dir:2};
        config.getUserByQuery = "{{ url('getUserByQuery') }}";
        config.status = {!! json_encode($vd->statuses) !!};
        config.priority = {!! json_encode($vd->priorities) !!};
        config.translations = {
            no_details: '{{ trans('content.report_fields.no_details') }}',
            select_assign_to: '{{ trans('content.ticket_task_report.select_assign_to') }}',
            select_department: '{{ trans('content.filter_heading.select_department') }}',
            select_Problem_Category: '{{ trans('content.filter_heading.select_pc') }}',
            select_Sub_Category: '{{ trans('content.filter_heading.select_sc') }}',
            select_status: '{{ trans('content.filter_heading.select_status') }}',
            select_Priority: '{{ trans('content.filter_heading.select_priority') }}',
        };
        new TicketTaskReport(config);
    });
</script>
@endpush