{{-- @page-meta
{
  "page_no": "USR12P-26",
  "file": "print-modal.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    },
    {
      "version": "1.01",
      "writer": "Prithvi Pillai",
      "from": "2026-05",
      "reviewer": null,
      "description": "modal button position changes"
    }
  ]
}
--}}

<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">Print</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="print-modal-form">
                    <label class="form-label">Select Option</label>
                    <select id="print_opt" name="print_opt" class="form-select">
                        <option value="1">Barcode</option>
                        <option value="2">One Column</option>
                        <option value="3">Two Column</option>
                        <option value="4">Vertical Column</option>
                    </select>
                </form>
            </div>
            <div class="modal-footer justify-content-end pb-1 py-0" style="padding-inline: 40px;">
                <div class="d-flex gap-2">
                    <button type="button" id="btnClose" data-bs-dismiss="modal" class="amg-btn amg-btn-secondary amg-dark-secondary-button amg-btn-block bg-black text-white amg-btn-md mb-2">{{ trans('depreciations.modal.close') }}</button>
                   <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-block amg-btn-md mb-2">{{ trans('depreciations.modal.save') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
