{{-- @page-meta
{
  "page_no": "INCMD-05",
  "file": "add-incident.blade",
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
<div class="amg-modal amg-form-modal modal fade" id="ticket-incident-modal" tabindex="-1"
    aria-labelledby="ticketTypeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="incident-mdl-frm" name="incidentMdlForm" method="post" action="#" en autocomplete="off"
            ctype="multipart/form-data" class="form-horizontal w-100 amg-form-theme" onsubmit="return false;"
            autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" id="id" name="id" class="hidden" value="" />
            {{-- <input type="hidden" id="tmp_id" name="tmp_id" class="hidden" value="" /> --}}
            <input type="hidden" id="tmp_id" name="tmp_id" class="hidden" value="" />
            <input type="hidden" name="for_action" id="for_action" value="" />
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="myLargeModalLabel">
                        {{ trans('content.ticket_incident.ticket_incident') }}</h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4"
                        aria-label="{{ trans('user.user_form.close') }}">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body no-scroll">
                    <div id="user_mdl_loader" class="amg-form-loader">
                        <div class="amg-form-loader-spinner"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
                    </div>
                    <ul class="nav nav-underline" id="incident-tabs" role="tablist">
                        <li class="nav-item border-0">
                            <a class="nav-link active" id="basic-tab" data-bs-toggle="tab" href="#basic-details"
                                role="tab" aria-controls="overview" aria-expanded="true"><span
                                    class="s1-text fw-normal">{{ trans('content.ticket_incident.basic_incident_tab') }}</span></a>
                        </li>
                        <li class="nav-item" id="userPermission">
                            <a class="nav-link" id="incident-management-tab" data-bs-toggle="tab"
                                href="#incident-management" role="tab" aria-controls="link1"><span
                                    class="s1-text fw-normal">{{ trans('content.ticket_incident.incident_management') }}</span></a>
                        </li>
                    </ul>
                    <div class="tab-content tabcontent-border pt-3" id="AddUserTabContent">
                        <div role="tabpanel" class="tab-pane fade show active" id="basic-details"
                            aria-labelledby="basic-details-tab">
                            <div class="container-fluid py-3">
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end required"
                                                for="company_id">{{ trans('content.ticket_incident.company') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12.75 18.75H1.35C1.01863 18.75 0.75 18.4814 0.75 18.15V3.35C0.75 3.01863 1.01863 2.75 1.35 2.75H6.75V1.35C6.75 1.01863 7.01863 0.75 7.35 0.75H12.15C12.4814 0.75 12.75 1.01863 12.75 1.35V6.75M12.75 18.75H18.15C18.4814 18.75 18.75 18.4814 18.75 18.15V7.35C18.75 7.01863 18.4814 6.75 18.15 6.75H12.75M12.75 18.75V14.75M12.75 6.75V10.75M12.75 10.75H14.75M12.75 10.75V14.75M12.75 14.75H14.75"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <select name="company_id" id="company_id" class="form-select">
                                                    <option value="">Select company</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end required"
                                                for="department_id">{{ trans('content.ticket_incident.department_impacted') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M21 7.35304L21 16.647C21 16.8649 20.8819 17.0656 20.6914 17.1715L12.2914 21.8381C12.1102 21.9388 11.8898 21.9388 11.7086 21.8381L3.30861 17.1715C3.11814 17.0656 3 16.8649 3 16.647L2.99998 7.35304C2.99998 7.13514 3.11812 6.93437 3.3086 6.82855L11.7086 2.16188C11.8898 2.06121 12.1102 2.06121 12.2914 2.16188L20.6914 6.82855C20.8818 6.93437 21 7.13514 21 7.35304Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 21L12 12" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M12.5 11V21C12.5 21.2761 12.2761 21.5 12 21.5C11.7239 21.5 11.5 21.2761 11.5 21V11C11.5 10.7239 11.7239 10.5 12 10.5C12.2761 10.5 12.5 10.7239 12.5 11Z"
                                                            fill="currentColor" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <select name="department_id" id="department_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end required"
                                                for="creator_id">{{ trans('content.ticket_incident.incident_reprted_by') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 18V17C7 14.2386 9.23858 12 12 12C14.7614 12 17 14.2386 17 17V18"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path
                                                            d="M12 12C13.6569 12 15 10.6569 15 9C15 7.34315 13.6569 6 12 6C10.3431 6 9 7.34315 9 9C9 10.6569 10.3431 12 12 12Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="1.5" />
                                                    </svg>
                                                </span>
                                                <select name="creator_id" id="creator_id" class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="incident_start_date"
                                                class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.incident_start_date_time') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">

                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13 21H5C3.89543 21 3 20.1046 3 19V10H21V13M15 4V2M15 4V6M15 4H10.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M3 10V6C3 4.89543 3.89543 4 5 4H7" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M7 2V6" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M21 10V6C21 4.89543 20.1046 4 19 4H18.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M14.9922 18H17.9922M21 18H17.9922M17.9922 18V15M17.9922 18V21"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>

                                                </span>
                                                <input name="incident_start_date" id="incident_start_date"
                                                    autocomplete="off" type="text" class="form-control"
                                                    placeholder="Incident Start Date & Time" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="incident_end_date"
                                                class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.incident_end_date_time') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13 21H5C3.89543 21 3 20.1046 3 19V10H21V13M15 4V2M15 4V6M15 4H10.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M3 10V6C3 4.89543 3.89543 4 5 4H7" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M7 2V6" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M21 10V6C21 4.89543 20.1046 4 19 4H18.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M14.9922 18H21" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <input name="incident_end_date" id="incident_end_date"
                                                    autocomplete="off" type="text" class="form-control"
                                                    placeholder="Incident End Date & Time" />
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

                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>

                                                </span>
                                                <select name="problem_category_id" id="problem_category_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4" id="sub_category_id_cvr">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end"
                                                for="sub_category_id">{{ trans('content.ticket_incident.sub_category') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <select name="sub_category_id" id="sub_category_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end required"
                                                for="priority_id">{{ trans('content.ticket_incident.priority') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 19V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19Z" stroke="currentColor" stroke-width="1.5"/>
                                                        <path d="M8 14L12 10L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                                <select name="priority_id" id="priority_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.sla_breahced') }}</label>
                                            <div class="input-group ">
                                                <label
                                                    class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.Yes') }}
                                                    <input autocomplete="off" type="radio" name="sla_breaches"
                                                        value="1"
                                                        class="form-check-input ps-10 ms-10 user_permission">
                                                </label>

                                                <label
                                                    class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.no') }}
                                                    <input autocomplete="off" type="radio" name="sla_breaches"
                                                        value="0"
                                                        class="form-check-input ps-10 ms-10 user_permission">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="service_impacted"
                                                class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.ticket_incident.service_impacted') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                        fill="none">
                                                        <path
                                                            d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                </span>
                                                <select name="service_impacted[]" id="service_impacted"
                                                    class="form-control" multiple></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="subject"
                                                class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.ticket_incident.subject') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M20.0429 21H3.95705C2.41902 21 1.45658 19.3364 2.22324 18.0031L10.2662 4.01533C11.0352 2.67792 12.9648 2.67791 13.7338 4.01532L21.7768 18.0031C22.5434 19.3364 21.581 21 20.0429 21Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M12 9V13" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" />
                                                        <path d="M12 17.01L12.01 16.9989" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <input name="subject" id="subject" autocomplete="off"
                                                    type="text" class="form-control"
                                                    placeholder="{{ trans('content.ticket_incident.Enter_Subject') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end required"
                                                for="location">{{ trans('content.ticket_incident.location') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M20 10C20 14.4183 12 22 12 22C12 22 4 14.4183 4 10C4 5.58172 7.58172 2 12 2C16.4183 2 20 5.58172 20 10Z"
                                                            stroke="currentColor" stroke-width="1.5" />
                                                        <path
                                                            d="M12 11C12.5523 11 13 10.5523 13 10C13 9.44772 12.5523 9 12 9C11.4477 9 11 9.44772 11 10C11 10.5523 11.4477 11 12 11Z"
                                                            fill="currentColor" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <select name="location" id="location_id"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end"
                                                for="internal_place">{{ trans('content.ticket_incident.internal_place') }}

                                            </label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">

                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 19V21" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M5 12H3" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 5V3" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M19 12H21" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>

                                                </span>
                                                <select name="internal_place" id="internal_place"
                                                    class="form-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end"
                                                for="ticket_id">{{ trans('content.ticket_incident.ticket_number') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M21.0404 12.2828L21.639 16.4731C21.8226 17.7584 20.752 18.8752 19.4601 18.746L12.199 18.0199C12.0667 18.0067 11.9333 18.0067 11.801 18.0199L4.53989 18.746C3.24801 18.8752 2.17738 17.7584 2.36099 16.4731L2.95959 12.2828C2.98639 12.0952 2.98639 11.9048 2.95959 11.7172L2.36099 7.52691C2.17738 6.24163 3.24801 5.1248 4.5399 5.25399L11.801 5.9801C11.9333 5.99333 12.0667 5.99333 12.199 5.9801L19.4601 5.25399C20.752 5.1248 21.8226 6.24163 21.639 7.5269L21.0404 11.7172C21.0136 11.9048 21.0136 12.0952 21.0404 12.2828Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M21 6L17 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M7 15L3 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                                <select name="ticket_id[]" id="ticket_id" class="form-select"
                                                    multiple>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label class="form-label b1-text me-2 mb-0 text-end"
                                                for="status">{{ trans('content.ticket_incident.incident_status') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z"
                                                            fill="#131927" stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <select name="status" id="status" class="form-select"
                                                    tabindex="-1" title="">
                                                    <option value="" disabled selected>
                                                        {{ trans('content.ticket_incident.incident_status') }}</option>
                                                    <option value="1">
                                                        {{ trans('content.ticket_incident.opened') }}</option>
                                                    <option value="2">
                                                        {{ trans('content.ticket_incident.closed') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="financial_loss_amount"
                                                class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.ticket_incident.financial_loss_amount') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M15 8.5C14.315 7.81501 13.1087 7.33855 12 7.30872M9 15C9.64448 15.8593 10.8428 16.3494 12 16.391M12 7.30872C10.6809 7.27322 9.5 7.86998 9.5 9.50001C9.5 12.5 15 11 15 14C15 15.711 13.5362 16.4462 12 16.391M12 7.30872V5.5M12 16.391V18.5"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <input name="financial_loss_amount" id="financial_loss_amount"
                                                    autocomplete="off" type="number" class="form-control"
                                                    placeholder="{{ trans('content.ticket_incident.Financial_loss_amount') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="man_hour_loss"
                                                class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.ticket_incident.man_hour_loss') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16 16.4722V20C16 21.1045 15.1046 22 14 22H10C8.89543 22 8 21.1045 8 20V16.4722" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M8 7.52779V4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V7.52779" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18C15.3137 18 18 15.3137 18 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M14 12H12V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                                <input name="man_hour_loss" id="man_hour_loss" autocomplete="off"
                                                    type="text" class="form-control"
                                                    placeholder="{{ trans('content.ticket_incident.man_hour_loss') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4 px-4">
                                    <!-- Content -->
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label
                                                class="form-label b1-text me-2 mb-0 text-end required">{{ trans('content.service_ticket_fields.Content') }}</label>
                                            <div class="input-group">
                                                <div class="input-group">
                                                    <textarea name="content" id="content" class="amg-summernote form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row"><div class="col-2"></div><div id="shows_error" class="error col-9"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade show" id="incident-management"
                            aria-labelledby="incident-management-tab">
                            <div class="container-fluid py-3">
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label
                                                class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.rca') }}</label>
                                            <div class="input-group">
                                                <textarea name="rca" id="rca" class="amg-summernote form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label
                                                class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.why_incident_happened') }}</label>
                                            <div class="input-group">
                                                <textarea name="why_incident_happened" id="why_incident_happened" class="amg-summernote form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label
                                                class="form-label b1-text me-2 mb-0 text-end">{{ trans('content.ticket_incident.preventive_measure_taken') }}</label>
                                            <div class="input-group">
                                                <textarea name="preventive_measure_taken" id="preventive_measure_taken" class="amg-summernote form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="amg-form-footer modal-footer d-flex justify-content-end py-4">
                    <button autocomplete="off" type="button" id="btnSubmit"
                        class="amg-btn amg-btn-primary amg-btn-md" style="width:150px" >
                        {{ trans("button.save") }}
                    </button>

                    <button autocomplete="off" type="button" id="btnClear"
                        class="amg-btn amg-btn-ghost s2-text bg-black text-white amg-btn-md ms-2" 
                        data-bs-dismiss="modal" style="width:150px">
                        {{ trans("button.close") }}
                    </button> 
                </div>
            </div>
        </form>
    </div>
</div>
