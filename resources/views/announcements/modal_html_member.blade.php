{{-- * ------------------------------------------------------------
* File: modal_html_member.blade.php
* Module: Announcement Module
* Anounc/26/05
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #005
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
<div id="announcementMemberMdl" class="amg-modal modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered">
        <form id="announcementMemberForm" class="w-100" method="post" onsubmit="return false;">

            <div class="modal-content rounded-4">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ trans("config.holiday_fields.add_holiday") }}
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <!-- Add Members -->
                    <div class="mb-3 addMember">
                        <label class="form-label">
                            {{ trans("config.announcement.users") }}
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-people"></i>
                            </span>

                            <select multiple name="users[]" id="users" class="form-select select2"></select>
                        </div>
                    </div>

                    <!-- Check All -->
                    <div class="mb-3 checkAllBox">
                        <label class="form-label">
                            {{ trans("config.announcement.checkAll") }}
                        </label>

                        <div class="form-check">
                            <input type="checkbox"
                                   name="all_user_check"
                                   id="all_user_check"
                                   value="1"
                                   class="form-check-input">

                            <label class="form-check-label" for="all_user_check">
                                Select All Users
                            </label>
                        </div>
                    </div>

                    <!-- Edit Member -->
                    <div class="mb-3 editMember d-none">
                        <label class="form-label">
                            {{ trans("config.announcement.users") }}
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-users"></i>
                            </span>

                            <select name="user"
                                    id="user"
                                    class="form-select select2"></select>
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{ $announcement->id }}">

                </div>

                <!-- Footer -->
                <div class="modal-footer px-4 pb-4">
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary">Save</button>
                    <button type="button" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .input-group-text {
        background: #e5e7eb;   /* light grey like screenshot */
        border: 1px solid #d1d5db;
        color: #374151;        /* dark grey icon */
        min-width: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-group {
        display: flex;
        align-items: stretch;
        flex-wrap: nowrap !important;
    }

    /* Fix select2 inside input-group */
    .input-group .select2-container {
        width: 100% !important;
    }




</style>
