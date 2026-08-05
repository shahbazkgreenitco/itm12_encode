{{-- @page-meta
{
"page_no": "TKTE-05-2026",
"file": "bulk-assign.blade.php",
"versions": [
{
"version": "1.1",
"writer": "Shivam Kumar",
"from": "2026-06",
"reviewer": null,
"description": "bulk assign balde"
}
]
}
--}}
<div class="amg-modal amg-form-modal modal modal-lg fade" id="bulkAssignMdl" tabindex="-1"
    aria-labelledby="bulkAssignMdlLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="edit-ticket-mdl-frm" class="form-horizontal w-100 amg-form-theme" autocomplete="off"
            onsubmit="return false;">
            @csrf
            <div class="modal-content rounded-5">
                <!-- Modal Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title fw-bold px-4" id="customStatusModalTitle">
                        {{ trans('ticket.bulk_assign_to') }}
                    </h3>
                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#515151" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- === NEW: Length Dropdown & Search Bar (Matches Reference Design) === -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="col-auto">
                                <select class="amg-table-pagination-dropdown userModulePageLenth bulk-ticket-page-length form-select form-select-sm">
                                    <option value="10" selected>Show (10)</option>
                                    <option value="25">Show (25)</option>
                                    <option value="50">Show (50)</option>
                                    <option value="100">Show (100)</option>
                                </select>
                            </div>
                            <div class="flex-grow-1"></div>
                            <div>
                                <div class="amg-list-searchbar">
                                    <svg class="amg-list-searchbar__icon search-icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                    </svg>
                                    <input type="text" class="amg-list-searchbar__input bulk-ticket-search" placeholder="Search...">
                                </div>
                            </div>
                        </div>
                        <!-- Existing Table -->
                        <div class="table-responsive mb-3">
                            <table id="bulkAssignTicket" class="table table-striped table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ trans('ticket.id') }}</th>
                                        <th>{{ trans('ticket.subject') }}</th>
                                        <th>{{ trans('ticket.created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <!-- Existing Assign To Dropdown -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="">
                                    <label for="users" class="form-label b3-text fw-bold mb-1 required">{{ trans('ticket.ticket_list.assign_to') }}</label>
                                    <div class="input-group">
                                        <select name="users" id="users" class="form-select">
                                            <option value="">{{ trans('ticket.edit_ticket.select_user') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <label class="text-danger availabilityError"></label>
                                <label class="text-success availabilitySuccess"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="amg-form-footer gap-2 modal-footer d-flex justify-content-end pb-4 py-0">
                    <button type="button" id="btnClear" data-bs-dismiss="modal"
                        class="amg-btn amg-btn-secondary col-md-2 amg-btn-md">
                        {{ trans('ticket.update_status.cancel') }}</button>
                    <button type="button" id="btnBulkAssignSubmit"
                        class="amg-btn amg-btn-primary amg-btn-block col-md-2 amg-btn-md" style="min-width: 250px">
                        {{ trans('ticket.update_status.submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>

    

</style>