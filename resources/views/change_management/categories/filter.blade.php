{{-- @page-meta
{
  "page_no": "PC05F-26",
  "file": "filter.blade.php",
  "versions": [
    {
      "version": "2.0",
      "writer": "Sandeep Verma",
      "from": "2026-07",
      "reviewer": null,
      "description": "Redesigned filter modal to match new pattern"
    }
  ]
}
--}}

<div class="amg-modal modal fade" id="cmCategoryFilter" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:900px;">
        <form class="w-100 amg-form-theme">
            <div class="modal-content rounded-5">

                {{-- Header --}}
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title s1-text fw-semibold px-4">
                        {{ trans("content.user_fields.Filter") }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                        <svg style="height:27px;width:27px;min-width:27px;flex-shrink:0;" viewBox="0 0 31 31" fill="none">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.3048 21.2505 11.0026 21.3757 10.6875 21.3757C10.3724 21.3757 10.0702 21.2505 9.84735 21.0277C9.62453 20.8048 9.49935 20.5026 9.49935 20.1875C9.49935 19.8724 9.62453 19.5702 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.5702 9.62452 19.8724 9.49934 20.1875 9.49934C20.5026 9.49934 20.8048 9.62452 21.0277 9.84734C21.2505 10.0702 21.3757 10.3724 21.3757 10.6875C21.3757 11.0026 21.2505 11.3048 21.0277 11.5277Z" fill="#7F7F7F"/>
                        </svg>
                    </button>
            </div>

                {{-- Body --}}
            <div class="modal-body">
                    <div class="row g-3 px-4">
                        {{-- Department --}}
                        <div class="col-md-4">
                            <label class="form-label b5-text" for="filter_by_department_id">
                                {{ trans("change-management.change_management_fields.department") }}
                            </label>
                            <select id="filter_by_department_id" name="filter_by_department_id" class="form-select filter-input">
                                {{-- options dynamically loaded --}}
                            </select>
                        </div>

                        {{-- Date Type --}}
                        <div class="col-md-4">
                            <label class="form-label b5-text" for="filter_by_date">
                                {{ trans("content.filter_heading.filter_by_date") }}
                            </label>
                            <select id="filter_by_date" name="filter_by_date" class="form-select filter-input">
                                <option value="null">{{ trans("change-management.change_management_fields.No_Filter") }}</option>
                                <option value="1">{{ trans("change-management.change_management_fields.Created_Date") }}</option>
                                <option value="2">{{ trans("change-management.change_management_fields.Updated_Date") }}</option>
                            </select>
                        </div>

                        {{-- Date Range --}}
                        <div class="col-md-4">
                            <label class="form-label b5-text" for="reportrange">
                                {{ trans("content.filter_heading.filter_by_daterange") }}
                            </label>
                            <div id="reportrange" class="form-control d-flex justify-content-between align-items-center">
                                <span></span>
                                <input type="hidden" id="daterange" name="daterange">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="amg-form-footer modal-footer justify-content-end mb-4 py-0">
                    <div class="amg-btn-group mt-3 gap-3 px-4">
                        <button type="button" id="clearFilter" class="amg-btn amg-btn-secondary amg-btn-md" style="min-width:180px;">
                            {{ trans("content.filter_heading.Clear") }}
                        </button>
                        <button type="button" id="applyFilter" class="amg-btn amg-btn-primary text-white" style="min-width:180px;">
                            {{ trans("button.filter") }}
                        </button>
            </div>
            </div>

        </div>
        </form>
    </div>
</div>