<div id="thresholdMdl" class="amg-modal modal fade user-mdl-box relevant-ticket-mdl-box" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="thresholdFrm" name="thresholdFrm" method="post" action="#" class="amg-form-theme form-horizontal w-100"
            enctype="multipart/form-data" onsubmit="return false;">
            <div class="modal-content">
                {{-- modal head --}}
                <div class="modal-header">
                    <h3 class="modal-title s2-text fw-semibold px-2" id="locationModalLabel">
                        {{ trans('threshold.modal.edit_threshold_header') }}
                    </h3>
                    <button type="button" class="modal-close px-2" data-bs-dismiss="modal" aria-label="Close">
                        <svg viewBox="0 0 31 31"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#7F7F7F" />
                        </svg>
                    </button>
                </div>

                {{-- modal body --}}
                <div class="modal-body">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="container-fluid py-3">
                            <div class="row g-4 px-4">
                                <div class="col-md-6 amg-form-field amg-form-field-row">
                                    <label class="form-label b1-text mb-2">
                                        {{ trans('threshold.modal.threshold_module') }}
                                    </label>

                                    <div class="form-check">
                                        <input class="form-check-input" @if ($TS->threshold_enabled) checked @endif
                                            name="threshold_enabled" type="checkbox" value="1"
                                            id="threshold_enabled"
                                            onchange="$(this).prop('checked') ? $('.thre').show() : $('.thre').hide()">
                                        <label class="form-check-label" for="threshold_enabled">
                                            {{ trans('threshold.modal.enabled') }}
                                        </label>
                                    </div>

                                    {!! $errors->first('name', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                                </div>

                                <div class="col-md-6 amg-form-field amg-form-field-row thre"
                                    style="display: {{ (count($errors) ? old('threshold_enabled') : $TS->threshold_enabled) ? 'block' : 'none' }};">
                                    <label class="form-label b1-text mb-2">
                                        {{ trans('threshold.modal.alert_notification') }}
                                    </label>

                                    <div class="form-check">
                                        <input class="form-check-input" @if (count($errors) ? old('alerts_enabled') : $TS->alerts_enabled) checked @endif
                                            name="alerts_enabled" type="checkbox" value="1" id="alerts_enabled"
                                            onchange="$(this).prop('checked') ? $('.alrt').show() : $('.alrt').hide()">
                                        <label class="form-check-label" for="alerts_enabled">
                                            {{ trans('threshold.modal.enabled') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12 amg-form-field amg-form-field-row thre alrt"
                                    style="display: {{ (count($errors) ? old('alerts_enabled') : $TS->alerts_enabled && $TS->threshold_enabled) ? 'block' : 'none' }};">
                                    <label class="form-label b1-text mb-2">
                                        {{ trans('threshold.modal.send_alerts_to') }}
                                    </label>

                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path
                                                    d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <select class="form-select" id="send_alerts" name="send_alerts"
                                            onchange="$('#send_alerts').val() == '1' ? $('.email').show() : $('.email').hide()">
                                            <option value="0" @if ((count($errors) ? old('send_alerts') : $TS->send_alerts) == '0') selected @endif>
                                                {{ trans('threshold.modal.alerts_option1') }}
                                            </option>
                                            <option value="1" @if ((count($errors) ? old('send_alerts') : $TS->send_alerts) == '1') selected @endif>
                                                {{ trans('threshold.modal.alerts_option2') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 amg-form-field amg-form-field-row thre alrt threshold_alert thr_alrt email"
                                    style="display: {{ (count($errors) ? old('alerts_enabled') : $TS->alerts_enabled && $TS->threshold_enabled) ? 'block' : 'none' }};">
                                    <label class="form-label b1-text mb-2 required">
                                        {{ trans('threshold.modal.email_address') }}
                                    </label>

                                    <div class="d-flex select-div">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path
                                                    d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <select name="email[]" id="email" class="form-select" multiple>
                                            @foreach (explode(',', isset($TS) ? $TS->email : '') as $option)
                                                <option value="{{ $option }}"
                                                    {{ $option != '' ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Footer --}}
                <div class="amg-form-footer modal-footer justify-content-end py-4 px-4">
                    <div class="d-flex gap-2">
                        <button type="button" id="btnClose" data-bs-dismiss="modal"
                            class="amg-btn amg-btn-secondary amg-btn-block amg-btn-md">
                            {{ trans('threshold.modal.close') }}
                        </button>
                        <button type="button" id="btnSubmit"
                            class="amg-btn amg-btn-primary amg-btn-block amg-btn-md">
                            {{ trans('threshold.modal.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
