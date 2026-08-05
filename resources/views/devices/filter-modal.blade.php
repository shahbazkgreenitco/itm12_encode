{{-- Device Filter Modal --}}
<div class="amg-modal modal fade" id="deviceFilterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width:1200px;">
        <div class="modal-content rounded-5 bg-white">

            {{-- Header --}}
            <div class="modal-header py-3 pt-4">
                <h3 class="modal-title px-4">
                    {{ trans("devices.filters_fields.filter_devices") }}
                </h3>
                <button data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                    <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                        <path
                            d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                            fill="currentColor" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                {{-- Row 1 --}}
                <div class="row g-3 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.manufacturer') }}</label>
                        <select id="filter_by_manufacturer" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.model') }}
                        </label>
                        <select id="filter_by_model" class="form-select">
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.location') }}</label>
                        <select id="filter_by_location" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.category') }}</label>
                        <select id="filter_by_category" class="form-select"></select>
                    </div>
                </div>

                {{-- Row 2 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label"> {{ trans('devices.filters_fields.assigned_user') }}</label>
                        <select id="f_assigned_to" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"> {{ trans('devices.filters_fields.department') }}</label>
                        <select id="filter_by_dept" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"> {{ trans('devices.filters_fields.assigned_place') }}</label>
                        <select id="assigned_place" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"> {{ trans('devices.filters_fields.stock_place') }}</label>
                        <select id="f_stock_place" class="form-select"></select>
                    </div>
                    {{-- <div class="col-md-3">
                        <label class="form-label"> {{ trans('devices.filters_fields.device_type') }}</label>
                        <select id="asset_type_id" class="form-select"></select>
                    </div> --}}
                </div>

                {{-- Row 3 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.device_from') }}</label>
                        <select id="f_device_occure_type" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="0">{{ trans('devices.filters_fields.purchase_device') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.project_device') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.rental') }}</option>
                            <option value="3">{{ trans('devices.filters_fields.customer_owned') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.project') }}</label>
                        <select id="last_checkout_project" class="form-select"></select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.asset_owner') }}</label>
                        <select id="filter_asset_owner" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.assigned_to') }}</label>
                        <select id="filter_by_assigned_to" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.user') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.place') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Row 4 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.added_from') }}</label>
                        <select id="filter_by_added_from" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.manually_added') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.via_network') }}</option>
                            <option value="3">{{ trans('devices.filters_fields.via_azure') }}</option>
                            <option value="4">{{ trans('devices.filters_fields.via_ns') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.warranty_status') }}</label>
                        <select id="filter_by_warranty_status" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.not_expired') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.expired') }}</option>
                            <option value="3">{{ trans('devices.filters_fields.not_applicable') }}</option>
                            <option value="4">{{ trans('devices.filters_fields.warranty_end_date_oem_date_not_matching')
                                }}</option>
                            <option value="5">{{ trans('devices.filters_fields.purchase_date_oem_date_not_matching') }}
                            </option>
                            <option value="6">{{ trans('devices.filters_fields.invoice_date_oem_date_not_matching') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.allocation_type') }}</label>
                        <select id="filter_by_allocation_type" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.audit_confirmation') }}</label>
                        <select id="filter_by_audit_confirmation" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="accepted">{{ trans('devices.filters_fields.accepted') }}</option>
                            <option value="pending">{{ trans('devices.filters_fields.pending') }}</option>
                            <option value="declined">{{ trans('devices.filters_fields.rejected') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Row 5 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.asset_tag') }}</label>
                        <select id="filter_by_asset_tag" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.user_location') }}</label>
                        <select id="filter_by_user_location" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.user_base_location') }}</label>
                        <select id="filter_by_user_base_location" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.internal_place') }}</label>
                        <select id="filter_by_internal_place" class="form-select"></select>
                    </div>
                </div>


                {{-- Row 7 - Additional Filters --}}
                <div class="row g-3 mt-2 px-4">

                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.user_internal_place') }}</label>
                        <select id="filter_by_user_internalplace" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.mapped_location') }}</label>
                        <select id="filter_by_mapped_location" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.purchase_reference') }}</label>
                        <select id="filter_by_purchase_reference" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.detected_from') }}</label>
                        <select id="filter_by_detected_from" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.manually_added') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.via_network') }}</option>
                            <option value="3">{{ trans('devices.filters_fields.via_azure') }}</option>
                            <option value="4">{{ trans('devices.filters_fields.rdp') }}</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-3">
                        <label class="form-label">Custom Field</label>
                        <select id="filter_by_custom_field" class="form-select"></select>
                    </div> --}}
                </div>

                {{-- Row 8 --}}
                <div class="row g-3 mt-2 px-4">
                    {{-- <div class="col-md-3">
                        <label class="form-label">Custom Field Value</label>
                        <select id="filter_by_custom_field_value" class="form-select"></select>
                    </div> --}}

                    {{-- <div class="col-md-3">
                        <label class="form-label">Device Department</label>
                        <select id="filter_by_asset_department" class="form-select"></select>
                    </div> --}}
                </div>

                {{-- Row 9 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.sez_device') }}</label>
                        <select id="filter_by_sez_device" class="form-select">
                            <option value="">No Filter</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.high_priority_device') }}</label>
                        <select id="filter_by_high_priority_device" class="form-select">
                            <option value="">No Filter</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.under_transfer') }}</label>
                        <select id="filter_by_under_transfer_device" class="form-select">
                            <option value="">No Filter</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.requestable_device') }}</label>
                        <select id="filter_by_request_able_device" class="form-select">
                            <option value="">No Filter</option>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>

                {{-- Row 10 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.rdp_status') }}</label>
                        <select id="filter_by_rdp_status" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="0">{{ trans('devices.filters_fields.disabled') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.enabled') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.device_size') }}</label>
                        <input type="number" id="filter_by_device_size" class="form-control"
                            placeholder="{{ trans('devices.filters_fields.enter_size') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.condition') }}</label>
                        <select id="filter_by_condition_by_user_assign" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.above') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.less_than') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.device_with_rfid') }}</label>
                        <select id="filter_by_device_with_rfid" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.checkout') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.checkin') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Row 11 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.reason_type') }}</label>
                        <select id="filter_by_reason_type" class="form-select">
                            <option value="">No Filter</option>
                            <option value="1">Checkout</option>
                            <option value="2">Checkin</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.reason') }}</label>
                        <select id="filter_by_reason" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.rfid_tag') }}</label>
                        <select id="filter_by_rfid" class="form-select"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.asset_department') }}</label>
                        <select id="filter_by_department" class="form-select"></select>
                    </div>
                </div>

                {{-- Row 6 --}}
                <div class="row g-3 mt-2 px-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('devices.filters_fields.date_type') }}</label>
                        <select id="filter_by_date" class="form-select">
                            <option value="">{{ trans('devices.filters_fields.no_filter') }}</option>
                            <option value="1">{{ trans('devices.filters_fields.purchase_date') }}</option>
                            <option value="2">{{ trans('devices.filters_fields.checked_out_date') }}</option>
                            <option value="3">{{ trans('devices.filters_fields.updated_at') }}</option>
                            <option value="4">{{ trans('devices.filters_fields.warranty_expiry') }}</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">{{ trans('devices.filters_fields.date_range') }}</label>
                        <div id="deviceReportrange"
                            style="background:#fff;cursor:pointer;padding:7px 10px;border:1px solid #ccc;border-radius:6px;">
                            <i class="fa fa-calendar"></i>
                            <span>>{{ trans('devices.filters_fields.select_date_range') }}</span>
                        </div>
                        <input type="hidden" id="daterange">
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer justify-content-start mb-4 pb-4 py-0">
                <div class="amg-btn-group mt-3 gap-3 px-4">
                    <button type="button" id="filter" class="amg-btn amg-btn-primary filter" style="min-width:300px;">
                        {{ trans("devices.filters_fields.apply_filter") }}
                    </button>
                    <button type="button" id="clear" class="amg-btn amg-btn-ghost bg-black text-white clear"
                        style="min-width:300px;">
                        {{ trans("devices.filters_fields.clear") }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>