{{-- @page-meta
{
  "page_no": "PC05F-26",
  "file": "filter.blade.php",
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
<div class="amg-modal modal fade" id="advanceFilterModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1200px;">
        <form class="w-100 amg-form-theme">
            <div class="modal-content rounded-5">
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title s1-text fw-semibold px-4">{{ trans("problem_category.service_ticket_fields.ticket_problem_categories_filter") }}</h3>
                    <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#7F7F7F" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Row 1 -->
                    <div class="row g-3 px-4">
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_department">{{ trans("problem_category.service_ticket_fields.filter_by_department") }}</label>
                            <select id="filter_by_department" name="filter_by_department[]" class="form-select filter-input" multiple></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_priority">{{ trans("problem_category.service_ticket_fields.Filter_By_Priority") }}</label>
                            <select id="filter_by_priority" name="filter_by_priority[]" class="form-select filter-input" multiple></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="authority_approval">{{ trans("problem_category.service_ticket_fields.filter_by_authority_approval") }}</label>
                            <select id="authority_approval" name="authority_approval" autocomplete="off" class="form-select filter-input">
                                <option value="">{{ trans("problem_category.service_ticket_fields.No_Filter") }}</option>
                                <option value="1">{{ trans("problem_category.service_ticket_fields.Required") }}</option>
                                <option value="0">{{ trans("problem_category.service_ticket_fields.Not_Required") }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_allocation_group">{{ trans("problem_category.service_ticket_fields.filter_by_allocation_Group") }}</label>
                            <select id="filter_allocation_group" name="filter_allocation_group[]" class="form-select filter-input" multiple></select>
                        </div>
                    </div>
                    <!-- Row 2 -->
                    <div class="row g-3 mt-8 px-4">
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_pab">{{ trans("problem_category.service_ticket_fields.filter_by_authority_board") }}</label>
                            <select id="filter_by_pab" name="filter_by_pab[]" class="form-select filter-input" multiple></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_form">{{ trans("problem_category.service_ticket_fields.filter_by_formname") }}</label>
                            <select id="filter_by_form" name="filter_by_form[]" class="form-select filter-input" multiple></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_based_on_category">{{ trans("problem_category.service_ticket_fields.filter_by_category") }}</label>
                            <select id="filter_based_on_category" name="filter_based_on_category" class="form-select filter-input">
                                <option value="">{{ trans("problem_category.service_ticket_fields.filter_by_category") }}</option>
                                <option value="1">{{ trans("problem_category.service_ticket_fields.Filter_By_Problem_Category") }}</option>
                                <option value="2">{{ trans("problem_category.service_ticket_fields.Filter_By_Sub_Category") }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_escalation">{{ trans("problem_category.service_ticket_fields.filter_by_escalation") }}</label>
                            <select id="filter_by_escalation" name="filter_by_escalation" class="form-select filter-input">
                                <option value="">{{ trans("problem_category.service_ticket_fields.No_Filter") }}</option>
                                <option value="yes">{{ trans("problem_category.service_ticket_fields.Filter_By_escalation_yes") }}</option>
                                <option value="no">{{ trans("problem_category.service_ticket_fields.Filter_By_escalation_no") }}</option>
                            </select>
                        </div>
                    </div>
                    <!-- Row 3 -->
                    <div class="row g-3 mt-8 px-4">
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_escalation_for">{{ trans("problem_category.service_ticket_fields.filter_by_escalation_for") }}</label>
                            <select id="filter_by_escalation_for" name="filter_by_escalation_for" class="form-select filter-input">
                                <option value="">{{ trans("problem_category.service_ticket_fields.No_Filter") }}</option>
                                <option value="user">{{ trans("problem_category.service_ticket_fields.Filter_By_escalation_to_user") }}</option>
                                <option value="group">{{ trans("problem_category.service_ticket_fields.Filter_By_escalation_to_group") }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_escalation_to">{{ trans("problem_category.service_ticket_fields.filter_by_escalation_to") }}</label>
                            <select id="filter_by_escalation_to" name="filter_by_escalation_to" class="form-select filter-input"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label b5-text" for="filter_by_status">{{ trans("problem_category.service_ticket_fields.filter_by_status") }}</label>
                            <select id="filter_by_status" name="filter_by_status" class="form-select filter-input">
                                <option value="">{{ trans("problem_category.service_ticket_fields.filter_by_status") }}</option>
                                <option value="1">{{ trans("problem_category.service_ticket_fields.enabled_d") }}</option>
                                <option value="0">{{ trans("problem_category.service_ticket_fields.disabled_d") }}</option>
                            </select>
                        </div>
                        @if(in_array(config('app.client'), ["grdemo", "ril","rolepermission"]))
                            <div class="col-md-3">
                                <label class="form-label b5-text" for="filter_by_role">{{ trans("problem_category.service_ticket_fields.filter_by_roles") }}</label>
                                <select id="filter_by_role" name="filter_by_role" class="form-select filter-input"></select>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="amg-form-footer modal-footer justify-content-end mb-4 py-0">
                    <div class="amg-btn-group mt-3 gap-3 px-4">
                        <button type="button" id="btnClrFilter" class="amg-btn amg-btn-secondary amg-btn-md ms-2 btn-clear-filter" style="min-width: 200px;">
                            {{ trans("problem_category.service_ticket_fields.Clear") }}
                        </button>
                        <button type="button" class="amg-btn amg-btn-primary text-white btn-filter col-md-4 fw-light" style="min-width: 200px;">
                            {{ trans("problem_category.service_ticket_fields.Filter") }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
