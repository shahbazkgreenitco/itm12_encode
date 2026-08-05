{{-- @page-meta
{
  "page_no": "KANBAN-02-2026",
  "file": "add-kanban-modal.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Converted to New Design",
      "from": "2026-06",
      "reviewer": null,
      "description": "Kanban Add Modal - New Design Pattern with Company dropdown"
    }
  ]
}
--}}
<div class="amg-modal amg-form-modal modal fade" id="addKanbanFormMdl" tabindex="-1"
     aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="addKanbanForm" name="addKanbanForm" method="POST" action="#"
              class="form-horizontal" enctype="multipart/form-data">
            @csrf
            <div class="modal-content rounded-5">
                <!-- Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="title">
                        {{ trans("ticket.service_ticket_fields.add_kanaban") }}
                    </h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#515151" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">
                        <!-- ========== NEW: Company Dropdown ========== -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label class="form-label mb-0 mandatory">Company</label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-building form-field-icon"></i>
                                    </span>
                                    <select id="company_id" name="company_id" class="form-select filter-input form-with-icon">
                                        <!-- Options will be populated via AJAX -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Board Name -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label for="name" class="form-label mb-0 mandatory">
                                    {{ trans("ticket.service_ticket_fields.board_name") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-kanban form-field-icon"></i>
                                    </span>
                                    <input type="text" name="name" id="name" class="form-control filter-input form-with-icon"
                                           placeholder="{{ trans('ticket.service_ticket_fields.enter_board_name') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Department (Multiple Select) -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label for="department_id" class="form-label mb-0">
                                    {{ trans("ticket.service_ticket_fields.department") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-diagram-3 form-field-icon"></i>
                                    </span>
                                    <select name="department_id[]" multiple id="department_id" class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Problem Category (Multiple Select) -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label for="problem_category_id" class="form-label mb-0">
                                    {{ trans("ticket.service_ticket_fields.problem_category") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tags form-field-icon"></i>
                                    </span>
                                    <select name="problem_category_id[]" multiple id="problem_category_id" class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sub Category (Hidden by default, Multiple Select) -->
                        <div id="sub_category_id_cvr" class="row align-items-center mb-3 amg-form-field-row hide amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label for="sub_category_id" class="form-label mb-0">
                                    {{ trans("ticket.service_ticket_fields.sub_category") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-folder2-open form-field-icon"></i>
                                    </span>
                                    <select name="sub_category_id[]" multiple id="sub_category_id" class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Board Items Type -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label for="board_items_type" class="form-label mb-0 mandatory">
                                    {{ trans("ticket.service_ticket_fields.board_items_type") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-grid-3x3-gap form-field-icon"></i>
                                    </span>
                                    <select name="board_items_type" id="board_items_type" class="form-select filter-input form-with-icon">
                                        <option value="">{{ trans('ticket.service_ticket_fields.select_board') }}</option>
                                        @if (!Auth::user()->hasRole('User') ? 'selected': '')
                                            <option value="1">{{ trans('ticket.service_ticket_fields.ticket') }}</option>
                                        @endif
                                        <option value="2">{{ trans('ticket.service_ticket_fields.custom') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Custom FieldSet (Hidden) -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field" style="display: none;">
                            <div class="col-md-3 text-md-start">
                                <label for="custom_fieldset" class="form-label mb-0">FieldSets</label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-layers form-field-icon"></i>
                                    </span>
                                    <select name="custom_fieldset[]" id="custom_fieldset" class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Archive Days (Hidden) -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field" style="display: none;">
                            <div class="col-md-3 text-md-start">
                                <label for="archive_days" class="form-label mb-0">Archived After Days</label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-calendar2-week form-field-icon"></i>
                                    </span>
                                    <input type="number" name="archive_days" id="archive_days" class="form-control filter-input form-with-icon"
                                           placeholder="put days for archived">
                                </div>
                            </div>
                        </div>

                        <!-- Sprint Name (item - Multiple Select) -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label for="item" class="form-label mb-0 mandatory">
                                    {{ trans("ticket.service_ticket_fields.sprint_name") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-list-ul form-field-icon"></i>
                                    </span>
                                    <select name="item[]" multiple id="item" class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row align-items-start mb-3 amg-form-field-row amg-form-field">
                            <div class="col-md-3 text-md-start">
                                <label for="description" class="form-label mb-0">
                                    {{ trans("ticket.service_ticket_fields.description") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <textarea name="description" autocomplete="off" id="description" class="w-100 form-control"></textarea>
                            </div>
                        </div>

                        <!-- Checkbox: Auto Comment on Card Change -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field" id="comment_checkbox">
                            <div class="col-md-3 text-md-start">
                                <label for="auto_comment_on_card_change" class="form-label mb-0">
                                    {{ trans("ticket.service_ticket_fields.auto_comment_on_card_change") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="checkbox" name="auto_comment_on_card_change" id="auto_comment_on_card_change"
                                       value="1" class="form-check-input">
                            </div>
                        </div>

                        <!-- Checkbox: Access to All Department Technician -->
                        <div class="row align-items-center mb-3 amg-form-field-row amg-form-field" id="technician_checkbox">
                            <div class="col-md-3 text-md-start">
                                <label for="access_to_all_department_technician" class="form-label mb-0">
                                    {{ trans("ticket.service_ticket_fields.access_to_all_department_technician") }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <input type="checkbox" name="access_to_all_department_technician" id="access_to_all_department_technician"
                                       value="1" class="form-check-input">
                            </div>
                        </div>

                        <!-- Spinner (hidden by default) -->
                        <i class="fa fa-spinner fa-spin" id="img" style="display:none"></i>
                    </div>
                </div>

                <!-- Footer -->
                <div class="amg-form-footer modal-footer">
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-md col-md-2">
                        {{ trans("button.save") }}
                    </button>
                    <button type="button" id="btnClear" data-bs-dismiss="modal"
                            class="amg-btn amg-btn-ghost bg-black text-white amg-btn-md col-md-2">
                        {{ trans("button.close") }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>