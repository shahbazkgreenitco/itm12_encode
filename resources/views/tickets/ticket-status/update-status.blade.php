{{-- @page-meta
{
  "page_no": "TS04C-26",
  "file": "update-status.blade.php",
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
@php
    $textIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="black" /></svg>
SVG;
    $companyIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5Z" fill="black" /></svg>
SVG;
    $calendarIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25Z" fill="black" /></svg>
SVG;
@endphp
<div class="amg-modal amg-form-modal modal fade" id="statusupdateModal" tabindex="-1"
    aria-labelledby="ticketStatusUpdateModalLabel" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="status_update_form" name="status_update_form" method="post" action="#"
            class="form-horizontal w-100 amg-form-theme" enctype="multipart/form-data" onsubmit="return false;"
            autocomplete="off">
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="forAction" name="forAction" value="">
            <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">

            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="ticketStatusUpdateModalLabel">{{ trans('ticket-status.form.edit_ticket_status') }}</h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body bg-white">
                    <div class="container-fluid py-3">
                        <div class="row section-row">
                            <div class="col">
                                <div id="iseditable">
                                    <div class="row my-4 px-4">
                                        <div class="col-md-6">
                                            <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                <label for="name" class="form-label b1-text me-2 mb-0 required">{{ trans('ticket-status.form.status_name_label') }}</label>
                                                <div class="input-group ">
                                                    <span class="input-group-text" id="basic-addon1">
                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="black" /></svg>
                                                    </span>
                                                    <input name="name" id="name" autocomplete="off" type="text"
                                                        class="form-control" placeholder="{{ trans('ticket-status.form.status_name_placeholder') }}" />
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                <label for="company_id" class="form-label b1-text me-2 mb-0 required">{{ trans('ticket-status.form.company_label') }}</label>
                                                <div class="input-group ">
                                                    <span class="input-group-text" id="basic-addon1">{!! $companyIcon !!}</span>
                                                    <select name="company_id" id="company_id" class="form-select">
                                                        <option value="">{{ trans('ticket-status.form.select_company') }}</option>
                                                    </select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4 px-4">
                                        <div class="col-md-6">
                                            <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                <label for="is_enabled" class="form-label b1-text me-2 mb-0 required">{{ trans('ticket-status.form.status_label') }}</label>
                                                <div class="input-group ">
                                                    <span class="input-group-text" id="basic-addon1">{!! $textIcon !!}</span>
                                                    <select class="form-select" id="is_enabled" name="is_enabled">
                                                        <option value=""></option>
                                                        <option value="1">{{ trans('ticket-status.common.enable') }}</option>
                                                        <option value="2">{{ trans('ticket-status.common.disable') }}</option>
                                                    </select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                <label for="tat_halt" class="form-label b1-text me-2 mb-0">{{ trans('ticket-status.form.tat_halt_label') }}</label>
                                                <div class="input-group ">
                                                    <span class="input-group-text" id="basic-addon1">{!! $calendarIcon !!}</span>
                                                    <select class="form-select" id="tat_halt" name="tat_halt">
                                                        <option value=""></option>
                                                        <option value="1">{{ trans('ticket-status.common.enable') }}</option>
                                                        <option value="0">{{ trans('ticket-status.common.disable') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4 px-4">
                                        <div class="col-md-6">
                                            <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                <label for="ticket_type" class="form-label b1-text me-2 mb-0">{{ trans('ticket-status.form.ticket_type') }}</label>
                                                <div class="input-group ">
                                                    <span class="input-group-text" id="basic-addon1">{!! $textIcon !!}</span>
                                                    <select class="form-select" id="ticket_type" name="ticket_type[]" multiple></select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                <label for="attachment" class="form-label b1-text me-2 mb-0">{{ trans('ticket-status.form.attachment_label') }}</label>
                                                <div class="input-group ">
                                                    <span class="input-group-text" id="basic-addon1"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="bi bi-upload">
                                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"></path>
                                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"></path>
                                                                </svg></span>
                                                    <input name="attachment" id="attachment" autocomplete="off" type="file"
                                                        class="form-control" aria-label="Attachment" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4 mb-4 px-4 d-none" id="uploadedFileSection">
                                            <div class="col-md-6">
                                                <div class="amg-form-field d-flex align-items-center amg-form-field-row" id="iseditable">
                                                    <label for="deleteFileCheckbox" class="form-label b1-text me-2 mb-0">{{ trans('ticket-status.form.delete_attachment') }}</label>
                                                    <div class="input-group ">
                                                        <input name="delete_attachment_checkbox" id="deleteFileCheckbox"
                                                            autocomplete="off" type="checkbox" value="1"
                                                            class="form-check-input ms-13 mt-2">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                                    <label for="imgview" class="form-label b1-text me-2 mb-0">{{ trans('ticket-status.form.current_attachment') }}</label>
                                                    <div class="amg-form-image-preview-wrap imgviewcover">
                                                        <div class="amg-form-image-preview">
                                                            <img src="" id="imgview" class="amg-form-image-preview-img"
                                                                alt="{{ trans('ticket-status.form.attachment_preview') }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="color_code" class="form-label b1-text me-2 mb-0">{{ trans('ticket-status.form.color_label') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">{!! $textIcon !!}</span>
                                                <input name="color_code" id="color_code" autocomplete="off" type="color" class="form-control" aria-label="Color" style="height: 40px;" />
                                                <input type="text" id="color_hex" class="form-control" readonly placeholder="#000000" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="status_form" class="form-label b1-text me-2 mb-0 required">{{ trans('ticket-status.form.status_form') }}</label>
                                            <div class="input-group ">
                                                <span class="input-group-text" id="basic-addon1">{!! $textIcon !!}</span>
                                                <select class="form-select" id="status_form" name="status_form">
                                                    <option value=""></option>
                                                    <option value="1">{{ trans('ticket-status.common.yes') }}</option>
                                                    <option value="0">{{ trans('ticket-status.common.no') }}</option>
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="row mb-4 px-4">
                                    <div class="col-12 form-repeater mb-1">
                                        <div class="amg-form-section-title d-flex align-items-center justify-content-center justify-content-md-start text-center text-md-start mb-3">
                                            <span class="fs-11 fs-md-5"> {{ trans('ticket-status.mapping.form_mapping') }}</span>
                                        </div>
                                        <div class="status-update-form-mapping">
                                            <div data-repeater-list="forms">
                                                <div data-repeater-item class="row g-3 align-items-stretch status-update-repeater-item">
                                                    <div class="col-md-6">
                                                        <div class="amg-form-field d-flex align-items-center status-update-repeater-field amg-form-field-row">
                                                            <label class="form-label b1-text me-2 mb-0 required">{{ trans('ticket-status.mapping.problem_category') }}</label>
                                                            <div class="input-group ">
                                                                <span class="input-group-text" id="basic-addon1">{!! $textIcon !!}</span>
                                                                <select name="pc_id" class="form-select pc_id">
                                                                    <option value=""></option>
                                                                    @foreach ($problemCategories as $problemCategory)
                                                                        <option value="{{ $problemCategory->id }}">
                                                                            {{ $problemCategory->name }}@if ($problemCategory->department) - ({{ $problemCategory->department->name }}) @endif
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="amg-form-error-wrap"></div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="amg-form-field d-flex align-items-center status-update-repeater-field amg-form-field-row">
                                                            <label class="form-label b1-text me-2 mb-0">{{ trans('ticket-status.mapping.sub_category') }}</label>
                                                            <div class="input-group ">
                                                                <span class="input-group-text" id="basic-addon1">{!! $textIcon !!}</span>
                                                                <select name="sc_id" class="form-select sc_id">
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>
                                                            <div class="amg-form-error-wrap"></div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="amg-form-field d-flex align-items-center status-update-repeater-field amg-form-field-row">
                                                            <label class="form-label b1-text me-2 mb-0 required">{{ trans('ticket-status.mapping.form') }}</label>
                                                            <div class="input-group ">
                                                                <span class="input-group-text" id="basic-addon1">{!! $textIcon !!}</span>
                                                                <select name="form_id" class="form-select form_id">
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>
                                                            <div class="amg-form-error-wrap"></div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1">
                                                        <div class="status-update-repeater-action">
                                                            <button type="button" class="btn status-update-repeater-delete" data-repeater-delete>
                                                                <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                                                                    <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                                                                </svg></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" data-repeater-create class="amg-btn amg-btn-primary amg-btn-md status-update-repeater-add">
                                               {{ trans('ticket-status.mapping.add_new') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="amg-form-footer modal-footer d-flex justify-content-end row py-4">
                    <button type="button" data-bs-dismiss="modal" class="amg-btn amg-btn-secondary amg-btn-block amg-btn-md col-md-2 mb-2">{{ trans('ticket-status.common.close') }}</button>
                    <button type="button" id="btnUpdate" class="amg-btn amg-btn-primary amg-btn-block amg-btn-md col-md-2 js-act-update">{{ trans('ticket-status.common.update') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
