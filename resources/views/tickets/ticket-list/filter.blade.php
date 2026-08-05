{{-- @page-meta
{
    "page_no": "TL05F-26",
    "file": "filter.blade.php",
    "versions": [
        {
            "version": "1.0",
            "writer": "Shivam Kumar",
            "from": "2026-05",
            "reviewer": null,
            "description": "Initial setup"
        }
    ]
}
--}}
<div class="amg-modal modal fade" id="ticketFilterModal" tabindex="-1"
    aria-labelledby="ticketFilterModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-5">
            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title px-4">{{ trans('ticket.tkt_filters.ticket_filter') }}</h3>
                <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                    <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none">
                        <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="#7F7F7F" />
                        <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="#7F7F7F" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <!-- Row 1 -->
                <div class="row g-3 px-3">
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label b5-text" for="filter_by_status">{{ trans('ticket.tkt_filters.filter_by_status') }}</label>
                        <select id="filter_by_status" name="filter_by_status[]" class="form-select filter-input" multiple></select>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label b5-text" for="filter_by_priority">{{ trans('ticket.tkt_filters.filter_by_priority') }}</label>
                        <select id="filter_by_priority" name="filter_by_priority[]" class="form-select filter-input" multiple></select>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label b5-text" for="filter_by_department">{{ trans("ticket.tkt_filters.filter_by_department") }}</label>
                        <select id="filter_by_department" name="filter_by_department" multiple autocomplete="off" class="form-select filter-input"> </select>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label b5-text" for="filter_by_problem_category">{{ trans("ticket.tkt_filters.filter_by_problem_category") }}</label>
                        <select id="filter_by_problem_category" name="filter_by_problem_category[]" class="form-select filter-input" multiple></select>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label b5-text" for="filter_by_sub_category">{{ trans("ticket.tkt_filters.filter_by_sub_category") }}</label>
                        <select id="filter_by_sub_category" name="filter_by_sub_category[]" class="form-select filter-input" multiple></select>
                    </div>
                    @if(Auth::user()->hasPermission("service_tickets") || Auth::user()->hasRole("User"))
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label b5-text" for="filter_by_ticket_handlers">{{ trans("ticket.tkt_filters.by_ticket_handler") }}</label>
                            <select id="filter_by_ticket_handlers" name="filter_by_ticket_handlers[]" class="form-select filter-input" multiple></select>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label b5-text" for="filter_by_tag">{{ trans("ticket.tkt_filters.filter_by_tag") }}</label>
                            <select id="filter_by_tag" name="filter_by_tag[]" class="form-select filter-input"  multiple></select>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label b5-text" for="filter_created_via">{{ trans("ticket.tkt_filters.filter_created_via") }}</label>
                            <select id="filter_created_via" name="filter_created_via" class="form-select filter-input"></select>
                        </div>
                        @if (Auth::user()->hasPermission("service_tickets"))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_location_id">{{ trans("ticket.tkt_filters.filter_by_location") }}</label>
                                <select id="filter_by_location_id" name="filter_by_location_id[]" multiple class="form-select filter-input"></select>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_base_location_id">{{ trans("ticket.tkt_filters.filter_by_base_location") }}</label>
                                <select id="filter_base_location_id" name="filter_base_location_id[]" class="form-select filter-input"></select>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_based_on_cre_log">{{ trans("ticket.tkt_filters.filter_by_creator_logger") }}</label>
                                <select id="filter_based_on_cre_log" name="filter_based_on_cre_log" class="form-select filter-input">
                                    <option value='null'>{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                    <option value=1>{{ trans("ticket.tkt_filters.logger") }}</option>
                                    <option value=2>{{ trans("ticket.tkt_filters.creator") }}</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 creatorcover">
                                <label class="form-label b5-text" for="filter_by_ticket_creator">{{ trans("ticket.tkt_filters.select_creator_logger") }}</label>
                                <select id="filter_by_ticket_creator" name="filter_by_ticket_creator" class="form-select filter-input">
                                    <option value="null">{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_ticket_or_SR">{{ trans("ticket.tkt_filters.filter_by_ticket_or_service_request") }}</label>
                                <select id="filter_by_ticket_or_SR" name="filter_by_ticket_or_SR" class="form-select filter-input">
                                    <option value="null">{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                    <option value="1">{{ trans("ticket.tkt_filters.ticket") }}</option>
                                    <option value="2">{{ trans("ticket.tkt_filters.it_service_request") }}</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_vip_tickets">{{ trans("ticket.tkt_filters.filter_by_vip_tickets") }}</label>
                                <select id="filter_by_vip_tickets" name="filter_by_vip_tickets" class="form-select filter-input">
                                    <option value="null">{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_sla_breached">{{ trans("ticket.tkt_filters.filter_by_sla_breached") }}</label>
                                <select id="filter_by_sla_breached" name="filter_by_sla_breached" class="form-select filter-input">
                                    <option value="null">{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label b5-text" for="filter_by_feedback">{{ trans("ticket.tkt_filters.filter_by_feedback") }}</label>
                            <select id="filter_by_feedback" name="filter_by_feedback[]" multiple class="form-select filter-input">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        @if(config("services.task_module.enabled"))
                            @can('TaskRead')
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <label class="form-label b5-text" for="filter_task">{{ trans("ticket.tkt_filters.filter_by_task") }}</label>
                                    <select id="filter_task" name="filter_task" class="form-select filter-input"></select>
                                </div>
                            @endcan
                        @endif
                        @if(Auth::user()->hasPermission("service_tickets"))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_ticket_type">{{ trans("ticket.tkt_filters.filter_by_ticket_type") }}</label>
                                <select id="filter_by_ticket_type" name="filter_by_ticket_type[]" multiple class="form-select filter-input">
                                    <option value="0">Normal</option>
                                    @foreach($ticketTypes as $ticketType)
                                        <option value="{{ $ticketType->id }}">{{ $ticketType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_custom_field">{{ trans("ticket.tkt_filters.filter_by_custom_field") }}</label>
                                <select id="filter_by_custom_field" name="filter_by_custom_field" class="form-select filter-input"></select>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_custom_field_value">{{ trans("ticket.tkt_filters.filter_by_custom_field_value") }}</label>
                                <select id="filter_by_custom_field_value" name="filter_by_custom_field_value" class="form-select filter-input"> </select>
                            </div>
                        @endif
                        @if(Auth::user()->hasPermission("service_tickets"))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_by_merge">{{ trans("ticket.tkt_filters.filter_by_merge") }}</label>
                                <select id="filter_by_merge" name="filter_by_merge" class="form-select filter-input">
                                    <option value="null">{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                    <option value="1">{{ trans("ticket.tkt_filters.primary") }}</option>
                                    <option value="2">{{ trans("ticket.tkt_filters.secondary") }}</option>
                                </select>
                            </div>
                        
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <label class="form-label b5-text" for="filter_based_star">{{ trans("ticket.tkt_filters.filter_based_star") }}</label>
                                <select id="filter_based_star" name="filter_based_star[]" multiple class="form-select filter-input">
                                    <option value="null">{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                    <option value="1">Starred</option>
                                </select>
                            </div>
                        @endif
                   
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label b5-text" for="filter_by_date">{{ trans('ticket.tkt_filters.filter_by_date') }}</label>
                            <select id="filter_by_date" name="filter_by_date" class="form-select filter-input">
                                <option value="null">{{ trans("ticket.tkt_filters.no_filter") }}</option>
                                <option value="1">{{ trans("ticket.tkt_filters.created_date") }}</option>
                                <option value="3">{{ trans("ticket.tkt_filters.resolved_date") }}</option>
                                <option value="4">{{ trans("ticket.tkt_filters.updated_date") }}</option>
                                <option value="5">{{ trans("ticket.tkt_filters.closed_date") }}</option>    
                            </select>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <label class="form-label b5-text" for="filter_by_daterange">{{ trans("ticket.tkt_filters.filter_by_daterange") }}</label>
                            <div id="reportrange" class="form-control d-flex align-items-center justify-content-between"
                                style="cursor:pointer;">
                                <span></span>
                                <svg width="15px" height="15px" viewBox="0 0 48 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <title>{{ trans("content.filter_heading.filter_by_daterange") }}</title>
                                    <g id="Layer_2" data-name="Layer 2">
                                        <g id="invisible_box" data-name="invisible box">
                                            <rect width="48" height="48" fill="none" />
                                        </g>
                                        <g id="icons_Q2" data-name="icons Q2">
                                            <path
                                                d="M44,8H35V4.1A2.1,2.1,0,0,0,33.3,2,2,2,0,0,0,31,4V8H17V4.1A2.1,2.1,0,0,0,15.3,2,2,2,0,0,0,13,4V8H4a2,2,0,0,0-2,2V42a2,2,0,0,0,2,2H44a2,2,0,0,0,2-2V10A2,2,0,0,0,44,8ZM42,40H6V20H42Zm0-24H6V12H42Z" />
                                            <rect x="8" y="24" width="8" height="8" rx="2" ry="2" />
                                            <rect x="32" y="24" width="8" height="8" rx="2" ry="2" />
                                            <rect x="20" y="24" width="8" height="8" rx="2" ry="2" />
                                        </g>
                                    </g>
                                </svg>
                                <input type="hidden" name="daterange" id="daterange">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer justify-content-end py-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button type="button" id="btnFilter" class="amg-btn amg-btn-primary s2-text btn-filter" style="min-width: 350px;">
                        {{ trans("ticket.tkt_filters.filter") }}
                    </button>
                    <button type="button" id="btnClrFilter"
                        class="amg-btn amg-btn-secondary s2-text hover-opacity-80 btn-clear-filter"
                        style="min-width: 120px;">
                        {{ trans("ticket.tkt_filters.clear") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
