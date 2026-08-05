{{-- 
/**
------------------------------------------------------------
File: note_modal.blade.php
Module: Accessories
ACC/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-006
Created On: 2026-05-12
Reviewed By: -
------------------------------------------------------------
Purpose:
Accessories Notes View Modal

------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}

<div id="noteModal" class="amg-modal modal fade" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-5">
            <!-- HEADER -->
            <div class="modal-header d-flex align-items-center py-3 pt-4 border-0">
                <h3 class="modal-title px-4" id="noteModalLabel">
                    {{ trans("accessories.accessory_fields.notes") }}
                </h3>
                <button type="button" class="modal-close px-4 border-0 bg-transparent" data-bs-dismiss="modal"
                    aria-label="Close">
                    <svg class="amg-modal-close-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body bg-white px-4 pb-3">

                <div class="amg-form-field">

                    <label class="form-label mb-2">
                        {{-- Accessory Notes --}}
                          {{ trans("accessories.accessory_fields.notes") }}
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-card-text"></i>
                        </span>
                        <textarea id="noteContent" class="form-control" rows="6" readonly></textarea>

                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" id="btnClear" class="amg-btn amg-btn-secondary" data-bs-dismiss="modal">
                    {{ trans('button.close') }}
                </button>
            </div>
        </div>
    </div>
</div>