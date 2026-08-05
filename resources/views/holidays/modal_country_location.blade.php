{{-- * ------------------------------------------------------------
* File: modal_country_location.blade.php
* Module: Holiday Module
* HOLY/26/03
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #003
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}
<div class="modal fade" id="descriptionMdl" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content rounded-4 bg-white">

            <!-- Header -->
            <div class="modal-header py-3">
                <h5 class="modal-title">Details</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body" id="modalBodyContent"
                 style="max-height: 350px; overflow-y: auto;">
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>