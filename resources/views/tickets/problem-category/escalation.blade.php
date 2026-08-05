{{-- @page-meta
{
  "page_no": "PC03E-26",
  "file": "escalation.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
<div class="amg-modal amg-form-modal modal fade" id="EsclationModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered w-50">
        <div class="modal-content rounded-5">
            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title px-4">
                    {{ trans("problem_category.service_ticket_fields.Esclations") }}
                    (<span id="pbm_cat_name"></span>)
                    <a href="#" id="history_link" target="_blank" class="ms-2" title="{{ trans("problem_category.service_ticket_fields.history") }}">
                        <button class="modal-close" data-bs-toggle="tooltip" title="{{ trans("problem_category.service_ticket_fields.history") }}">
                            <svg width="16" height="15" class="opacity-60" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.75 3.75002V7.14612L11.5719 8.83909C11.714 8.92445 11.8164 9.06279 11.8566 9.22366C11.8967 9.38453 11.8713 9.55476 11.7859 9.6969C11.7006 9.83904 11.5622 9.94144 11.4014 9.98159C11.2405 10.0217 11.0703 9.99633 10.9281 9.91096L7.80312 8.03596C7.71063 7.98039 7.63411 7.90183 7.58099 7.80791C7.52787 7.71399 7.49997 7.60792 7.5 7.50002V3.75002C7.5 3.58426 7.56585 3.42529 7.68306 3.30808C7.80027 3.19087 7.95924 3.12502 8.125 3.12502C8.29076 3.12502 8.44973 3.19087 8.56694 3.30808C8.68415 3.42529 8.75 3.58426 8.75 3.75002ZM8.125 2.31338e-05C7.13906 -0.00243276 6.16242 0.190675 5.25161 0.568169C4.34079 0.945664 3.51389 1.50005 2.81875 2.19924C2.25078 2.77424 1.74609 3.32737 1.25 3.90627V2.50002C1.25 2.33426 1.18415 2.17529 1.06694 2.05808C0.949731 1.94087 0.79076 1.87502 0.625 1.87502C0.45924 1.87502 0.300268 1.94087 0.183058 2.05808C0.065848 2.17529 0 2.33426 0 2.50002L0 5.62502C0 5.79078 0.065848 5.94975 0.183058 6.06697C0.300268 6.18418 0.45924 6.25002 0.625 6.25002H3.75C3.91576 6.25002 4.07473 6.18418 4.19194 6.06697C4.30915 5.94975 4.375 5.79078 4.375 5.62502C4.375 5.45926 4.30915 5.30029 4.19194 5.18308C4.07473 5.06587 3.91576 5.00002 3.75 5.00002H1.95312C2.51172 4.34221 3.06797 3.72268 3.70234 3.08049C4.57098 2.21186 5.67633 1.61847 6.88029 1.37446C8.08424 1.13045 9.33341 1.24665 10.4717 1.70853C11.61 2.17041 12.5869 2.95749 13.2805 3.97144C13.974 4.98538 14.3533 6.18121 14.3711 7.40952C14.3889 8.63782 14.0443 9.84413 13.3804 10.8777C12.7165 11.9113 11.7627 12.7263 10.6382 13.2209C9.51379 13.7155 8.2685 13.8678 7.05799 13.6587C5.84749 13.4496 4.72543 12.8885 3.83203 12.0453C3.77232 11.9889 3.70208 11.9448 3.62532 11.9155C3.54856 11.8862 3.46679 11.8724 3.38467 11.8747C3.30254 11.877 3.22168 11.8955 3.1467 11.929C3.07172 11.9626 3.00408 12.0106 2.94766 12.0703C2.89123 12.13 2.84712 12.2003 2.81783 12.277C2.78855 12.3538 2.77467 12.4356 2.777 12.5177C2.77932 12.5998 2.79779 12.6807 2.83136 12.7557C2.86493 12.8306 2.91295 12.8983 2.97266 12.9547C3.86285 13.7948 4.94512 14.4042 6.125 14.7298C7.30489 15.0554 8.54653 15.0873 9.74157 14.8226C10.9366 14.558 12.0487 14.005 12.9809 13.2117C13.913 12.4184 14.6368 11.4091 15.0892 10.2718C15.5415 9.13442 15.7086 7.90366 15.5759 6.68689C15.4432 5.47011 15.0147 4.30431 14.3279 3.29122C13.641 2.27813 12.7166 1.44854 11.6354 0.874854C10.5542 0.301167 9.34899 0.000819796 8.125 2.31338e-05Z" fill="currentColor"></path>
                            </svg>
                        </button>
                    </a>
                </h3>

                <button type="button" data-bs-dismiss="modal" class="modal-close px-4">
                   <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                        <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                    </svg>
                </button>
            </div>

            <div class="modal-body bg-white">
                <div class="px-4">
                    <table id="esclations" class="table amg-table">
                        <thead>
                            <tr>
                                <th><h4>{{ trans("problem_category.service_ticket_fields.User_Info") }}</h4></th>
                                <th><h4>{{ trans("problem_category.service_ticket_fields.Esclation_Time") }}</h4></th>
                                <th><h4>{{ trans("problem_category.service_ticket_fields.sla_count") }}</h4></th>
                                <th><h4>{{ trans("problem_category.service_ticket_fields.technician_mark_cc") }}</h4></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <form name="frm_esclation" id="frm_esclation" class="amg-form-theme px-4" method="post" action="" >
                    <div class="row g-4 mt-2">
                        <div class="col-md-12">
                            <div class="amg-form-field amg-form-field-row">
                                <label class="form-label required">{{ trans("problem_category.service_ticket_fields.Escalate_For") }}</label>
                                <div class="d-flex gap-3">
                                    <label> <input type="radio" class="escalate_for" name="escalate_for" value="users" id="users" checked> {{ trans("problem_category.service_ticket_fields.user") }} </label>
                                    <label><input type="radio" class="escalate_for" name="escalate_for" id="groups" value="groups"> {{ trans("problem_category.service_ticket_fields.group") }}</label>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <div class="col-md-12 escalate_to_group hide">
                            <div class="amg-form-field amg-form-field-row">
                                <label class="form-label required">{{ trans("problem_category.service_ticket_fields.Escalate_To_Group") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5Z" fill="black"/></svg>
                                    </span>
                                    <select name="esclate_group" id="esclate_group" class="form-select"></select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <div class="col-md-12 escalate_to_user">
                            <div class="amg-form-field amg-form-field-row">
                                <label class="form-label required">{{ trans("problem_category.service_ticket_fields.Esclate_To_User") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                      <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="black"/></svg>
                                    </span>
                                    <select name="esclate_to" id="esclate_to" class="form-select"></select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="amg-form-field amg-form-field-row">
                                <label class="form-label required">{{ trans("problem_category.service_ticket_fields.Esclate_Trigger_Time_Hrs") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="black"/></svg>
                                    </span>
                                    <input type="text" name="esclate_need_at" id="esclate_need_at" placeholder="{{ trans("problem_category.service_ticket_fields.Esclate_Trigger_Time_Hrs") }}" class="form-control">
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="amg-form-field amg-form-field-row">
                                <label class="form-label required">{{ trans("problem_category.service_ticket_fields.sla_count") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5Z" fill="black"/></svg>
                                    </span>
                                    <select name="sla_count" id="sla_count"class="form-select">
                                        <option value="">{{ trans("problem_category.service_ticket_fields.Select") }}</option>
                                        <option value="0">{{ trans("problem_category.service_ticket_fields.no") }}</option>
                                        <option value="1">{{ trans("problem_category.service_ticket_fields.yes") }}</option>
                                    </select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <!-- Technician CC -->
                        <div class="col-md-12">
                            <div class="amg-form-field amg-form-field-row">
                                <label class="form-label">{{ trans("problem_category.service_ticket_fields.technician_mark_cc") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="black"/></svg>
                                    </span>
                                    <select name="technician_mark_cc" id="technician_mark_cc"
                                        class="form-select">
                                        <option value="">{{ trans("problem_category.service_ticket_fields.Select") }}</option>
                                        <option value="0">{{ trans("problem_category.service_ticket_fields.no") }}</option>
                                        <option value="1">{{ trans("problem_category.service_ticket_fields.yes") }}</option>
                                    </select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="text-muted small">{{ trans("problem_category.service_ticket_fields.es_note") }}</div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer d-flex justify-content-end pb-4 py-0">
                <button type="button" id="btnEsclationClear" class="amg-btn amg-btn-secondary col-md-2"> {{ trans("problem_category.service_ticket_fields.Close") }} </button>
                <button type="submit" id="btnEsclationSubmit" form="frm_esclation" class="amg-btn amg-btn-primary col-md-2"> {{ trans("problem_category.service_ticket_fields.Save") }} </button>
            </div>
        </div>
    </div>
</div>
