{{--
/**
------------------------------------------------------------
File: modal_html.blade.php
Module: Category
CAT/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #CAT-002
Created On: 2026-04-27
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version  (SAFDAR ALI) - 2026-04-27 - #CAT-002
[1.0.1] - Updated file structure and fix the feedback points (SAFDAR ALI) - 2026-04-28 - #CAT-002
[1.0.2] - corrected modal closing icon (manjeet) - 2026-07-26 - #CAT -002
------------------------------------------------------------
*/
--}}

<div id="categoryMdl" class="amg-modal amg-form-modal modal fade user-mdl-box" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="categoryMdlForm" method="post" autocomplete="off">
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title s2-text px-4">{{ trans('config.categories_fields.add_category_details') }}</h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-2" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none"><path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor"></path></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">
                            <div class="col-xl-6 mt-3">
                                <label class="form-label b1-text ">
                                    {{ trans('config.categories_fields.category_name') }} <span
                                        class="required"></span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.0098 15.9998L11.9998 16.0109" stroke="#131927"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            </path>
                                            <path d="M12.0098 11.9998L11.9998 12.0109" stroke="#131927"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            </path>
                                            <path d="M12.0098 7.99977L11.9998 8.01088" stroke="#131927"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            </path>
                                            <path d="M8.00977 11.9998L7.99977 12.0109" stroke="#131927"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            </path>
                                            <path d="M16.0098 11.9998L15.9998 12.0109" stroke="#131927"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            </path>
                                            <path
                                                d="M21 3.6V20.4C21 20.7314 20.7314 21 20.4 21H3.6C3.26863 21 3 20.7314 3 20.4V3.6C3 3.26863 3.26863 3 3.6 3H20.4C20.7314 3 21 3.26863 21 3.6Z"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </svg>
                                    </span>

                                    <input type="text" name="name" id="name" class="form-control" placeholder="{{ trans('config.categories_fields.category_name') }}">
                                </div>

                                <div class="amg-form-error-wrap"></div>
                            </div>

                            <div class="col-xl-6 mt-3">
                                <label class="form-label b1-text">
                                    {{ trans('config.categories_fields.asset_department') }}
                                </label>

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

                                    <select name="departments_id" id="departments_id" class="form-select"></select>
                                </div>

                                <div class="amg-form-error-wrap"></div>
                            </div>

                            <div class="col-xl-6 mt-3">
                                <label class="form-label b1-text">
                                    {{ trans('config.categories_fields.type') }} <span class="required"></span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">

                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M21 7.35304L21 16.647C21 16.8649 20.8819 17.0656 20.6914 17.1715L12.2914 21.8381C12.1102 21.9388 11.8898 21.9388 11.7086 21.8381L3.30861 17.1715C3.11814 17.0656 3 16.8649 3 16.647L2.99998 7.35304C2.99998 7.13514 3.11812 6.93437 3.3086 6.82855L11.7086 2.16188C11.8898 2.06121 12.1102 2.06121 12.2914 2.16188L20.6914 6.82855C20.8818 6.93437 21 7.13514 21 7.35304Z"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M3.52844 7.29314L11.7086 11.8377C11.8898 11.9384 12.1102 11.9384 12.2914 11.8377L20.5 7.27734"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M12 21L12 12" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>

                                    <select name="category_type" id="category_type" autocomplete="off"
                                        class="form-select" placeholder="Select Category">
                                        <option value="">{{ trans('config.categories_fields.select_type') }}
                                        </option>
                                        <option value="asset" data-id="1"
                                            @if (old('category_type') == 'asset') selected @endif>Asset</option>
                                        <option value="accessory" data-id="6"
                                            @if (old('category_type') == 'accessory') selected @endif>Accessory</option>
                                        <option value="consumable" data-id="5"
                                            @if (old('category_type') == 'consumable') selected @endif>Consumable</option>
                                        <option value="component" data-id="4"
                                            @if (old('category_type') == 'component') selected @endif>Component</option>
                                        <option value="license" data-id="7"
                                            @if (old('category_type') == 'license') selected @endif>License</option>
                                    </select>
                                </div>

                                <div class="amg-form-error-wrap"></div>
                            </div>

                            <div class="col-xl-6 mt-3">
                                <label class="form-label b1-text">
                                    {{ trans('config.categories_fields.threshold') }}
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">

                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 4L12 8" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4 8L6.5 10.5" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M17.5 10.5L20 8" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M3 17H6" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 17L13 11" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M18 17H21" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M8.5 20.001H4C2.74418 18.3295 2 16.2516 2 14C2 8.47715 6.47715 4 12 4C17.5228 4 22 8.47715 22 14C22 16.2516 21.2558 18.3295 20 20.001L15.5 20"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M12 23C13.6569 23 15 21.6569 15 20C15 18.3431 13.6569 17 12 17C10.3431 17 9 18.3431 9 20C9 21.6569 10.3431 23 12 23Z"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                    </span>

                                    <input type="text" name="threshold" id="threshold" class="form-control" placeholder="Threshold">
                                </div>

                                <div class="amg-form-error-wrap"></div>

                                <div class="form-check mt-3 ms-1">
                                    <input class="form-check-input" type="checkbox" id="alerts_enabled"
                                        name="alerts_enabled" value="1">
                                    <label class="form-check-label">
                                        {{ trans('config.categories_fields.send_threshold_alerts') }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-6 mt-3">
                                <label class="form-label b1-text">
                                    {{ trans('config.categories_fields.account_type') }}
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">

                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7 18V17C7 14.2386 9.23858 12 12 12C14.7614 12 17 14.2386 17 17V18"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round" />
                                            <path
                                                d="M12 12C13.6569 12 15 10.6569 15 9C15 7.34315 13.6569 6 12 6C10.3431 6 9 7.34315 9 9C9 10.6569 10.3431 12 12 12Z"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <circle cx="12" cy="12" r="10" stroke="#131927"
                                                stroke-width="1.5" />
                                        </svg>

                                    </span>

                                    <select name="account_type_id" id="account_type_id" class="form-select"></select>
                                </div>

                                <div class="amg-form-error-wrap"></div>
                            </div>

                            <div class="col-xl-6 mt-3">
                                <label class="form-label b1-text">
                                    {{ trans('config.categories_fields.service_cycle_period') }}
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">

                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" stroke="#131927"
                                                stroke-width="1.5" />
                                            <path
                                                d="M16.5829 9.66667C15.8095 8.09697 14.043 7 11.9876 7C9.38854 7 7.25148 8.75408 7 11"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M14.4941 9.72222H16.4003C16.7317 9.72222 17.0003 9.45359 17.0003 9.12222V7.5"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M7.41707 13.6667C8.19054 15.6288 9.95698 17 12.0124 17C14.6115 17 16.7485 14.8074 17 12"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M9.50586 13.6221H7.59967C7.2683 13.6221 6.99967 13.8908 6.99967 14.2221V16.3999"
                                                stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                    </span>

                                    <input type="text" name="service_cycle_period" id="service_cycle_period"
                                        class="form-control" placeholder="{{ trans('config.categories_fields.service_cycle_period') }}">
                                </div>

                                <div class="amg-form-error-wrap"></div>
                            </div>

                            <div class="col-xl-6 mt-3 ">
                                <label class="form-label b1-text">
                                    {{ trans('config.categories_fields.fieldset') }}
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">

                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 12H12M16 12H12M12 12V8M12 12V16" stroke="#131927"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M7 4H4V7" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4 11V13" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M11 4H13" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M11 20H13" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M20 11V13" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M17 4H20V7" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M7 20H4V17" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M17 20H20V17" stroke="#131927" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>

                                    </span>

                                    <select name="fieldset_id" id="fieldset_id" class="form-select">
                                        <option value="">
                                            {{ trans('config.categories_fields.select_the_custom_fieldset') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="amg-form-error-wrap"></div>
                            </div>

                            <div class="col-lg-12 mt-3 ">
                                <label class="form-label b1-text  mb-2">
                                    {{ trans('config.categories_fields.category_eula') }}
                                </label>
                                <textarea name="eula_text" id="eula_text" class="form-control" rows="4" placeholder="{{ trans('config.categories_fields.eula') }}"></textarea>
                            </div>

                            <div class="col-md-12 mt-lg-2">
                                <div class="row g-3">

                                    <div class="col-xl-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="use_default_eula" name="use_default_eula">
                                            <label
                                                class="form-check-label">{{ trans('config.categories_fields.show_default_eula') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="require_acceptance" name="require_acceptance">
                                            <label
                                                class="form-check-label">{{ trans('config.categories_fields.user_acceptance_required') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 mb-0 mb-lg-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="checkin_email" name="checkin_email">
                                            <label
                                                class="form-check-label">{{ trans('config.categories_fields.intimate_user_email_on_checkin') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 mb-0 mb-lg-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="checkout_email_accept" name="checkout_email_accept">
                                            <label
                                                class="form-check-label">{{ trans('config.categories_fields.intimate_user_email_on_checkout_accept') }}</label>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <label
                                    class="form-label b1-text mb-2">{{ trans('config.categories_fields.attachment') }}</label>
                                <input type="file" name="category_image" id="category_image"
                                    class="form-control">
                                <small
                                    class="text-muted">{{ trans('config.categories_fields.attachments_info') }}</small>
                                <input type="hidden" name="delete_img" id="delete_img" value="0">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-trash del_attach text-danger"
                                        style="cursor:pointer; display:none;" title="Remove Image"></i>
                                    <img class="img_attach-class"
                                        style="width:56px;height:56px; display:none; border-radius:6px;" />
                                    <div class="img_name-class small text-muted"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end px-4 py-4 border-top">
                    <button type="button" class="amg-btn amg-btn-secondary amg-btn-md"
                        data-bs-dismiss="modal">{{ trans('config.categories_fields.cancel') }}</button>
                    <button type="button" id="btnSubmit"
                        class="amg-btn amg-btn-primary amg-btn-md" style="min-width: 150px">{{ trans('config.categories_fields.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
