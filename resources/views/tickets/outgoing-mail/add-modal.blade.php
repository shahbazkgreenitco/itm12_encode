{{-- @page-meta
{
  "page_no": "TOM02C-26",
  "file": "add-modal.blade.php",
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
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="currentColor" /></svg>
SVG;
    $companyIcon = <<<'SVG'
<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.125 17.5H16.25V2.5H16.875C17.0408 2.5 17.1997 2.43415 17.3169 2.31694C17.4342 2.19973 17.5 2.04076 17.5 1.875C17.5 1.70924 17.4342 1.55027 17.3169 1.43306C17.1997 1.31585 17.0408 1.25 16.875 1.25H3.125C2.95924 1.25 2.80027 1.31585 2.68306 1.43306C2.56585 1.55027 2.5 1.70924 2.5 1.875C2.5 2.04076 2.56585 2.19973 2.68306 2.31694C2.80027 2.43415 2.95924 2.5 3.125 2.5H3.75V17.5H1.875C1.70924 17.5 1.55027 17.5658 1.43306 17.6831C1.31585 17.8003 1.25 17.9592 1.25 18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H18.125C18.2908 18.75 18.4497 18.6842 18.5669 18.5669C18.6842 18.4497 18.75 18.2908 18.75 18.125C18.75 17.9592 18.6842 17.8003 18.5669 17.6831C18.4497 17.5658 18.2908 17.5 18.125 17.5ZM5 2.5H15V17.5H12.5V14.375C12.5 14.2092 12.4342 14.0503 12.3169 13.9331C12.1997 13.8158 12.0408 13.75 11.875 13.75H8.125C7.95924 13.75 7.80027 13.8158 7.68306 13.9331C7.56585 14.0503 7.5 14.2092 7.5 14.375V17.5H5V2.5ZM11.25 17.5H8.75V15H11.25V17.5Z" fill="currentColor" /></svg>
SVG;
@endphp

<div class="amg-modal amg-form-modal modal fade" id="mailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="mail-mdl-frm" name="mail-mdl-frm" method="post" action="#" class="form-horizontal w-100 amg-form-theme" enctype="multipart/form-data" onsubmit="return false;" autocomplete="off">
            @csrf
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="for_action" name="for_action" value="">

            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">{{ trans('outgoing-mail-setting.modal.create_title') }}</h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="{{ trans('outgoing-mail-setting.modal.close') }}">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <div class="row my-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_enabled" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_enabled') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <select name="mail_enabled" id="mail_enabled" class="form-select">
                                            <option value="1">{{ trans('outgoing-mail-setting.options.enable') }}</option>
                                            <option value="0">{{ trans('outgoing-mail-setting.options.disable') }}</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="company_id" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.company_id') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $companyIcon !!}</span>
                                        <select name="company_id" id="company_id" class="form-select">
                                            <option value="">{{ trans('outgoing-mail-setting.placeholders.company_id') }}</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_from_name" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_from_name') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input name="mail_from_name" id="mail_from_name" autocomplete="off" type="text" class="form-control" placeholder="{{ trans('outgoing-mail-setting.placeholders.mail_from_name') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_driver" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_driver') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input name="mail_driver" id="mail_driver" autocomplete="off" type="text" class="form-control" placeholder="{{ trans('outgoing-mail-setting.placeholders.mail_driver') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_host" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_host') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input name="mail_host" id="mail_host" autocomplete="off" type="text" class="form-control" placeholder="{{ trans('outgoing-mail-setting.placeholders.mail_host') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_port" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_port') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input name="mail_port" id="mail_port" autocomplete="off" type="text" class="form-control" placeholder="{{ trans('outgoing-mail-setting.placeholders.mail_port') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_username" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_username') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input name="mail_username" id="mail_username" autocomplete="off" type="text" class="form-control" placeholder="{{ trans('outgoing-mail-setting.placeholders.mail_username') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_password" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_password') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input name="mail_password" id="mail_password" autocomplete="new-password" type="password" class="form-control" placeholder="{{ trans('outgoing-mail-setting.placeholders.mail_password') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 px-4">
                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_encryption" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_encryption') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <select name="mail_encryption" id="mail_encryption" class="form-select">
                                            <option value="ssl">{{ trans('outgoing-mail-setting.options.ssl') }}</option>
                                            <option value="tls">{{ trans('outgoing-mail-setting.options.tls') }}</option>
                                            <option value="false">{{ trans('outgoing-mail-setting.options.false') }}</option>
                                        </select>
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="amg-form-field d-flex align-items-center">
                                    <label for="mail_from_address" class="form-label b1-text me-2 mb-0 text-end required">{{ trans('outgoing-mail-setting.fields.mail_from_address') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{!! $textIcon !!}</span>
                                        <input name="mail_from_address" id="mail_from_address" autocomplete="off" type="text" class="form-control" placeholder="{{ trans('outgoing-mail-setting.placeholders.mail_from_address') }}">
                                    </div>
                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="amg-form-footer modal-footer d-flex justify-content-end row pb-4 py-0">
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-block amg-btn-md col-md-2 js-act-create">{{ trans('outgoing-mail-setting.modal.create') }}</button>
                    <button type="button" id="btnClear" data-bs-dismiss="modal" class="amg-btn amg-btn-ghost s2-text bg-black text-white amg-btn-md col-md-2">{{ trans('outgoing-mail-setting.modal.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
