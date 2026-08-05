{{-- @page-meta
{
  "page_no": "USR08M-26",
  "file": "merge-modal.blade.php",
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

<div class="amg-modal amg-form-modal modal fade" id="mergeMdl" tabindex="-1"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="merge-mdl-frm" method="post" action="#" class="form-horizontal w-100 amg-form-theme" onsubmit="return false;">
            
            <div class="modal-content rounded-5">

                <!-- HEADER -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">
                        {{ trans("content.user_fields.Merge_Users") }}
                        <span class="count_shower"></span>
                    </h3>

                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body bg-white">

                    <div class="container-fluid py-3">
                        <div class="row g-4 px-4">

                            <!-- NO USER -->
                            <div class="col-md-12 no_ticket text-center">
                                <p>{{ trans("content.user_fields.No_User_Added") }}</p>
                            </div>

                            <!-- PRIMARY USER -->
                            <div class="col-md-12 primary_ticket"></div>

                            <!-- OTHER USERS -->
                            <div class="col-md-12 others">
                                <ul class="list-group"></ul>
                            </div>

                            <!-- COMMENTS -->
                            <div class="col-md-12">
                                <div class="amg-form-field amg-form-field-row">
                                    <label for="comments" class="form-label b1-text required">
                                        {{ trans("content.service_ticket_fields.Comments") }}
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-chat-left-text"></i>
                                        </span>

                                        <textarea name="comments" id="comments"
                                            rows="4"
                                            class="form-control"
                                            placeholder="{{ trans('content.service_ticket_fields.Enter_Comments_about_this_merge') }}"
                                            style="resize: vertical;"></textarea>
                                    </div>

                                    <div class="amg-form-error-wrap"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="amg-form-footer modal-footer d-flex justify-content-end pb-4 py-0">

                    <!-- IMPORTANT: same IDs preserved -->
                    <button type="button" id="btnSubmit"
                        class="amg-btn amg-btn-primary col-md-3 amg-btn-md">
                        <i class="fa fa-code-fork"></i>
                        {{ trans("button.start_merge") }}
                    </button>

                    <button type="button" id="btnClear"
                        data-bs-dismiss="modal"
                        class="amg-btn amg-btn-outline col-md-3 amg-btn-md">
                        {{ trans("button.clear_list") }}
                    </button>

                </div>

            </div>
        </div>
    </div>
</div>
