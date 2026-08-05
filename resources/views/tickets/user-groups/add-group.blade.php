{{-- @page-meta
{
  "page_no": "CCEMG-03",
  "file": "add-group.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
<div class="amg-modal amg-form-modal modal fade" id="cc-email-modal" tabindex="-1" aria-labelledby="ccEmailModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="cc-email-mdl-frm" name="ccEmailMdlForm" method="post" action="#" en autocomplete="off"
            ctype="multipart/form-data" class="form-horizontal w-100 amg-form-theme" onsubmit="return false;"
            autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" id="id" name="id" class="hidden" value="" />
            <input type="hidden" id="tmp_id" name="tmp_id" class="hidden" value="" />
            <input type="hidden" name="for_action" id="for_action" value="" />
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="myLargeModalLabel"></h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4"
                        aria-label="{{ trans('user.user_form.close') }}">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row mb-4 px-4">
                            <div class="col-12">
                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                    <label class="form-label b1-text me-2 mb-0 text-end required"
                                        for="company_id">{{ trans('content.user_group.company') }}</label>
                                    <div class="input-group ">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M12.75 18.75H1.35C1.01863 18.75 0.75 18.4814 0.75 18.15V3.35C0.75 3.01863 1.01863 2.75 1.35 2.75H6.75V1.35C6.75 1.01863 7.01863 0.75 7.35 0.75H12.15C12.4814 0.75 12.75 1.01863 12.75 1.35V6.75M12.75 18.75H18.15C18.4814 18.75 18.75 18.4814 18.75 18.15V7.35C18.75 7.01863 18.4814 6.75 18.15 6.75H12.75M12.75 18.75V14.75M12.75 6.75V10.75M12.75 10.75H14.75M12.75 10.75V14.75M12.75 14.75H14.75"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <select name="company_id" id="company_id" class="form-select">
                                            <option value="">{{ trans('content.user_group.select_company') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 px-4">
                            <div class="col-12">
                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                    <label for="name"
                                        class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.user_group.name') }}</label>
                                    <div class="input-group ">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M20.0429 21H3.95705C2.41902 21 1.45658 19.3364 2.22324 18.0031L10.2662 4.01533C11.0352 2.67792 12.9648 2.67791 13.7338 4.01532L21.7768 18.0031C22.5434 19.3364 21.581 21 20.0429 21Z"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round" />
                                                <path d="M12 9V13" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                                <path d="M12 17.01L12.01 16.9989" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <input name="name" id="name" autocomplete="off" type="text"
                                            class="form-control" placeholder="{{ trans('content.user_group.name') }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 px-4">
                            <div class="col-12">
                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                    <label class="form-label b1-text me-2 mb-0 text-end required"
                                        for="department_id">{{ CommonHelper::isClientCws() ? trans("content.service_ticket_fields.department_or_customer") : trans("content.service_ticket_fields.Department") }}</label>
                                    <div class="input-group ">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M21 7.35304L21 16.647C21 16.8649 20.8819 17.0656 20.6914 17.1715L12.2914 21.8381C12.1102 21.9388 11.8898 21.9388 11.7086 21.8381L3.30861 17.1715C3.11814 17.0656 3 16.8649 3 16.647L2.99998 7.35304C2.99998 7.13514 3.11812 6.93437 3.3086 6.82855L11.7086 2.16188C11.8898 2.06121 12.1102 2.06121 12.2914 2.16188L20.6914 6.82855C20.8818 6.93437 21 7.13514 21 7.35304Z"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M12 21L12 12" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M12.5 11V21C12.5 21.2761 12.2761 21.5 12 21.5C11.7239 21.5 11.5 21.2761 11.5 21V11C11.5 10.7239 11.7239 10.5 12 10.5C12.2761 10.5 12.5 10.7239 12.5 11Z"
                                                    fill="currentColor" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <select name="department_id" id="department_id" class="form-select"></select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 px-4">
                            <div class="col-12">
                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                    <label class="form-label b1-text me-2 mb-0 text-end required"
                                        for="problem_category_id">{{ trans('content.ticket_incident.problem_category') }}</label>
                                    <div class="input-group ">
                                        <span class="input-group-text" id="basic-addon1">

                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.0098 15.9998L11.9998 16.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M12.0098 11.9998L11.9998 12.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M12.0098 7.99977L11.9998 8.01088" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M8.00977 11.9998L7.99977 12.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M16.0098 11.9998L15.9998 12.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M21 3.6V20.4C21 20.7314 20.7314 21 20.4 21H3.6C3.26863 21 3 20.7314 3 20.4V3.6C3 3.26863 3.26863 3 3.6 3H20.4C20.7314 3 21 3.26863 21 3.6Z"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>

                                        </span>
                                        <select name="problem_category_id" id="problem_category_id"
                                            class="form-select"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row  px-4" id="sub_category_id_cvr">
                            <div class="col-12">
                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                    <label class="form-label b1-text me-2 mb-0 text-end required"
                                        for="sub_category_id">{{ trans('content.ticket_incident.sub_category') }}</label>
                                    <div class="input-group ">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.0098 15.9998L11.9998 16.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M12.0098 11.9998L11.9998 12.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M12.0098 7.99977L11.9998 8.01088" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M8.00977 11.9998L7.99977 12.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M16.0098 11.9998L15.9998 12.0109" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M21 3.6V20.4C21 20.7314 20.7314 21 20.4 21H3.6C3.26863 21 3 20.7314 3 20.4V3.6C3 3.26863 3.26863 3 3.6 3H20.4C20.7314 3 21 3.26863 21 3.6Z"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <select name="sub_category_id" id="sub_category_id"
                                            class="form-select"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="amg-form-footer modal-footer d-flex justify-content-end py-4">
                    <button autocomplete="off" type="button" id="btnSubmit"
                        class="amg-btn amg-btn-primary amg-btn-md" style="width:300px">
                        {{ trans('button.add') }}
                    </button>

                    <button autocomplete="off" type="button" id="btnClear"
                        class="amg-btn amg-btn-secondary amg-btn-md ms-2" data-bs-dismiss="modal"
                        style="width:150px">
                        {{ trans('button.close') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
