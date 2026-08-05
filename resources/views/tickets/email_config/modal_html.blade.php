<div class="amg-modal amg-form-modal modal fade" id="emailtoTicketConfigMdl" tabindex="-1" aria-labelledby="emailtoTicketConfigMdlModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="emailToTicketConfigMdlForm" name="emailtoTicketConfigMdlForm" method="post" action="#" autocomplete="off" class="form-horizontal w-100 amg-form-theme" onsubmit="return false;">
            @csrf
            <input type="hidden" name="type_id" id="type_id" value="">
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="emailtoTicketConfigMdlModalLabel"></h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body bg-white">
                    <div id="ticket_type_mdl_loader" class="amg-form-loader">
                        <div class="amg-form-loader-spinner"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
                    </div>

                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label for="company_id"
                                        class="form-label b1-text required">{{ trans("content.service_ticket_fields.company") }}</label>
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
                                        <select name="company_id" id="email_company_id" class="form-select"></select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="auto_create_from_email" class="form-label b1-text">{{ trans('content.service_ticket_fields.Email_Service_Status') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                        <select name="auto_create_from_email" id="auto_create_from_email"  class="form-control select2">
                                            <option value="1">{{ trans("content.service_ticket_fields.Enabled") }}</option>
                                            <option value="0">{{ trans("content.service_ticket_fields.Disabled") }}</option>
                                        </select>                                    
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ebts_host" class="form-label b1-text required"> {{trans('content.service_ticket_fields.email_host')}}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="ebts_host" id="ebts_host" autocomplete="off" class="form-control" placeholder="{{ trans('content.service_ticket_fields.Enter_Email_Host') }}" value=""/>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ebts_port" class="form-label b1-text required"> {{trans('content.service_ticket_fields.Email_Port')}}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="ebts_port" id="ebts_port" autocomplete="off" class="form-control" placeholder="{{ trans("content.service_ticket_fields.Enter_Email_Port") }}" value=""/>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ebts_username" class="form-label b1-text required">{{ trans('content.service_ticket_fields.Email_Account') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="ebts_username" id="ebts_username" autocomplete="off" class="form-control" placeholder="{{ trans("content.service_ticket_fields.Enter_Email_Account") }}" value=""/>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ebts_password" class="form-label b1-text required">{{ trans('content.service_ticket_fields.Email_Password') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="password" name="ebts_password" id="ebts_password" autocomplete="off" class="form-control" placeholder="{{ trans("content.service_ticket_fields.Enter_Email_Password") }}"  value=""/>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ebts_encryption" class="form-label b1-text required">{{ trans('content.service_ticket_fields.Encryption') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <select name="ebts_encryption" id="ebts_encryption"  class="form-control select2">
                                            <option value="ssl">SSL</option>
                                            <option value="tls">TLS</option>
                                            <option value="false">{{ trans("content.service_ticket_fields.False") }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="validate_cert" class="form-label b1-text">{{ trans('content.service_ticket_fields.Validate_Certificate') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                        <select name="validate_cert" id="validate_cert"  class="form-control select2">
                                            <option value="2">{{ trans("content.service_ticket_fields.False") }}</option>
                                            <option value="1">{{ trans("content.service_ticket_fields.True") }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="default_department_id" class="form-label b1-text required">{{ trans('content.service_ticket_fields.Default_Department') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="default_department_id" id="default_department_id" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="default_prob_cat_id_email" class="form-label b1-text required">{{ trans('content.service_ticket_fields.Problem_Category') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="default_prob_cat_id" id="default_prob_cat_id_email" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12" id="sub_category_id_cvr">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="default_sub_cat_id_email" class="form-label b1-text required">{{ trans('content.service_ticket_fields.Sub_Problem_Category') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="default_sub_cat_id" id="default_sub_cat_id_email" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="isReqDeptChgBfrResolve" class="form-label b1-text required">{{ trans('content.service_ticket_fields.Change_Department') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="isReqDeptChgBfrResolve" id="isReqDeptChgBfrResolve" class="form-control select2">
                                            <option value="1">{{ trans("content.service_ticket_fields.yes") }}</option>
                                            <option value="0">{{ trans("content.service_ticket_fields.no") }}</option>
                                        </select>                                    
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="isReqDeptChgBfrAssigned" class="form-label b1-text required">{{ trans('content.service_ticket_fields.assigned_to') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="isReqDeptChgBfrAssigned" id="isReqDeptChgBfrAssigned" class="form-control select2">
                                            <option value="1">{{ trans("content.service_ticket_fields.yes") }}</option>
                                            <option value="0">{{ trans("content.service_ticket_fields.no") }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="isReqDeptChgBfrTransfer" class="form-label b1-text required">{{ trans('content.transfer.transfer') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="isReqDeptChgBfrTransfer" id="isReqDeptChgBfrTransfer" class="form-control select2">
                                            <option value="1">{{ trans("content.service_ticket_fields.yes") }}</option>
                                            <option value="0">{{ trans("content.service_ticket_fields.no") }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="except_notify_status" class="form-label b1-text required">{{ trans("content.service_ticket_fields.Exception_Notification") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                            <select name="except_notify_status" id="except_notify_status" class="form-control select2">
                                            <option value="1">{{ trans("content.service_ticket_fields.Enabled") }}</option>
                                            <option value="0">{{ trans("content.service_ticket_fields.Disabled") }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12 notifyCover">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="except_notify_tos" class="form-label b1-text required">{{ trans("content.service_ticket_fields.Notify_TOS") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="except_notify_tos" id="except_notify_tos" autocomplete="off" class="form-control" placeholder="{{ trans("content.service_ticket_fields.Notify_TOS") }}"  value=""/>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12 ccnotifyCover">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="except_notify_ccs" class="form-label b1-text required">{{ trans("content.service_ticket_fields.Notify_To_CC") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="except_notify_ccs" id="except_notify_ccs" autocomplete="off" class="form-control" placeholder="{{ trans("content.service_ticket_fields.Notify_To_CC") }}"  value=""/>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ticketing_restricted" class="form-label b1-text required">{{ trans("content.service_ticket_fields.Restriction_For_Domains") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="ticketing_restricted" id="ticketing_restricted" class="form-control select2">
                                            <option value="1">{{ trans("content.service_ticket_fields.Allow_specific_domains") }}</option>
                                            <option value="2">{{ trans("content.service_ticket_fields.Allow_All_Domains") }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12 emailcover domainallowcover">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ticketing_allowed_domains" class="form-label b1-text">{{ trans("content.service_ticket_fields.Allow_specific_domains") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <textarea name="ticketing_allowed_domains" id="ticketing_allowed_domains" autocomplete="off" rows="5" class="textarea form-control" placeholder="{{ trans("content.service_ticket_fields.specific_domains_placeholder") }}"></textarea>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12 emailcover domainallcover">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ticketing_blocked_accounts" class="form-label b1-text">{{ trans("content.service_ticket_fields.Blocked_Email_Accounts") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <textarea name="ticketing_blocked_accounts" id="ticketing_blocked_accounts" autocomplete="off" rows="5" class="textarea form-control" placeholder="{{ trans("content.service_ticket_fields.Blocked_Email_Accounts_placeholder") }}"></textarea>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="ticketing_blocked_domains" class="form-label b1-text">{{ trans("content.service_ticket_fields.Restricted_domains") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <textarea name="ticketing_blocked_domains" id="ticketing_blocked_domains" autocomplete="off" rows="5" class="textarea form-control" placeholder="{{ trans("content.service_ticket_fields.restricted_domains_placeholder") }}"></textarea>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="restricted_words" class="form-label b1-text">{{ trans("content.service_ticket_fields.Restricted_Words") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <textarea name="restricted_words" id="restricted_words" autocomplete="off" rows="5" class="textarea form-control" placeholder="{{ trans("content.service_ticket_fields.restricted_words_placeholder") }}"></textarea>
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="outgoing_mail_id" class="form-label b1-text">{{ trans("content.service_ticket_fields.outgoing_mail_server_id") }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                        <select name="outgoing_mail_id" id="outgoing_mail_id" class="form-control select2">
                                            <option value="">Select Outgoing Mail</option>
                                            @foreach($allOutGoingServers as $allOutGoingServer)
                                                <option value="{{$allOutGoingServer->id}}">{{$allOutGoingServer->mail_username}}</option>
                                            @endforeach 
                                        </select>                                    
                                    </div>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="amg-form-footer modal-footer d-flex justify-content-end pb-4 py-0">
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-block col-md-2 amg-btn-md">{{ trans('ticket-types.form.save') }}</button>
                    <button type="button" id="btnClear" data-bs-dismiss="modal" class="amg-btn amg-btn-outline amg-btn-block col-md-2 amg-btn-md">{{ trans('ticket-types.form.cancel') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
