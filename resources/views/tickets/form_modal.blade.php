{{-- @page-meta
{
    "page_no": "DF05-26",
    "file": "form_modal.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Dynamic Form Modal"
        }
    ]
}
--}}

{{-- Service Request Modal Popup --}}
<div class="amg-modal amg-form-modal modal fade" id="mdl-servicerequest" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

    <div id="mdl_popup_loader"></div>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form name="requested_form"
            id="requested_form"
            method="post"
            action="#"
            class="form-horizontal requested_form w-100 amg-form-theme"
            autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="form_id" id="form_id" value="">
            <input type="hidden" name="request_id" id="request_id" value="">
            <input type="hidden" id="tmp_id" name="tmp_id" value="" />
            <input type="hidden" name="for_action" id="for_action" value="">
            <input type="hidden" name="field_form_required" id="field_form_required" value="">
            <input type="hidden" name="dept_id" id="dept_id" value="">
            <input type="hidden" name="prob_id" id="prob_id" value="">
            <input type="hidden" name="user_id" id="user_id" value="">
            <div class="modal-content rounded-5 formModel">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4 form_title">
                        {{ trans("content.service_ticket_fields.service_request_form") }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                            fill="#515151" />
                    </svg>
                </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 hide">
                            <div class="form-group">
                                <label class="control-label mandatory" for="descriptions"></label>
                                <div>
                                    <textarea name="field_values" id="field_values" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="build-wrap"></div>
                </div>
                <div class="amg-form-footer modal-footer d-flex justify-content-end pb-4 py-0" id="footer_button">
                    <button type="button" id="saveData" class="amg-btn amg-btn-primary amg-btn-block col-md-2 amg-btn-md">
                        {{ trans("button.submit") }}
                    </button>

                    <button type="button" id="btnClear" data-bs-dismiss="modal" class="amg-btn amg-btn-secondary amg-btn-block col-md-2 amg-btn-md">
                        {{ trans("button.cancel") }}
                    </button>
                </div>
            </div>

        </form>

    </div>

</div>
<style>
    .formModel{
        box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px;
    }
</style>


@push('scripts')
<script type="text/javascript" src="{!! CommonHelper::asset('js/form/requested_form.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/form-render.min.js') !!}"></script>
<script>
    $(document).ready(function() {
        var t = this;
        var config = {};
        config.url = {};
        config.url.update_form = "{{ url('dynamic_form/update_form') }}";
        config.url.form_list = "{{ url('dynamic_form') }}";
        config.requested_form = "{{ url('requested_form') }}";
        config.url.edit = "{{ url('requested_form/edit') }}";
        config.url.update = "{{ url('dynamic_form/form/update') }}";
        config.editFormImage = "{{asset('images/edit.jpeg')}}";
        config.url.countries = "{{ url('jx-holidays-countries') }}";
        config.url.ticekt_getcustomview ="{{ url('tickets/ticekt_getcustomview') }}";
        config.url.requested_custom_form = "{{ url('requested_form/custom_form/edit') }}";
        config.url.fetch_active_ticket = "{{ url('tickets/fetch-active-access') }}";
        config.url.requestInfo = "{{ url('tickets/requestInfo') }}";
        config.url.fetch_git_form = "{{ url('tickets/fetch-git-form') }}";
        config.translations = {
            edit: '{{ trans('content.service_ticket_fields.edit') }}',
            file_size: '{{ trans('content.dynamic_form.file_size') }}',
        };
        config.token = "{{ csrf_token() }}";
        config.client = "{{ config('app.client') }}";
        config.sub_client = "{{ config('app.sub_client') }}";
        new RequestedForm(config);
    });
</script>
@endpush

