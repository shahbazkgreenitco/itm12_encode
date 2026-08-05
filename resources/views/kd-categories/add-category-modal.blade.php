{{--
/**
------------------------------------------------------------
File: add-category-modal.blade.php
Module: kd-category
kD-D/19/05
------------------------------------------------------------
Version: 1.0.0
Author: Manjeet V
Page ID: #KD-C-add-001
Created On: 2026-05-19
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}
{{-- make changes to above commneted info --}}
<div class="amg-modal amg-form-modal modal fade" id="add-category-modal" tabindex="-1" aria-labelledby="vertical-center-modal"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="categoryAddModal" name="categoryAddModal" class="amg-form-theme">
                @csrf
                <div class="modal-header d-flex align-items-center">
                    <h3 class="modal-title" id="myLargeModalLabel">
                    </h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close" aria-label="Close">
                        <svg viewBox="0 0 31 31" fill="none" ><path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" /></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-0 mb-xl-3 px-4">
                        <!-- company -->
                      
                        <div class="col-12 col-xl-6">
                            <div class="amg-form-field-row">
                                <div class="amg-form-field d-flex align-items-center mb-3 amg-form-field-row">
                                    <label class="form-label b1-text me-2 mb-0 text-end required">{{ trans('config.kd_category_fields.mdl_company')}}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <select name="company" id="company" class="form-select"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                     
                        <!-- Category Name -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="category_name"
                                    class="form-label b1-text me-2 mb-0 text-end required">{{ trans('config.kd_category_fields.mdl_category_name')}}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-type"></i>
                                    </span>
                                    <input type="text" name="category_name" id="category_name" class="form-control form-input equal-width-text-input" placeholder="{{ trans('config.kd_category_fields.mdl_enter_category_name') }}">
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-0 mb-xl-3 px-4">
                        <!-- Department -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6 departmentSection">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="department" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('config.kd_category_fields.mdl_department') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-gear" viewBox="0 0 16 16">
                                            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
                                            <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.386 1.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                                        </svg>
                                    </span>
                                    <select name="department" id="department" class="form-select"></select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <!-- parent cat -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="parent_category"
                                    class="form-label b1-text me-2 mb-0 text-end">{{ trans('config.kd_category_fields.mdl_parent_category') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
                                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                                            <path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0"/>
                                        </svg>
                                    </span>
                                    <select name="parent_category" id="parent_category" class="form-select">
                                    </select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-0 mb-xl-3 px-4">
                        <!-- Content -->
                        <div class="col-xl-12 row">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="content"
                                    class="form-label b1-text me-2 mb-0 text-end required">{{ trans('config.kd_category_fields.mdl_content')}}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                                            <path d="M4.5 3a2.5 2.5 0 0 1 5 0v7.5a1.5 1.5 0 0 1-3 0V4a.5.5 0 0 1 1 0v6.5a.5.5 0 0 0 1 0V3a1.5 1.5 0 0 0-3 0v7.5a2.5 2.5 0 0 0 5 0V4a.5.5 0 0 1 1 0v6.5a3.5 3.5 0 0 1-7 0z"/>
                                        </svg>
                                    </span>
                                    <div class="col-12">
                                        <textarea name="content" id="content" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <div class="amg-btn-group mt-3">
                        <button type="button" class="amg-btn btn-submit-category amg-btn-primary" id="btn-submit-category">
                        </button>
                        <button class="amg-btn amg-btn-secondary" id="btnClear" data-bs-dismiss="modal" type="button">{{ trans('config.kd_category_fields.cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
