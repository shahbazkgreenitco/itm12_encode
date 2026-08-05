<div id="mdl-customCard" class="amg-modal amg-form-modal modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-5">
                <!-- Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title s1-text fw-semibold mb-0">
                        Create Custom Task
                    </h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31"
                            fill="none">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#7F7F7F" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                   <form id="frm-customCard" name="frm-customCard" method="post" action="#" class="form-horizontal">
                    <input type="hidden" id="id" name="id" value=""/>
                    <input type="hidden" id="tmp_id" name="tmp_id" value=""/>
                    <!-- Task Type -->
                    {{-- <div class="row mb-3 align-items-center" id="radioGroup">
                        <label class="col-lg-3 col-md-4 col-form-label">
                            Task Type
                        </label>
                        <div class="col-lg-9 col-md-8">
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="taskType" id="ticketOption"
                                        value="ticket" checked>
                                    <label class="form-check-label" for="ticketOption">
                                        Ticket
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="taskType" id="customTaskOption"
                                        value="customTask">

                                    <label class="form-check-label" for="customTaskOption">
                                        Custom Task
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Sprint -->
                    <div class="row mb-3 align-items-center amg-form-field" id="manualItemSelectWrapper" >
                        <label class="col-lg-3 col-md-4 col-form-label mandatory">
                            Select Sprint
                        </label>
                        <div class="col-lg-9 col-md-8">
                            <select id="manualItemSelect" class="form-control"></select>
                        </div>
                    </div>
                    <!-- Ticket -->
                    {{-- <div class="row mb-3 align-items-center" id="ticketIdGroup" style="display: none;">
                        <label class="col-lg-3 col-md-4 col-form-label mandatory">
                            Select Ticket
                        </label>
                        <div class="col-lg-9 col-md-8">
                            <div class="d-flex gap-2 align-items-center">
                                <div class="flex-grow-1">
                                    <select id="ticketId" name="ticketId[]" class="form-control select2" multiple>
                                    </select>
                                </div>
                                <span type="button" class="btn btn-outline-secondary btn-Filter">
                                    <i class="bi bi-funnel"></i>
                                    <div id="filter_counting" class="bg-red"></div>
                                </span>
                            </div>
                        </div>
                    </div> --}}
                    <!-- Custom Task -->
                    <div id="customTaskFields">
                        <div class="row mb-3 align-items-center amg-form-field">
                            <label class="col-lg-3 col-md-4 col-form-label mandatory">
                                Title
                            </label>
                            <div class="col-lg-9 col-md-8">
                                <input type="text" id="Title" name="Title" class="form-control"
                                    placeholder="Enter Task Title">
                            </div>
                        </div>

                        <div class="row mb-3 amg-form-field">
                            <label class="col-lg-3 col-md-4 col-form-label">
                                Description
                            </label>
                            <div class="col-lg-9 col-md-8">
                                <textarea id="description" name="description" rows="4" class="form-control"></textarea>
                                <div id="shows_error"></div>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center amg-form-field">
                            <label class="col-lg-3 col-md-4 col-form-label">
                                Department
                            </label>
                            <div class="col-lg-9 col-md-8">
                                <select id="taskDepartment" name="department" class="form-control select2">
                                </select>
                            </div>

                        </div>

                        <div class="row mb-3 align-items-center d-none amg-form-field">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                Problem Category
                            </label>

                            <div class="col-lg-9 col-md-8">

                                <select id="taskProblemCategory" name="problem_category" class="form-control select2">
                                </select>

                            </div>

                        </div>

                        <div class="row mb-3 align-items-center d-none amg-form-field">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                Sub Category
                            </label>

                            <div class="col-lg-9 col-md-8">

                                <select id="taskSubCategory" name="sub_category" class="form-control select2">
                                </select>

                            </div>

                        </div>

                        <div class="row mb-3 align-items-center amg-form-field">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                Status
                            </label>

                            <div class="col-lg-9 col-md-8">

                                <select id="status" name="status" class="form-control select2">
                                </select>

                            </div>

                        </div>

                        <div class="row mb-3 align-items-center amg-form-field">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                Priority
                            </label>

                            <div class="col-lg-9 col-md-8">

                                <select id="priority" name="priority" class="form-control select2">
                                </select>

                            </div>

                        </div>

                        <div class="row mb-3 align-items-center amg-form-field" id="date">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                Expected Date
                            </label>

                            <div class="col-lg-9 col-md-8">

                                <input type="datetime-local" id="expectedDate" name="expectedDate"
                                    class="form-control">

                            </div>

                        </div>

                        <div class="row mb-3 align-items-center amg-form-field">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                Reference Ticket
                            </label>

                            <div class="col-lg-9 col-md-8">

                                <select id="referenceTicket" name="referenceTicket" class="form-control select2">
                                </select>

                            </div>

                        </div>

                        <div class="row mb-3 align-items-center amg-form-field">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                Assign To
                            </label>

                            <div class="col-lg-9 col-md-8">

                                <select id="assign_to" name="assign_to[]" class="form-control select2" multiple>
                                </select>

                            </div>

                        </div>

                        <!-- Dynamic Custom Fields -->
                        <div class="row mb-3 amg-form-field">
                            <div class="kanbanCustomFieldWrapper"></div>
                        </div>
                        <div class="row mb-3 amg-form-field">

                            <label class="col-lg-3 col-md-4 col-form-label">
                                {{ trans('service_ticket.service_detail.attachment') }}
                            </label>
                            <div class="col-lg-9 col-md-8">
                                <div id="attachment-dropper-cover"
                                    class="amg-uploader"
                                    data-amg-uploader
                                    data-multiple="true"
                                    data-auto-upload="true"
                                    data-max-files="10"
                                    data-max-size="10"
                                    data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv"
                                    data-upload-url="{{ url('tickets/attachment/card_attachment') }}"
                                    data-remove-url="{{ url('tickets/attachment/card_attachment_remove') }}"
                                    data-token="{{ csrf_token() }}"
                                    data-record-input="#frm-customCard #tmp_id"
                                    data-upload-field="attachment">

                                    {{-- File Input --}}
                                   <input type="file"
                                        class="amg-uploader__input"
                                        multiple
                                        hidden>

                                    {{-- Dropzone --}}
                                    <div class="amg-uploader__dropzone border rounded d-flex flex-column align-items-center justify-content-center text-center srd-attachment-box">

                                        <div class="small d-flex align-items-center gap-2">

                                            <svg width="21"
                                                height="16"
                                                viewBox="0 0 21 16"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg">

                                                <path
                                                    d="M11.25 15.75V11.25H14.25L10.5 6.75L6.75 11.25H9.75V15.75H6V15.7125C5.87344 15.7195 5.75391 15.75 5.625 15.75C4.13316 15.75 2.70242 15.1574 1.64752 14.1025C0.592632 13.0476 0 11.6168 0 10.125C0.00194455 8.74787 0.511116 7.41972 1.43026 6.39422C2.34941 5.36872 3.61411 4.71774 4.98281 4.56562C5.22893 3.28221 5.91421 2.12454 6.92099 1.2914C7.92776 0.458267 9.19321 0.00166223 10.5 0C11.8072 0.00111796 13.0732 0.457475 14.0805 1.29066C15.0877 2.12384 15.7733 3.28182 16.0195 4.56562C17.3874 4.71881 18.651 5.37026 19.5691 6.39565C20.4873 7.42104 20.9958 8.7486 20.9977 10.125C20.9977 11.6168 20.405 13.0476 19.3501 14.1025C18.2952 15.1574 16.8645 15.75 15.3727 15.75C15.2484 15.75 15.1266 15.7195 14.9977 15.7125V15.75H11.25Z"
                                                    fill="#7F7F7F" />

                                            </svg>

                                            <span class="amg-uploader__trigger text-muted">

                                                {{ trans('service_ticket.service_detail.drag_drop_upload_files') }}

                                            </span>

                                        </div>

                                    </div>

                                    {{-- Preview --}}
                                    <div class="amg-uploader__preview mt-2" id="card_attachments"></div>

                                    {{-- Error --}}
                                    <div class="amg-uploader__error text-danger mt-1"></div>

                                </div>
        
                                <input type="file" id="card_attachment" name="card_attachment" style="visibility:hidden" />

                                <div class="text-muted mt-1 srd-attachment-note">
                                    {{ trans('service_ticket.service_detail.supported_file_types') }}
                                </div>
                                <div class="text-muted mt-1 srd-attachment-note">
                                    {{ trans('service_ticket.service_detail.max_files_size_message') }}
                                </div>

                            </div>
                        </div>
                    </div>
                     </form>
                </div>
                <div class="modal-footer justify-content-end pb-4 px-4">

                    <div class="amg-btn-group gap-3">

                        <button type="button" id="btnSave" class="amg-btn amg-btn-primary">
                            Save
                        </button>

                        <button type="button" id="btnCancel" class="amg-btn amg-btn-ghost bg-black text-white"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
       
    </div>
</div>
<style>
    label {
        text-align: end;
    }

    .form-control {
        height: 34px;
    }

    [class^="col-"]:not(.pad-no) {
        padding: 6px 15px 0px !important;
    }

    .select2-container--default {
        width: 100% !important;
    }

    .kanbanCustomFieldWrapper .form-group {
        padding-inline: 0;
    }
    .srd-attachment-box {
        border-style: dashed !important;
        min-height: 72px;
    }

    .srd-attachment-note {
        font-size: 11px;
    }
</style>
