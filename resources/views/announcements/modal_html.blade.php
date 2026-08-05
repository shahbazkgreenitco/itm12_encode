{{-- * ------------------------------------------------------------
* File: modal_html.blade.php
* Module: Announcement Module
* Anounc/26/02
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #002
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
<div class="amg-modal amg-form-modal modal fade" id="announcementMdl" tabindex="-1" aria-labelledby="vertical-center-modal"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="announcementForm" method="post" class="w-100 amg-form-theme" autocomplete="off"
            onsubmit="return false;">
            <div class="modal-content rounded-5">
                <!-- Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">
                        <span id="headingLabel">Add</span> Announcement
                    </h3>

                   <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row mb-0 mb-xl-3 px-4">
                            <!-- Company -->
                            <div class="col-12 col-xl-6 mb-3 mb-xl-0">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="company_id"
                                        class="form-label b1-text me-2 mb-0 text-end required">Company</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7 9.01L7.01 8.99889" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M11 9.01L11.01 8.99889" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M7 13.01L7.01 12.9989" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M11 13.01L11.01 12.9989" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M7 17.01L7.01 16.9989" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M11 17.01L11.01 16.9989" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M15 21H3.6C3.26863 21 3 20.7314 3 20.4V5.6C3 5.26863 3.26863 5 3.6 5H9V3.6C9 3.26863 9.26863 3 9.6 3H14.4C14.7314 3 15 3.26863 15 3.6V9M15 21H20.4C20.7314 21 21 20.7314 21 20.4V9.6C21 9.26863 20.7314 9 20.4 9H15M15 21V17M15 9V13M15 13H17M15 13V17M15 17H17"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <select name="company_id" id="company_id" class="form-select"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="col-12 col-xl-6 mb-3 mb-xl-0">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="title"
                                        class="form-label b1-text me-2 mb-0 text-end required">Title</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19 7V5L5 5V7" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12 5L12 19M12 19H10M12 19H14" stroke="#131927"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <input type="text" name="title" id="title" class="form-control"
                                            placeholder="Enter title">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0 mb-xl-3 px-4">
                            <!-- Start Date -->
                            <div class="col-12 col-xl-6 mb-3 mb-xl-0">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="start_date"
                                        class="form-label b1-text me-2 mb-0 text-end required">Start Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M13 21H5C3.89543 21 3 20.1046 3 19V10H21V13M15 4V2M15 4V6M15 4H10.5"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M3 10V6C3 4.89543 3.89543 4 5 4H7" stroke="#131927"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M7 2V6" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M21 10V6C21 4.89543 20.1046 4 19 4H18.5" stroke="#131927"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M14.9922 18H17.9922M21 18H17.9922M17.9922 18V15M17.9922 18V21"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                        <input type="text" name="start_date" id="start_date"
                                            class="form-control">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-12 col-xl-6 mb-3 mb-xl-0">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="end_date" class="form-label b1-text me-2 mb-0 text-end required">End
                                        Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M13 21H5C3.89543 21 3 20.1046 3 19V10H21V13M15 4V2M15 4V6M15 4H10.5"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M3 10V6C3 4.89543 3.89543 4 5 4H7" stroke="#131927"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M7 2V6" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M21 10V6C21 4.89543 20.1046 4 19 4H18.5" stroke="#131927"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M14.9922 18H21" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                        <input type="text" name="end_date" id="end_date" class="form-control">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                        </div>

                        <div class="row mb-0 mb-xl-3 px-4">

                            <!-- Department -->
                            <div class="col-12 mb-3 mb-xl-0 col-xl-6">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="department"
                                        class="form-label b1-text me-2 mb-0 text-end">Department</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M21 7.35304L21 16.647C21 16.8649 20.8819 17.0656 20.6914 17.1715L12.2914 21.8381C12.1102 21.9388 11.8898 21.9388 11.7086 21.8381L3.30861 17.1715C3.11814 17.0656 3 16.8649 3 16.647L2.99998 7.35304C2.99998 7.13514 3.11812 6.93437 3.3086 6.82855L11.7086 2.16188C11.8898 2.06121 12.1102 2.06121 12.2914 2.16188L20.6914 6.82855C20.8818 6.93437 21 7.13514 21 7.35304Z"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M12 21L12 12" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path
                                                    d="M12.5 11V21C12.5 21.2761 12.2761 21.5 12 21.5C11.7239 21.5 11.5 21.2761 11.5 21V11C11.5 10.7239 11.7239 10.5 12 10.5C12.2761 10.5 12.5 10.7239 12.5 11Z"
                                                    fill="#131927" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                        <select name="department_id[]" id="department" class="form-control select2"
                                            multiple></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="col-12 col-xl-6 mb-3 mb-xl-0 ">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="location"
                                        class="form-label b1-text me-2 mb-0 text-end">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M20 10C20 14.4183 12 22 12 22C12 22 4 14.4183 4 10C4 5.58172 7.58172 2 12 2C16.4183 2 20 5.58172 20 10Z"
                                                    stroke="#131927" stroke-width="1.5"></path>
                                                <path
                                                    d="M12 11C12.5523 11 13 10.5523 13 10C13 9.44772 12.5523 9 12 9C11.4477 9 11 9.44772 11 10C11 10.5523 11.4477 11 12 11Z"
                                                    fill="#131927" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                        <select multiple name="location_id[]" id="location"
                                            class="form-control select2"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                        </div>

                        <div class="row mb-0 mb-xl-3 px-4">

                            <!-- Users -->
                            <div class="col-12 mb-3 mb-xl-0 col-xl-6 userSection">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="users" class="form-label b1-text me-2 mb-0 text-end">Users</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7 18V17C7 14.2386 9.23858 12 12 12C14.7614 12 17 14.2386 17 17V18"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round">
                                                </path>
                                                <path
                                                    d="M12 12C13.6569 12 15 10.6569 15 9C15 7.34315 13.6569 6 12 6C10.3431 6 9 7.34315 9 9C9 10.6569 10.3431 12 12 12Z"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <circle cx="12" cy="12" r="10" stroke="#131927"
                                                    stroke-width="1.5"></circle>
                                            </svg>
                                        </span>
                                        <select multiple name="users[]" id="users" class="form-control select2"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-12 mb-3 mb-xl-0 col-xl-6">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="status_id"
                                        class="form-label b1-text me-2 mb-0 text-end">Status</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z"
                                                    fill="#131927" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path
                                                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                        <select name="status_id" id="status_id" class="form-select">
                                            <option value="null">Select</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-0 mb-xl-3 px-4">
                            <!-- Content -->
                            <div class="col-xl-12 ">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="content"
                                        class="form-label b1-text me-2 mb-0 text-end required">Announcement</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M21.4383 11.6622L12.2483 20.8522C11.1225 21.9781 9.59552 22.6106 8.00334 22.6106C6.41115 22.6106 4.88418 21.9781 3.75834 20.8522C2.63249 19.7264 2 18.1994 2 16.6072C2 15.015 2.63249 13.4881 3.75834 12.3622L12.9483 3.17222C13.6989 2.42166 14.7169 2 15.7783 2C16.8398 2 17.8578 2.42166 18.6083 3.17222C19.3589 3.92279 19.7806 4.94077 19.7806 6.00222C19.7806 7.06368 19.3589 8.08166 18.6083 8.83222L9.40834 18.0222C9.03306 18.3975 8.52406 18.6083 7.99334 18.6083C7.46261 18.6083 6.95362 18.3975 6.57834 18.0222C6.20306 17.6469 5.99222 17.138 5.99222 16.6072C5.99222 16.0765 6.20306 15.5675 6.57834 15.1922L15.0683 6.71222"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                        <textarea name="content" id="content" class="form-control"></textarea>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0 mb-xl-3 px-4">
                            <!-- Check All Users -->
                            <div class="col-12 mb-3 mb-xl-0 checkAllBox">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="all_user_check" class="form-label b1-text me-2 mb-0 text-end">Check
                                        for All Users</label>
                                    <div class="input-group">
                                        <div class="d-flex align-items-center border-0">
                                            <input type="checkbox" name="all_user_check"
                                                class="form-check-input mt-0" id="all_user_check" value="1">
                                        </div>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="amg-form-footer modal-footer d-flex justify-content-end py-4 border-top">              
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-block col-md-2"
                        style="min-width:150px">Save</button>
                    <button type="button" id="btnClear"
                        class="amg-btn amg-btn-ghost s2-text bg-black text-white announcementForm amg-btn-block col-md-2 ms-2"
                        data-bs-dismiss="modal">Close</button>                
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .note-editor {
        width: 100%;
    }
</style>
