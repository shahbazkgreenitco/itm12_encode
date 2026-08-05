{{-- @page-meta
{
  "page_no": "TKDAS05-26",
  "file": "add_tag.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-06",
      "reviewer": null,
      "description": "Initial Page Design and Develop"
    }
  ]
}
{
  "page_no": "TKDAS05-26",
  "file": "add_tag.blade.php",
  "versions": [
    {
      "version": "1.1",
      "writer": "Muzaffar Shaikh",
      "from": "2026-06",
      "reviewer": null,
      "description": "Modal Related UI Fixes"
    }
  ]
}
--}}

<div id="mdl-add-tag" class="amg-modal amg-form-modal modal fade" tabindex="-1"
    aria-labelledby="addTagModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div id="mdl_popup_loader"></div>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="frm-assign-to" name="frm_assign_to" method="post" action="#" class="form-horizontal w-100" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_token" id="token" class="hidden" value="" />
            <input type="hidden" name="id" id="id" class="hidden" value="{{ $ticket->id }}" />
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center">
                    <h3 class="modal-title">
                        Manage Tags
                    </h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-0 mb-xl-3 px-4">
                        <div class="col-12">
                            <div class="amg-form-field amg-form-field-row d-flex align-items-center amg-form-field-row">
                                <label for="assigned_tags" class="form-label b1-text me-2 mb-0 text-end">
                                    {{ trans('content.service_ticket_fields.tags') }}
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tags"></i>
                                    </span>
                                    <select id="assigned_tags" name="assigned_tag" class="form-control" multiple></select>
                                </div>
                                <div class="amg-form-error-wrap"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="amg-btn-group">
                            <button type="submit" id="btnSubmit" class="amg-btn amg-btn-primary">
                                {{ trans('button.save') }}
                            </button>

                        <button type="button" id="btnClear" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">
                                {{ trans('button.close') }}
                            </button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>

<style>
    #mdl-add-tag .amg-form-modal .select2-container--default .select2-selection--multiple .select2-selection__rendered, .amg-modal .select2-container--default .select2-selection--multiple .select2-selection__rendered{
        margin-top: 1rem !important;
    }
</style>
