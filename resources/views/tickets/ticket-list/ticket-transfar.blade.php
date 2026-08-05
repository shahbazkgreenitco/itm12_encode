{{-- @page-meta
{
  "page_no": "TKT01L-03",
  "file": "ticket-transfer.blade.php",
  "versions": [
    {
      "version": "1.0",
      "version": "1.1",
      "writer": "Muzaffar Shaikh",
      "from": "2026-05",
      "reviewer": null,
      "description": "updated form input and design changes"
    }
  ]
}
--}}
<div class="amg-modal amg-form-modal modal modal-lg fade" id="ticketTransferModal" tabindex="-1"
    aria-labelledby="ticketTransferModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="ticket-transfer-mdl-frm" class="form-horizontal w-100 amg-form-theme" autocomplete="off"
            onsubmit="return false;">
            @csrf
            <input type="hidden" name="_token" id="token" class="hidden" value=""/>
            <input type="hidden" name="id" id="id" class="hidden" value=""/>
            <input type="hidden" name="self_assign" id="self_assign" class="hidden self_assign" value="0"/>
            <input type="hidden" name="creator_id" id="creator_id" class="hidden creator_id" value=""/>
            <input type="hidden" name="request_submit_id" id="request_submit_id" value="">
            <div class="modal-content rounded-5">
                <!-- Modal Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title s1-text fw-bold s1-text px-2" id="customStatusModalTitle">{{ trans('ticket.transfer_ticket.transfer_ticket') }}</h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-2" aria-label="Close">
                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#515151" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body - Redesigned with Icons & Left Labels -->
                <div class="modal-body">
                    <div class="container-fluid px-4">
                        <!-- Department -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-end">
                                <label for="transfer_department_id" class="form-label mb-0 required">
                                    {{ trans('ticket.transfer_ticket.department') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-building form-field-icon"></i>
                                    </span>
                                    <select name="department_id" id="transfer_department_id" class="form-select filter-input form-with-icon">
                                        <option value="">{{ trans('ticket.transfer_ticket.select_department') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Problem Category -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-end">
                                <label for="transfer_problem_category_id" class="form-label mb-0 required">
                                    {{ trans('ticket.transfer_ticket.problem_request_category') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tags form-field-icon"></i>
                                    </span>
                                    <select name="problem_category_id" id="transfer_problem_category_id" class="form-select filter-input form-with-icon">
                                        <option value="">{{ trans('ticket.transfer_ticket.select_problem_request_category') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sub Category (wrapper with conditional visibility) -->
                        <div id="transfer_sub_category_id_cvr" class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-end">
                                <label for="transfer_sub_category_id" class="form-label mb-0 required">
                                    {{ trans('ticket.transfer_ticket.sub_category') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-folder2-open form-field-icon"></i>
                                    </span>
                                    <select name="sub_category_id" id="transfer_sub_category_id" class="form-select filter-input form-with-icon">
                                        <option value="">{{ trans('ticket.transfer_ticket.select_sub_category') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Priority + TAT (inline 2 columns) -->
                        <div class="row align-items-start mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-end">
                                <label class="form-label mb-0 required">
                                    {{ trans('ticket.transfer_ticket.priority') }} &amp; {{ trans('ticket.transfer_ticket.tat') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        {{-- <label for="transfer_priority_id" class="form-label mb-1 required">
                                            {{ trans('ticket.transfer_ticket.priority') }}
                                        </label> --}}
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-flag form-field-icon"></i>
                                            </span>
                                            <select name="priority_id" id="transfer_priority_id" class="form-select filter-input form-with-icon">
                                                <option value="">{{ trans('ticket.transfer_ticket.select_priority') }}</option>
                                                <option value="low">{{ trans('ticket.transfer_ticket.low') }}</option>
                                                <option value="medium">{{ trans('ticket.transfer_ticket.medium') }}</option>
                                                <option value="high">{{ trans('ticket.transfer_ticket.high') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        {{-- <label for="tat" class="form-label mb-1 required">
                                            {{ trans('ticket.transfer_ticket.tat') }}
                                        </label> --}}
                                        <div class="input-group ticket-input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-clock form-field-icon"></i>
                                            </span>
                                            <input type="number" name="tat" id="tat" autocomplete="off" class="form-control filter-input form-with-icon" placeholder="tat" />
                                            <span class="input-group-text">{{trans('ticket.transfer_ticket.hours')}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned To -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row" id="transfer_assigned_to_cvr">
                            <div class="col-md-3 text-end">
                                <label for="transfer_assigned_to" class="form-label mb-0">
                                    {{ trans('ticket.transfer_ticket.assign_to') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person form-field-icon"></i>
                                    </span>
                                    <select name="assigned_to" id="transfer_assigned_to" class="form-select filter-input form-with-icon">
                                        <option value="">{{ trans('ticket.transfer_ticket.select_user') }}</option>
                                    </select>
                                </div>
                                <small class="text-danger availabilityError"></small>
                                <small class="text-success availabilitySuccess" style="color: #186B43 !important" ></small>
                            </div>
                        </div>

                        <!-- Tags (multiple) -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-end">
                                <label for="transfer_tags" class="form-label mb-0">
                                    {{ trans('ticket.transfer_ticket.tag') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tag form-field-icon"></i>
                                    </span>
                                    <select name="tags[]" id="transfer_tags" multiple class="form-select filter-input form-with-icon">
                                        <option value="">{{ trans('ticket.transfer_ticket.add_tag') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Device -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-end">
                                <label for="transfer_device_id" class="form-label mb-0">
                                    {{ trans('ticket.transfer_ticket.device') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-laptop form-field-icon"></i>
                                    </span>
                                    <select name="device_id" id="transfer_device_id" class="form-select filter-input form-with-icon">
                                        <option value="">{{ trans('ticket.transfer_ticket.select_device') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks (textarea) - align top -->
                        <div class="row align-items-start mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-end">
                                <label for="remarks" class="form-label mb-0 required">
                                    {{ trans('ticket.transfer_ticket.reason_for_transfer_ticket') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-chat form-field-icon"></i>
                                    </span>
                                    <textarea name="remarks" id="remarks" placeholder="{{ trans('content.service_ticket_fields.enter_comments') }}"
                                            class="form-control filter-input form-with-icon" rows="3"></textarea>
                                </div>
                                <div id="shows_error"></div>
                            </div>
                        </div>

                        {{-- <!-- Hidden checkbox (remove_tasks) -->
                        <div class="checkbox-row d-none">
                            <input type="checkbox" id="remove_tasks" name="remove_tasks" value="1" checked="checked">
                            <label for="remove_tasks">{{ trans('ticket.transfer_ticket.remove_prev_category_task') }}</label>
                        </div> --}}

                        <!-- Visible checkbox (is_note) -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row checkbox-row d-none">
                            <div class="col-md-3 text-end">
                            <label for="remove_tasks" class="form-label mb-0">{{ trans('ticket.transfer_ticket.remove_prev_category_task') }}</label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-check-input" type="checkbox" id="remove_tasks" name="remove_tasks" value="1" checked="checked">
                            </div>
                        </div>


                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-5 text-end">
                                <label for="is_note" class="form-label mb-0">
                                    {{ trans('ticket.transfer_ticket.make_it_note_for_internal_purpose') }}
                                </label>
                            </div>
                            <div class="col-md-7 pt-2">
                                <input class="form-check-input" type="checkbox" name="is_note" value="1" id="is_note" checked="checked">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer (unchanged, kept original button styles) -->
                <div class="amg-form-footer gap-2 modal-footer d-flex justify-content-end py-4">
                    <button type="button" id="btnClear" data-bs-dismiss="modal"
                        class="amg-btn amg-btn-secondary col-md-2 amg-btn-md">
                        {{ trans('ticket.transfer_ticket.cancel') }}
                    </button>
                    <button type="button" id="btnSubmit"
                        class="amg-btn amg-btn-primary amg-btn-block col-md-2 amg-btn-md">
                        {{ trans('ticket.transfer_ticket.update') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>