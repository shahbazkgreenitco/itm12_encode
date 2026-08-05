{{--
/**
* ------------------------------------------------------------
* File: print_modal.blade.php
* Module: Devices
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-022
* Created On: 2026-14-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div id="print-modal" class="amg-modal amg-form-modal modal fade user-mdl-box" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <form id="print-modal-form" name="print-modal-form" method="post" action="#" class="form-horizontal amg-form-theme w-100" onsubmit="return false;">
            <div class="modal-content rounded-5">
                {{-- Header --}}
                <div class="modal-header d-flex align-items-center py-3 px-4">
                    <h3 id="title" class="modal-title s2-text fw-semibold px-2"></h3>
                    <button type="button" class="modal-close px-2" data-bs-dismiss="modal" aria-label="Close">
                        <svg viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="#515151" />
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row g-4">
                            <div class="col-md-12 amg-form-field amg-form-field-row d-inline">
                                <label for="print_opt" class="form-label b1-text mb-2">
                                    {{ trans("content.device_fields.Select_Option") }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="bi bi-chevron-down"></i>
                                    </span>
                                    <select name="print_opt" id="print_opt" class="form-select">
                                        <option value="1">{{ trans("content.device_fields.Print_in_A4_Sheet") }}</option>
                                        <option value="2">{{ trans("content.device_fields.Print_in_Single_Column") }}</option>
                                        <option value="3">{{ trans("content.device_fields.Print_in_Two_Column") }}</option>
                                        @if(in_array(config('app.client'), ['knightfrank']))
                                            <option value="4">{{ trans("content.device_fields.Print_Vertical") }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 amg-form-field amg-form-field-row d-inline">
                                <label for="print_usr_plc" class="form-label b1-text mb-2">
                                    {{ trans("content.device_fields.include_User/Place") }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class="bi bi-chevron-down"></i>
                                    </span>
                                    <select name="print_usr_plc" id="print_usr_plc" class="form-select">
                                        <option>{{ trans("content.device_fields.Select_Option") }}</option>
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 amg-form-field amg-form-field-row d-inline cvr d-none">
                                <label for="print_usr_info" class="form-label b1-text mb-2">
                                    {{ trans("content.device_fields.user_info") }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.0407 16.5627C16.8508 14.5056 15.0172 13.0306 12.8774 12.3314C13.9358 11.7013 14.7582 10.7412 15.2182 9.59845C15.6781 8.45573 15.7503 7.19361 15.4235 6.00592C15.0968 4.81823 14.3892 3.77064 13.4094 3.02402C12.4296 2.2774 11.2318 1.87305 10 1.87305C8.76821 1.87305 7.57044 2.2774 6.59067 3.02402C5.6109 3.77064 4.90331 4.81823 4.57654 6.00592C4.24978 7.19361 4.32193 8.45573 4.78189 9.59845C5.24186 10.7412 6.06422 11.7013 7.12268 12.3314C4.98284 13.0299 3.14925 14.5049 1.9594 16.5627C1.91577 16.6338 1.88683 16.713 1.87429 16.7955C1.86174 16.878 1.86585 16.9622 1.88638 17.0431C1.9069 17.124 1.94341 17.2 1.99377 17.2665C2.04413 17.3331 2.10731 17.3889 2.17958 17.4306C2.25185 17.4724 2.33175 17.4992 2.41457 17.5096C2.49738 17.5199 2.58143 17.5136 2.66176 17.491C2.74209 17.4683 2.81708 17.4298 2.88228 17.3777C2.94749 17.3256 3.00161 17.261 3.04143 17.1877C4.51331 14.6439 7.11487 13.1252 10 13.1252C12.8852 13.1252 15.4867 14.6439 16.9586 17.1877C16.9984 17.261 17.0526 17.3256 17.1178 17.3777C17.183 17.4298 17.258 17.4683 17.3383 17.491C17.4186 17.5136 17.5027 17.5199 17.5855 17.5096C17.6683 17.4992 17.7482 17.4724 17.8205 17.4306C17.8927 17.3889 17.9559 17.3331 18.0063 17.2665C18.0566 17.2 18.0932 17.124 18.1137 17.0431C18.1342 16.9622 18.1383 16.878 18.1258 16.7955C18.1132 16.713 18.0843 16.6338 18.0407 16.5627ZM5.62503 7.50017C5.62503 6.63488 5.88162 5.78902 6.36235 5.06955C6.84308 4.35009 7.52636 3.78933 8.32579 3.4582C9.12521 3.12707 10.0049 3.04043 10.8535 3.20924C11.7022 3.37805 12.4818 3.79473 13.0936 4.40658C13.7055 5.01843 14.1222 5.79799 14.291 6.64665C14.4598 7.49532 14.3731 8.37499 14.042 9.17441C13.7109 9.97384 13.1501 10.6571 12.4306 11.1379C11.7112 11.6186 10.8653 11.8752 10 11.8752C8.84009 11.8739 7.72801 11.4126 6.90781 10.5924C6.0876 9.77219 5.62627 8.66011 5.62503 7.50017Z" fill="currentColor"></path></svg>
                                    </span>
                                    <select name="print_usr_info" id="print_usr_info" class="form-select">
                                        <option value="1" selected>{{ trans("content.device_fields.User_Full_Name") }}</option>
                                        <option value="2">{{ trans("content.device_fields.Username") }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Footer --}}
                <div class="amg-form-footer modal-footer justify-content-end py-4 px-4">
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" id="btnClear" data-bs-dismiss="modal" class="amg-btn amg-btn-secondary s2-text amg-btn-block amg-btn-md">
                            Close
                        </button>
                        <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary s2-text amg-btn-block amg-btn-md">
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>