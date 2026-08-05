<div id="filterMdl" class="amg-modal amg-form-modal modal fade" data-bs-backdrop="static"
    data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1200px;">
        <div class="modal-content rounded-5">
            <div class="modal-header d-flex align-items-center py-3 pt-4">
                <h3 class="modal-title px-4">Filter</h3>
                <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                    <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none" >
                        <path
                            d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                            fill="currentColor" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_manufacturer") }}</label>
						</div>
						<select id="filter_by_manufacture" name="filter_by_manufacture"></select>
					</div>
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_category") }}</label>
						</div>
						<select id="filter_by_category" name="filter_by_category"></select>
					</div>
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_model") }}</label>
						</div>
						<select id="filter_by_model" name="filter_by_model"></select>
					</div>
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_processor") }}</label>
						</div>
						<select id="filter_by_processor_name" name="filter_by_processor_name" autocomplete="off" class="form-control"></select>
					</div>
				</div>
				<div class="row" style="margin-top: 6px">
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_oS") }}</label>
						</div>
						<select name="filter_by_os" id="filter_by_os" autocomplete="off" class="form-control"></select>
					</div>
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_platform") }}</label>
						</div>
						<select name="filter_by_platform" id="filter_by_platform" autocomplete="off" class="form-control" placeholder="{{ trans('content.network_inv_fields.Select_Category') }}">
							<option value="null">{{ trans("content.network_inv_fields.No_Filter") }}</option>
							<option value="Windows">{{ trans("content.network_inv_fields.Windows") }}</option>
							<option value="Linux/Unix">Linux</option>
							<option value="iOS">iOS</option>
						</select>
					</div>
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_system_type") }}</label>
						</div>
						<select id="filter_by_computer_system_type" name="filter_by_computer_system_type"></select>
					</div>
					<div class="col-lg-3">
						<label>{{ trans('content.filter_heading.filter_by_license_activated') }} </label>
						<select name="filter_by_genuine_status" id="filter_by_genuine_status" autocomplete="off" class="form-control" >
							<option value="null">{{ trans("content.network_inv_fields.No_Filter") }}</option>
							<option value="1">Active</option>
							<option value="2">Inactive</option>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 6px">
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.hardisk") }}</label>
                        <div class="input-group">
							<input type="text" name="filter_by_hardisk" id="filter_by_hardisk" placeholder="{{ trans('content.network_inv_fields.Enter_Size') }}" class="form-control" autocomplete="off">
                            <span class="input-group-text bg-white border-start-1" id="togglePassword" style="cursor:pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path fill="currentColor" d="M15 15h4v-2h-4zm0-4h4V9h-4zM5 17q-.825 0-1.412-.587T3 15V9q0-.825.588-1.412T5 7h4q.825 0 1.413.588T11 9H5v6h4v-2H7v-2h4v4q0 .825-.587 1.413T9 17zm8 0V7h6q.825 0 1.413.588T21 9v1.5q0 .625-.437 1.063T19.5 12q.625 0 1.063.438T21 13.5V15q0 .825-.587 1.413T19 17z" />
                                </svg>
                            </span>
                        </div>
					</div>
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.hardisk_condition") }}</label>
						<select name="filter_by_diskcondn" id="filter_by_diskcondn" autocomplete="off" class="form-control">
							<option value="null">{{ trans("content.network_inv_fields.No_Filter") }}</option>
							<option value="1">{{ trans("content.network_inv_fields.Above") }}</option>
							<option value="2">{{ trans("content.network_inv_fields.Equal_To") }}</option>
							<option value="3">{{ trans("content.network_inv_fields.Less_than") }}</option>
						</select>
					</div>
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.ram") }}</label>
                         <div class="input-group mar-btm">
                            <input type="text" name="filter_by_ram" id="filter_by_ram" placeholder="{{ trans('content.network_inv_fields.Enter_Size') }}" class="form-control" autocomplete="off">
                            <span class="input-group-text bg-white border-start-1" id="togglePassword" style="cursor:pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path fill="currentColor" d="M15 15h4v-2h-4zm0-4h4V9h-4zM5 17q-.825 0-1.412-.587T3 15V9q0-.825.588-1.412T5 7h4q.825 0 1.413.588T11 9H5v6h4v-2H7v-2h4v4q0 .825-.587 1.413T9 17zm8 0V7h6q.825 0 1.413.588T21 9v1.5q0 .625-.437 1.063T19.5 12q.625 0 1.063.438T21 13.5V15q0 .825-.587 1.413T19 17z" />
                                </svg>
                            </span>
                        </div>
					</div>
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.ram_condition") }}</label>
						<select name="filter_by_ramcondn" id="filter_by_ramcondn" autocomplete="off" class="form-control">
							<option value="null">{{ trans("content.network_inv_fields.No_Filter") }}</option>
							<option value="1">{{ trans("content.network_inv_fields.Above") }}</option>
							<option value="2">{{ trans("content.network_inv_fields.Equal_To") }}</option>
							<option value="3">{{ trans("content.network_inv_fields.Less_than") }}</option>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 6px">
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.filter_by_license_status") }}</label>
						<select name="filter_by_license_status" id="filter_by_license_status" autocomplete="off" class="form-control" >
							<option value="null">{{ trans("content.network_inv_fields.No_Filter") }}</option>
							<option value="0">Unlicensed</option>
							<option value="1">Licensed</option>
							<option value="2">OOBGrace</option>
							<option value="3">OOTGrace</option>
							<option value="4">NonGenuineGrace</option>
							<option value="5">Notification</option>
							<option value="6">ExtendedGrace</option>
						</select>
					</div>
					<div class="col-lg-3">
						<div>
							<label>{{ trans("content.filter_heading.filter_by_version") }}</label>
						</div>
						<select name="filter_by_version" id="filter_by_version" autocomplete="off" class="form-control"></select>
					</div>
					<div class="col-lg-2 hide">
						<div><label>{{ trans("content.filter_heading.filter_by_programs") }}</label></div>
						<select name="filter_Caption" id="filter_Caption" autocomplete="off" class="form-control"></select>
					</div>
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.filter_by_location") }}</label>
						<select id="filter_by_location" name="filter_by_location"></select>
					</div>
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.filter_by_internal_place") }}</label>
						<select name="filter_by_internal_place" id="filter_by_internal_place" autocomplete="off" class="form-control"
								placeholder="Select Internal Place" multiple="multiple"></select>
					</div>
				</div>
				<div class="row" style="margin-top: 6px">
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.filter_by_date") }}</label>
						<select name="filter_by_date" id="filter_by_date" autocomplete="off" class="form-control">
							<option value="">{{ trans("content.service_ticket_fields.No_Filter") }}</option>
							<option value="1">Created At</option>
							<option value="2">Updated At</option>
						</select>
					</div>
					<div class="col-lg-6">
                            <label for="filter_by_daterange">{{ trans("ticket.tkt_filters.filter_by_daterange") }}</label>
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
					<div class="col-lg-3">
						<label>{{ trans("content.filter_heading.filter_by_ad_agent_non_ad_agent")}}</label>
						<select name="filter_by_ad_nonad_agent" id="filter_by_ad_nonad_agent" autocomplete="off" class="form-control" >
							<option value="null">{{ trans("content.network_inv_fields.No_Filter") }}</option>
							<option value="1">{{ trans("content.network_inv_fields.ad_agent") }}</option>
							<option value="0">{{ trans("content.network_inv_fields.non_ad_agent") }}</option>
						</select>
					</div>
				</div>
            </div>
            <div class="modal-footer justify-content-end mb-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button type="button" class="amg-btn amg-btn-ghost amg-dark-secondary-button bg-black text-white hover-opacity-80 btn-clear-filter fw-light min-w-120">
                        {{ trans('user.user_filter.clear') }}
                    </button>
                    <button type="button" class="amg-btn amg-btn-primary btn-filter col-md-4 fw-light text-white min-w-250">
                        {{ trans('user.user_filter.apply_filter') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>