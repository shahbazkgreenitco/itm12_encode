{{--
/**
* ------------------------------------------------------------
* File: change-info.blade.php
* Module: Devices
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-014
* Created On: 2026-14-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="title" class="modal-title" style="text-align: left; margin: 0; display: inline-block;">{{ trans('devices.device_info.modal.device_history') }}</h4>
            <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                <svg style="height: 27px; width: 27px; min-width: 27px; flex-shrink: 0;" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor" />
                </svg>
            </button>
        </div>
        <div class="modal-body table-responsive">
            <table id="device-history-table" class="table table-bordered history-table-bordered">
                <thead>
                    <tr>
                        <th class="table-border">{{ trans('devices.device_info.modal.device_info') }}</th>
                        <th class="table-border">{{ trans('devices.device_info.modal.old_device_info') }}</th>
                        <th class="table-border">{{ trans('devices.device_info.modal.updated_device_info') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($interactCacheObj->interactedRecord->asset_tag != $interactCacheObj->asset_tag)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_tag") }}</td>
                            <td class="table-border">{{ $interactCacheObj->asset_tag }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->asset_tag }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->model_id != $interactCacheObj->model_old_id)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_model") }}</td>
                            <td class="table-border">{{ $interactCacheObj->model_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->model_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->man_id != $interactCacheObj->man_old_id)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_manufacturer") }}</td>
                            <td class="table-border">{{ $interactCacheObj->man_old_name }}</td>
                            <td class="table-border">{{ $interactCacheObj->man_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->cat_id != $interactCacheObj->cat_old_id)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_category") }}</td>
                            <td class="table-border">{{ $interactCacheObj->cat_old_name }}</td>
                            <td class="table-border">{{ $interactCacheObj->cat_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->company_name != $interactCacheObj->company_name_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.company") }}</td>
                            <td class="table-border">{{ $interactCacheObj->company_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->company_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->location_name != $interactCacheObj->location_name_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.location") }}</td>
                            <td class="table-border">{{ $interactCacheObj->location_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->location_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->place_name != $interactCacheObj->place_old_name)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.internal_place") }}</td>
                            <td class="table-border">{{ $interactCacheObj->place_old_name }}</td>
                            <td class="table-border">{{ $interactCacheObj->place_name}}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->status_name != $interactCacheObj->status_name_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_status") }}</td>
                            <td class="table-border">{{ $interactCacheObj->status_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->status_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->serial != $interactCacheObj->serial)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.serialcode") }}</td>
                            <td class="table-border">{{ $interactCacheObj->serial }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->serial }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->product_number != $interactCacheObj->product_number)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.product_number") }}</td>
                            <td class="table-border">{{ $interactCacheObj->product_number }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->product_number }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->uuid != $interactCacheObj->uuid)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_uuid") }}</td>
                            <td class="table-border">{{ $interactCacheObj->uuid }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->uuid }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->name != $interactCacheObj->name)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_name") }}</td>
                            <td class="table-border">{{ $interactCacheObj->name }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->assets_types_name != $interactCacheObj->assets_types_name_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.asset_type") }}</td>
                            <td class="table-border">{{ $interactCacheObj->assets_types_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->assets_types_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->device_occure_type_new != $interactCacheObj->device_occure_type_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_from") }}</td>
                            <td class="table-border">{{ $interactCacheObj->device_occure_type_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->device_occure_type_new }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->asset_owner_new != $interactCacheObj->asset_owner_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.asset_owner") }}</td>
                            <td class="table-border">{{ $interactCacheObj->asset_owner_old_name }}</td>
                            <td class="table-border">{{ $interactCacheObj->asset_owner_new_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->supplier_name != $interactCacheObj->supplier_name_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.supplier") }}</td>
                            <td class="table-border">{{ $interactCacheObj->supplier_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->supplier_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->purchase_name != $interactCacheObj->purchase_name_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.purchase_reference") }}</td>
                            <td class="table-border">{{ $interactCacheObj->purchase_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->purchase_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->purchase_date != $interactCacheObj->purchase_date)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.purchase_date") }}</td>
                            <td class="table-border">{{ $interactCacheObj->purchase_date }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->purchase_date }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->purchase_cost != $interactCacheObj->purchase_cost)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.purchase_cost") }}</td>
                            <td class="table-border">{{ $interactCacheObj->purchase_cost }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->purchase_cost }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->order_number != $interactCacheObj->order_number)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.order_number") }}</td>
                            <td class="table-border">{{ $interactCacheObj->order_number }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->order_number }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->warranty_months != $interactCacheObj->warranty_months)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.warranty") }}</td>
                            <td class="table-border">{{ $interactCacheObj->warranty_months }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->warranty_months }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->amc_supplier_name != $interactCacheObj->amc_supplier_name_old)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.amc_supplier") }}</td>
                            <td class="table-border">{{ $interactCacheObj->amc_supplier_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->amc_supplier_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->ip != $interactCacheObj->old_ip)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_ip") }}</td>
                            <td class="table-border">{{ $interactCacheObj->old_ip }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->ip }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->mac != $interactCacheObj->old_mac)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.device_mac") }}</td>
                            <td class="table-border">{{ $interactCacheObj->old_mac }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->mac }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->warranty_start_date != $interactCacheObj->warranty_start_date)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.warranty_start_date") }}</td>
                            <td class="table-border">{{ $interactCacheObj->warranty_start_date }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->warranty_start_date }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->warrenty_end_date != $interactCacheObj->warrenty_end_date)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.warranty_end_date") }}</td>
                            <td class="table-border">{{ $interactCacheObj->warrenty_end_date }}</td>
                            <td class="table-border">{{ $interactCacheObj->interactedRecord->warrenty_end_date }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->interactedRecord->department_id != $interactCacheObj->department_id)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.department") }}</td>
                            <td class="table-border">{{ $interactCacheObj->department_name_old }}</td>
                            <td class="table-border">{{ $interactCacheObj->department_name }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->notes != $interactRecord->notes)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.notes") }}</td>
                            <td class="table-border">{{ $interactCacheObj->notes }}</td>
                            <td class="table-border">{{ $interactRecord->notes }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->stock_place != $interactRecord->stock_place)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.stock_place") }}</td>
                            <td class="table-border">{{ $interactCacheObj->oldstockplace }}</td>
                            <td class="table-border">{{ $interactCacheObj->newstockplace }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->high_pririty != $interactRecord->high_pririty)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.high_priority_device") }}</td>
                            <td class="table-border">{{ $interactCacheObj->high_pririty == 1 ? 'Yes':'No' }}</td>
                            <td class="table-border">{{ $interactRecord->high_pririty == 1 ? 'Yes': 'No'}}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->requestable != $interactRecord->requestable )
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.requestable_device") }}</td>
                            <td class="table-border">{{ $interactCacheObj->requestable == 1 ? 'Yes' : 'No' }}</td>
                            <td class="table-border">{{ $interactRecord->requestable == 1 ? 'Yes':'No' }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->sez_device != $interactRecord->sez_device )
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.sez_device") }}</td>
                            <td class="table-border">{{ $interactCacheObj->sez_device == 1 ? 'Yes' : 'No' }}</td>
                            <td class="table-border">{{ $interactRecord->sez_device == 1 ? 'Yes':'No' }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->purchase_currency != $interactRecord->purchase_currency)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.purchase_currency_format") }}</td>
                            <td class="table-border">{{ $interactCacheObj->purchase_currency }}</td>
                            <td class="table-border">{{ $interactRecord->purchase_currency }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->order_number != $interactRecord->order_number)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.order_number") }}</td>
                            <td class="table-border">{{ $interactCacheObj->order_number }}</td>
                            <td class="table-border">{{ $interactRecord->order_number }}</td>
                        </tr>
                    @endif
                    @if($interactCacheObj->amc_expire_date != $interactRecord->amc_expire_date)
                        <tr class="table-border">
                            <td class="table-border">{{ trans("devices.device_info.modal.amc_expire_date") }}</td>
                            <td class="table-border">{{ $interactCacheObj->amc_expire_date }}</td>
                            <td class="table-border">{{ $interactRecord->amc_expire_date }}</td>
                        </tr>
                    @endif
                    @if (!empty($customFieldsCache))
                        @foreach($customFieldsCache as $key => $cacheValue)
                            @php
                                $recordValue = $customFieldsRecord[$key] ?? null;
                            @endphp
                            @if($cacheValue != $recordValue)
                                <tr class="table-border">
                                    <td class="table-border">{{ ucwords(str_replace(['_itm_', '_'], ' ',$key)) }}</td>
                                    <td class="table-border">{{ $cacheValue }}</td>
                                    <td class="table-border">{{ $recordValue }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>