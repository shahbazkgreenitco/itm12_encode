{{-- * ------------------------------------------------------------
* File: create_modal.blade.php
* Module: Knowledge Document
* KNGD/26/02
* ------------------------------------------------------------
* Version: 1.0.1
* Author: Sandeep Verma
* Page ID: #002
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.1] - Add the Status Option for Public Document (Hrishikesh Pandey)
* [1.0.0] - Initial version (Sandeep Verma)
* ------------------------------------------------------------ --}}
<div class="amg-modal amg-form-modal modal fade" id="articleModal" tabindex="-1" aria-labelledby="vertical-center-modal"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="knowledge_document_form" name="knowledge_document_form" class="amg-form-theme" method="POST"
                action="#">
                @csrf
                <input type="hidden" id="id" name="id" class="hidden" value="" />
                <input type="hidden" name="for_action" id="for_action" value="" />
                <input type="hidden" id="tmp_id" name="tmp_id" value="" />
                <div class="modal-header d-flex align-items-center">
                    <h3 class="modal-title" id="myLargeModalLabel">
                        {{ trans('content.knowledge_document.Create_Article') }}
                    </h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close" aria-label="Close">
                        <svg viewBox="0 0 31 31" fill="currentColor">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-0 mb-xl-3 px-4">

                        <!-- Articale Title -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="title"
                                    class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.knowledge_document.title') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-type"></i>
                                    </span>
                                    <input type="text" name="title" id="title"
                                        class="form-control form-input equal-width-text-input"
                                        placeholder="{{ trans('content.knowledge_document.Enter_the_title') }}">
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-6">
                            <div class="amg-form-field-row">
                                <div class="amg-form-field d-flex align-items-center mb-3 amg-form-field-row">
                                    <label
                                        class="form-label b1-text me-2 mb-0 text-end required">{{ trans('config.kd_category_fields.mdl_company') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <select name="company" id="company" class="form-select"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-0 mb-xl-3 px-4">
                        <!-- Department -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6 departmentSection">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="department_id"
                                    class="form-label b1-text me-2 mb-0 text-end required">{{ trans('config.kd_category_fields.mdl_department') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-gear" viewBox="0 0 16 16">
                                            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
                                            <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.386 1.46c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                                        </svg>
                                    </span>
                                    <select name="department_id" id="department_id" class="form-select"></select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <!-- parent cat -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="parent_category_id"
                                    class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.service_ticket_fields.Parent_Category') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
                                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                                            <path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0"/>
                                        </svg>
                                    </span>
                                    <select name="parent_category_id" id="parent_category_id" class="form-select">
                                    </select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0 mb-xl-3 px-4" id="sub_category_id_cvr">
                        <!-- sub cat -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="sub_category_id"
                                    class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.service_ticket_fields.sub_category') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16">
                                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                                            <path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0"/>
                                        </svg>
                                    </span>
                                    <select name="sub_category_id" id="sub_category_id" class="form-select">
                                    </select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0 mb-xl-3 px-4">
                        <!-- Department -->
                        <div class="col-12 mb-3 mb-xl-0 col-xl-6 departmentSection">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="status"
                                    class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.knowledge_document.article_status') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                            <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                                            <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                                        </svg>
                                    </span>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="1">{{ trans('content.knowledge_document.Public') }}
                                        </option>
                                        <option value="2">{{ trans('content.knowledge_document.Private') }}
                                        </option>
                                        <option value="3">{{ trans('content.knowledge_document.only_allowed_user') }}</option>
                                        <option value="4">{{ trans('content.knowledge_document.non_login_public') }}</option>
                                    </select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-12 mb-3 mt-3 mb-xl-0">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="card_img"
                                        class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.service_ticket_fields.Add_article_image') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-image" viewBox="0 0 16 16">
                                                <path d="M6.502 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                                                <path d="M14 14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zM4 1a1 1 0 0 0-1 1v10l2.224-2.224a.5.5 0 0 1 .61-.075L8 11l2.157-3.02a.5.5 0 0 1 .76-.063L13 10V4.5h-2A1.5 1.5 0 0 1 9.5 3V1z"/>
                                            </svg>
                                        </span>
                                        <input type="file" name="card_img" id="card_img" class="form-control form-input equal-width-text-input">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3 mb-xl-3 px-4">
                        <!-- Content -->
                        <div class="col-xl-12 row form-pd">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="tags"
                                    class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.knowledge_document.tags') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-threads" viewBox="0 0 16 16">
                                            <path d="M6.321 6.016c-.27-.18-1.166-.802-1.166-.802.756-1.081 1.753-1.502 3.132-1.502.975 0 1.803.327 2.394.948s.928 1.509 1.005 2.644q.492.207.905.484c1.109.745 1.719 1.86 1.719 3.137 0 2.716-2.226 5.075-6.256 5.075C4.594 16 1 13.987 1 7.994 1 2.034 4.482 0 8.044 0 9.69 0 13.55.243 15 5.036l-1.36.353C12.516 1.974 10.163 1.43 8.006 1.43c-3.565 0-5.582 2.171-5.582 6.79 0 4.143 2.254 6.343 5.63 6.343 2.777 0 4.847-1.443 4.847-3.556 0-1.438-1.208-2.127-1.27-2.127-.236 1.234-.868 3.31-3.644 3.31-1.618 0-3.013-1.118-3.013-2.582 0-2.09 1.984-2.847 3.55-2.847.586 0 1.294.04 1.663.114 0-.637-.54-1.728-1.9-1.728-1.25 0-1.566.405-1.967.868ZM8.716 8.19c-2.04 0-2.304.87-2.304 1.416 0 .878 1.043 1.168 1.6 1.168 1.02 0 2.067-.282 2.232-2.423a6.2 6.2 0 0 0-1.528-.161"/>
                                        </svg>
                                    </span>
                                    <div class="col-12">
                                        <select name="tags[]" id="tags" class="form-select"
                                            multiple="multiple"></select>
                                    </div>
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
                                    class="form-label b1-text me-2 mb-0 text-end required">{{ trans('config.kd_category_fields.mdl_content') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-text" viewBox="0 0 16 16">
                                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                                            <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8m0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
                                        </svg>
                                    </span>
                                    <div class="col-12 input-group flex-nowrap">
                                        <textarea name="content" id="content" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <div class="col-xl-12 row mt-2">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                <label for="content"
                                    class="form-label b1-text me-2 mb-0 text-end align-self-start pt-4">{{ trans('content.service_ticket_fields.Add_Attachment') }}</label>
                                <div class="input-group">
                                    <div class="col-md-12">
                                        <div id="attachment-dropper-cover" class="amg-uploader" data-amg-uploader
                                            data-multiple="true" data-auto-upload="true" data-max-files="10"
                                            data-max-size="10"
                                            data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv"
                                            data-upload-url="{{ url('knowledge_document/attachment/kd_attachment') }}"
                                            data-remove-url="{{ url('knowledge_document/attachment/kd_attachment_remove') }}"
                                            data-token="{{ csrf_token() }}"
                                            data-record-input="#knowledge_document_form #tmp_id"
                                            data-upload-field="attachment">
                                            <input type="file" id="attachment_input" class="amg-uploader__input"
                                                multiple hidden>
                                            <div id="attachment-dropper" class="amg-uploader__dropzone cursor-pointer">
                                                <div class="amg-uploader__message">
                                                    <span class="amg-uploader__icon-wrap">
                                                        <svg width="20" height="20" viewBox="0 0 20 20"
                                                            fill="none">
                                                            <path
                                                                d="M16.5625 9.05781L9.64687 15.9734C8.76042 16.8599 7.55875 17.3581 6.30469 17.3581C5.05062 17.3581 3.84895 16.8599 2.9625 15.9734C2.07605 15.087 1.57788 13.8853 1.57788 12.6312C1.57788 11.3772 2.07605 10.1755 2.9625 9.28906L9.87812 2.37344C10.4718 1.7797 11.2765 1.44531 12.1156 1.44531C12.9547 1.44531 13.7594 1.7797 14.3531 2.37344C14.9469 2.96719 15.2812 3.77187 15.2812 4.61094C15.2812 5.45 14.9469 6.25469 14.3531 6.84844L7.43 13.764C7.13313 14.0609 6.7308 14.2281 6.31156 14.2281C5.89233 14.2281 5.49 14.0609 5.19312 13.764C4.89625 13.4672 4.72906 13.0648 4.72906 12.6456C4.72906 12.2264 4.89625 11.824 5.19312 11.5272L11.5781 5.14219"
                                                                stroke="#f12f35" stroke-width="1.25"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg></span>
                                                    <span>
                                                        {{ trans('content.service_ticket_fields.upload_note') }}
                                                        {{ trans('content.service_ticket_fields.or') }}
                                                        <small class="d-block mt-1">{{ trans('content.knowledge_document.file_format') }}</small>
                                                        <small class="d-block mt-1">{{ trans('content.knowledge_document.maximum_file') }}</small>
                                                        <span class="amg-uploader__hint">{{ trans('content.knowledge_document.drop_file') }}</span>
                                                    </span>                                                    
                                                </div>
                                                
                                                <button type="button" id="manual_file_trigger"
                                                    name="manual_file_trigger"
                                                    class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">
                                                    {{ trans('content.service_ticket_fields.Add_Attachment') }}
                                                </button>
                                            </div>
                                            <div id="attachments" class="amg-uploader__preview"></div>
                                            <div class="amg-uploader__error"></div>
                                        </div>
                                        <input type="hidden" name="form_type" id="form_type" value="0">
                                    </div>
                                    {{-- <div class="amg-form-error-wrap"></div> --}}
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="amg-btn-group mt-3">
                                <button class="amg-btn amg-btn-secondary" id="btnClear" data-bs-dismiss="modal"
                                    type="button">{{ trans('config.kd_category_fields.cancel') }}</button>
                                <button type="button"
                                    class="amg-btn btn-submit-category amg-btn-primary js-act-create" id="btnSubmit">
                                    {{ trans('button.create') }}
                                </button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

<style>
    .input-group .form-select {
        min-height: 40px;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 40px !important;
        border-radius: 0 6px 6px 0;
        display: flex;
        align-items: center;
        /* padding: 4px; */
    }

    .select2-selection__rendered {
        display: flex !important;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px;
    }
    .note-editor {
    width: 100% !important;
}
</style>
