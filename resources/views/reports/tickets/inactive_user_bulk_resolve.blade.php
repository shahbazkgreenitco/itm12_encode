<div class="modal fade" id="bulkResolveMdl" data-bs-backdrop="static" tabindex="-1" aria-labelledby="bulkResolveLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkResolveLabel">
                    {{ trans('content.inactive_user.bulk_resolve') }}
                    <span class="count_shower"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkResolve_ticket" name="bulkResolve" method="post" action="#">
                <div class="modal-body">
                    <div class="no_ticket mb-3">
                        <p>{{ trans('content.inactive_user.text_message') }}</p>
                    </div>
                    <div class="mb-3">
                        <ul class="assign_ticket"></ul>
                    </div>
                    <input type="hidden" name="ticket_id" id="ticket_id">
                    <div class="mb-3">
                        <label for="content" class="form-label mandatory">
                            {{ trans('content.inactive_user.reason') }}
                        </label>
                        <textarea class="form-control" id="content" name="content" rows="4"></textarea>
                    </div>
                    <div id="shows_error"></div>
                </div>
                <div class="amg-form-footer modal-footer">
                    <button type="button" id="btnBulkResolve" class="amg-btn amg-btn-primary amg-btn-md col-md-2">
                         {{ trans('button.submit') }}
                    </button>
                    <button type="button" id="btnClear" data-bs-dismiss="modal"
                        class="amg-btn amg-btn-ghost bg-black text-white amg-btn-md col-md-2">
                        {{ trans('button.close') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
