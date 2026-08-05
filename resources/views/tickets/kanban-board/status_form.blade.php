<div id="statusKanbanMdl" class="amg-modal amg-form-modal modal fade kanban-mdl-box" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title fw-bold px-4" id="update_details">{{ trans('ticket.update_status.update_status') }}</h3>
                <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                    <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none" width="31" height="31">
                        <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                    </svg>
                </button>
            </div>
            <div class="modal-body sidebar_tickt_details">
                <form id="frm_update_status_card" name="frm_update_status_card" action="#" class="form-horizontal">
                    <input type="hidden" id="id" name="id" class="hidden" />
                    <input type="hidden" id="ticket_status_form_id" name="ticket_status_form_id" class="hidden" />
                    <input type="hidden" id="tmp_id" name="tmp_id" value="" />
                    <input type="hidden" id="ticket_id_card" name="ticket_id" value="" />
                    <input type="hidden" id="ticket_status_card" name="status_id" value="" />
                    <input type="hidden" id="priority_id_card" name="priority_id" value="" />
                    <input type="hidden" id="tat" name="tat" value="" />
                    
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="comment" class="control-label mandatory">{{ trans("content.service_ticket_fields.Comment") }}</label>
                            <div class="col-sm-12">
                                <textarea name="comment" id="comment" class="form-control amg-summernote" placeholder="{{ trans("content.service_ticket_fields.placeholder_comment") }}"></textarea>
                                <div id="shows_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="update-dropper-cover">
                                <div id="attachment-dropper-cover-update"
                                    class="amg-uploader"
                                    data-amg-uploader
                                    data-multiple="true"
                                    data-auto-upload="true"
                                    data-max-files="10"
                                    data-max-size="10"
                                    data-record-id
                                    data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv"
                                    data-upload-url="{{ url('ticket/attachment/add') }}"
                                    data-remove-url="{{ url('ticket/attachment/remove') }}"
                                    data-token="{{ csrf_token() }}"
                                    data-record-input="#frm_update_status_card #tmp_id"
                                    data-upload-field="attachment"
                                    data-extra-inputs="#frm_update_status_card #ticket_id_card">
                                    <input type="file" id="attachment_input" class="amg-uploader__input" multiple hidden>
                                    <!-- Dropzone -->
                                    <div id="attachment-dropper" class="amg-uploader__dropzone">
                                        <div class="amg-uploader__message">
                                            <span class="amg-uploader__icon-wrap">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M16.5625 9.05781L9.64687 15.9734C8.76042 16.8599 7.55875 17.3581 6.30469 17.3581C5.05062 17.3581 3.84895 16.8599 2.9625 15.9734C2.07605 15.087 1.57788 13.8853 1.57788 12.6312C1.57788 11.3772 2.07605 10.1755 2.9625 9.28906L9.87812 2.37344C10.4718 1.7797 11.2765 1.44531 12.1156 1.44531C12.9547 1.44531 13.7594 1.7797 14.3531 2.37344C14.9469 2.96719 15.2812 3.77187 15.2812 4.61094C15.2812 5.45 14.9469 6.25469 14.3531 6.84844L7.43 13.764"
                                                        stroke="black" stroke-width="1.25"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            <span> {{ trans("content.service_ticket_fields.upload_note") }}
                                                {{ trans("content.service_ticket_fields.or") }}
                                                <span class="amg-uploader__hint">{{ __('ticket.create_ticket.drop_files_here') }}</span>
                                            </span>
                                        </div>
                                        <button type="button" id="manual_file_trigger" name="manual_file_trigger" class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">{{ __('ticket.create_ticket.add_attachment') }}</button>
                                    </div>
                                    <div class="mt-1 fs-1">
                                        {{ trans('service_ticket.service_detail.supported_file_types') }}
                                    </div>
                                    <div class="mt-1 fs-1">
                                        {{ trans('service_ticket.service_detail.max_files_size_message') }}
                                    </div>
                                    <!-- Preview -->
                                    <div id="attachment_updates" class="amg-uploader__preview"></div>
                                    <!-- Error -->
                                    <div class="amg-uploader__error"></div>
                                </div>
                                <input type="hidden" name="form_type" id="form_type" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="" class="comment_cc">
                                <input type="checkbox" id="add_back_trail" name="add_back_trail" value="1" checked="checked" /> {{ trans("content.service_ticket_fields.back_trail") }}
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="follow_cc" class="comment_cc">
                                    <input type="checkbox" id="follow_cc" name="follow_cc" value="1" /> {{ trans("content.service_ticket_fields.CC_EMails") }}
                                </label>
                                <br>
                                <div class="form-controls">
                                    <select name="cc_emails" id="cc_emails" class="form-control" multiple></select>
                                    <div id="shows_error_cc"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="set_edit_status_form"></div>
                </form>
            </div>
           <div class="amg-form-footer modal-footer justify-content-end mb-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button type="button" id="btnCardClear" data-bs-dismiss="modal" class="amg-btn amg-btn-secondary">{{ trans('ticket.update_status.cancel') }}</button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary" style="min-width: 250px">{{ trans('ticket.update_status.update') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>