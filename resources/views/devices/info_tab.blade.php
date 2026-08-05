{{--
/**
* ------------------------------------------------------------
* File: info_tab.blade.php
* Module: Device
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-016
* Created On: 2026-14-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div class="rounded-4 overflow-hidden">
    <table class="table dataTable mb-0 border-0" id="main-user-list-wrapper">
        <tbody>
            {{-- using this tr d-none in this case since the background color for tr is reversed for this table --}}
            <tr class="d-none"></tr>
            <tr>
                <td class="info-title-text w-25">{{ trans("devices.device_info.view.device_tag") }}</td>
                <td class="b3-text fw-semibold">{{ $device->asset_tag }}</td>
            </tr>
            <tr>
                <td class="info-title-text">{{ trans("devices.device_info.view.department") }}</td>
                <td class="b3-text fw-semibold">
                    @if($device->department_id && isset($device->assetDepartment->name)) 
                        {{ $device->assetDepartment->name }} 
                    @endif
                </td>
            </tr>
            <tr>
                <td class="info-title-text">{{ trans("devices.device_info.view.device_name") }}</td>
                <td class="b3-text fw-semibold">{{ $device->name }}</td>
            </tr>
            <tr>
                <td class="info-title-text">{{ trans("devices.device_info.view.current_status") }}</td>
                <td>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="role-badge role-badge-user">{{ $device->status->name }}</span>
                        @can("DeviceCheckoutCheckin")
                            @if(!$device->isCheckedOut() && $device->canCheckout())
                                <div class="check-at-info">
                                    @if($transfer_items)
                                        @foreach($transfer_items['transfer_item'] as $ti)
                                            <button class="amg-btn amg-btn-primary amg-btn-block border-0 text-white dtActCheckOut" data-id="{{ $device->id }}" data-asset_tag="{{ $device->asset_tag }}" data-name="{{ $device->name }}" data-company_id="{{ $device->company_id }}" data-cmp_name="{{ $device->company->name }}" @if($ti['transfer_status'] == 1) disabled data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.device_under_transfer') }}" data-original-title="{{ trans('devices.device_info.view.device_under_transfer') }}" @endif>
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M-4.37114e-07 10C-1.9571e-07 15.5227 4.47733 20 10 20C15.5227 20 20 15.5227 20 10C20 4.47733 15.5227 -6.78517e-07 10 -4.37114e-07C4.47733 -1.9571e-07 -6.78517e-07 4.47733 -4.37114e-07 10ZM15.6667 10L10.565 15.3333L10.565 11.7143L4.33333 11.7143L4.33333 8.286L10.565 8.286L10.565 4.66667L15.6667 10Z" fill="white"/>
                                                </svg>
                                                {{ trans("devices.device_info.view.check_out") }}
                                            </button>
                                        @endforeach
                                    @else
                                        <button type="button" class="amg-btn amg-btn-primary amg-btn-block border-0 dtActCheckOut" data-id="{{ $device->id }}" data-asset_tag="{{ $device->asset_tag }}" data-name="{{ $device->name }}" data-company_id="{{ $device->company_id }}" data-cmp_name="{{ $device->company->name }}">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M-4.37114e-07 10C-1.9571e-07 15.5227 4.47733 20 10 20C15.5227 20 20 15.5227 20 10C20 4.47733 15.5227 -6.78517e-07 10 -4.37114e-07C4.47733 -1.9571e-07 -6.78517e-07 4.47733 -4.37114e-07 10ZM15.6667 10L10.565 15.3333L10.565 11.7143L4.33333 11.7143L4.33333 8.286L10.565 8.286L10.565 4.66667L15.6667 10Z" fill="white"/>
                                            </svg>
                                            {{ trans("devices.device_info.view.check_out") }}
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @endcan
                    </div>
                </td>
            </tr>
            {{-- <tr>
                <td class="info-title-text align-top pt-3">Check Out To</td>
                <td>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <img src="https://ui-avatars.com/api/?name=Aditya+Bhave&background=random" class="rounded-circle me-2" width="28" height="28" alt="Avatar">
                                <span class="b3-text fw-semibold">Aditya Bhave</span>
                            </div>
                            <div class="info-title-text mb-1">Greenitco Technologies</div>
                            <div class="mb-1">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M-3.49691e-07 8C-1.56568e-07 12.4181 3.58187 16 8 16C12.4181 16 16 12.4181 16 8C16 3.58187 12.4181 4.1086e-07 8 6.03983e-07C3.58187 7.97106e-07 -5.42814e-07 3.58187 -3.49691e-07 8ZM12.5333 8L8.452 12.2667L8.452 9.37147L3.46667 9.37147L3.46667 6.6288L8.452 6.6288L8.452 3.73333L12.5333 8Z" fill="#186B43"/>
                                </svg>
                                <span class="checkout-text">Checkout to User</span>
                            </div>
                            <div>Allocation Type: Business Travel</div>
                        </div>
                        <div>
                            <button class="amg-btn amg-btn-primary amg-btn-block border-0">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M-4.37114e-07 10C-1.9571e-07 15.5227 4.47733 20 10 20C15.5227 20 20 15.5227 20 10C20 4.47733 15.5227 -6.78517e-07 10 -4.37114e-07C4.47733 -1.9571e-07 -6.78517e-07 4.47733 -4.37114e-07 10ZM15.6667 10L10.565 15.3333L10.565 11.7143L4.33333 11.7143L4.33333 8.286L10.565 8.286L10.565 4.66667L15.6667 10Z" fill="white"/>
                                </svg>
                                Check In
                            </button>
                        </div>
                    </div>
                </td>
            </tr> --}}
            @if($device->isCheckedOut())
                <tr>
                    <td class="info-title-text align-top pt-3">
                        {{ trans("devices.device_info.view.checked_out_to") }}
                        @if($device->assigned_for != 2) {{ trans('devices.device_info.view.user_bracket') }} @endif
                    </td>
                    <td>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($device->assigned_for == 2)
                                    {{-- Checked out to Place --}}
                                    @if($checkOutPlaceNote != null)
                                        <div class="mb-1">{{ trans("devices.device_info.view.notes") }}: {{ $checkOutPlaceNote }}</div>
                                    @endif

                                    <div class="d-flex align-items-center mb-1">
                                        <span class="b3-text fw-semibold">{{ $device->assignedPlace->place }}</span>
                                    </div>
                                    
                                    <div class="info-title-text mb-1">
                                        {{ isset($device->assignedPlace) && isset($device->assignedPlace->location) && !empty($device->assignedPlace->location->name) ? $device->assignedPlace->location->name : '' }}
                                    </div>
                                    
                                    <div class="mb-1">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M-3.49691e-07 8C-1.56568e-07 12.4181 3.58187 16 8 16C12.4181 16 16 12.4181 16 8C16 3.58187 12.4181 4.1086e-07 8 6.03983e-07C3.58187 7.97106e-07 -5.42814e-07 3.58187 -3.49691e-07 8ZM12.5333 8L8.452 12.2667L8.452 9.37147L3.46667 9.37147L3.46667 6.6288L8.452 6.6288L8.452 3.73333L12.5333 8Z" fill="#186B43"/>
                                        </svg>
                                        <span class="checkout-text">{{ trans("devices.device_info.view.checkedout_to_place") }}</span>
                                    </div>
                                @else
                                    {{-- Checked out to User --}}
                                    @php
                                        $userDisplayName = $device->assigneduser && $device->assigneduser->getGuranteedNameText(true) 
                                            ? $device->assigneduser->getGuranteedNameText(true) : trim(($device->assigneduser->first_name ?? '') . ' ' . ($device->assigneduser->last_name ?? '') . ' @ ' . ($device->assigneduser->username ?? ''));

                                        $userCompanyName = $device->assigneduser->job_type > 0 ? $device->assigneduser->ex_user_company : ($device->assigneduser->company->name ?? '');
                                    @endphp

                                    <div class="d-flex align-items-center mb-1">
                                        @if(!empty($device->assigneduser->avatar))
                                            <img src="{{ asset('storage/avatar/' . $device->assigneduser->avatar) }}" alt="{{ $userDisplayName }}" class="user-list-avatar rounded-circle me-2" width="28" height="28" onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                                            <span class="user-list-avatar user-list-avatar-fallback d-none">
                                                {{ strtoupper(substr($userDisplayName, 0, 1)) }}
                                            </span>
                                        @else
                                            <span class="user-list-avatar user-list-avatar-fallback" aria-hidden="true">
                                                {{ strtoupper(substr($userDisplayName, 0, 1)) }}
                                            </span>
                                        @endif
                                        <span class="b3-text fw-semibold">{{ $userDisplayName }}</span>
                                    </div>

                                    @if(!empty($userCompanyName))
                                        <div class="info-title-text mb-1">{{ $userCompanyName }}</div>
                                    @endif

                                    @if($checkOutPlaceNote != null)
                                        <div class="mb-1">{{ trans("devices.device_info.view.notes") }}: {{ $checkOutPlaceNote }}</div>
                                    @endif

                                    @if($device->assigneduser->email)
                                        <div class="mb-1">
                                            <span class="text-mail">{{ $device->assigneduser->email }}</span>
                                        </div>
                                    @endif

                                    <div class="mb-1">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M-3.49691e-07 8C-1.56568e-07 12.4181 3.58187 16 8 16C12.4181 16 16 12.4181 16 8C16 3.58187 12.4181 4.1086e-07 8 6.03983e-07C3.58187 7.97106e-07 -5.42814e-07 3.58187 -3.49691e-07 8ZM12.5333 8L8.452 12.2667L8.452 9.37147L3.46667 9.37147L3.46667 6.6288L8.452 6.6288L8.452 3.73333L12.5333 8Z" fill="#186B43"/>
                                        </svg>
                                        <span class="checkout-text">{{ trans("devices.device_info.view.checkedout_to_user") }}</span>
                                    </div>

                                    @if($device->assigneduser->phone)
                                        <div class="mb-1">{{ $device->assigneduser->phone }}</div>
                                    @endif

                                    @if($device->assigneduser->employee_num)
                                        <div class="mb-1">{{ $device->assigneduser->employee_num }}</div>
                                    @endif

                                    @if(isset($device->assigneduser) && isset($device->assigneduser->department) && isset($device->assigneduser->department->name))
                                        <div class="mb-1"><b>{{ trans('devices.device_info.view.user_department') }}:</b> <span class="user_dept">{{ $device->assigneduser->department->name }}</span></div>
                                    @endif
                                @endif

                                @if($device->projectName)
                                    <div class="mb-1">
                                        {{ !empty($device->projectName) && !empty($device->projectName->name) ? $device->projectName->name : '' }}
                                        <span>{{ $device->projectName->project_no }}</span>
                                    </div>
                                @endif

                                @if(isset($device->chkoutLog) && $device->chkoutLog->allocation_type_id != "")
                                    <div class="mb-1">
                                        {{ trans('devices.device_info.view.allocation_type') }}::
                                        <span class="allocation_type">{{ !empty($device->chkoutLog) && !empty($device->chkoutLog->allocationType) && !empty($device->chkoutLog->allocationType->name) ? $device->chkoutLog->allocationType->name : '' }}</span>
                                    </div>
                                @endif

                                @if($checkoutReasonName != "")
                                    <div class="mb-1">
                                        <b>{{ trans("devices.device_info.view.checkout_reason") }}:</b>
                                        <span class="checkout_reason">{{ $checkoutReasonName }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                @can("DeviceCheckoutCheckin")
                                    <button type="button" class="amg-btn amg-btn-primary amg-btn-block border-0 dtActCheckIn" data-id="{{ $device->id }}" data-asset_tag="{{ $device->asset_tag }}" data-name="{{ $device->name }}" data-company_id="{{ $device->company_id }}" data-cmp_name="{{ $device->company->name }}" data-checkout_date="{{ CommonHelper::displayDateTime($device->last_checkout, 'datetime', 'display') }}">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M-4.37114e-07 10C-1.9571e-07 15.5227 4.47733 20 10 20C15.5227 20 20 15.5227 20 10C20 4.47733 15.5227 -6.78517e-07 10 -4.37114e-07C4.47733 -1.9571e-07 -6.78517e-07 4.47733 -4.37114e-07 10ZM15.6667 10L10.565 15.3333L10.565 11.7143L4.33333 11.7143L4.33333 8.286L10.565 8.286L10.565 4.66667L15.6667 10Z" fill="white"/>
                                        </svg>
                                        {{ trans("devices.device_info.view.check_in") }}
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="info-title-text">{{ trans("devices.device_info.view.checked_out_date") }}</td>
                    <td class="b3-text fw-semibold">{{ CommonHelper::displayDateTime($device->last_checkout, 'date', 'display') }}</td>
                </tr>
                <tr>
                    <td class="info-title-text">{{ trans("devices.device_info.view.expected_checkin_date") }}</td>
                    <td class="b3-text fw-semibold">{{ CommonHelper::displayDateTime($device->expected_checkin, 'date', 'display') }}</td>
                </tr>
            @else
                @if($device->stock_place && !$device->sold_with_status)
                    <tr>
                        <td class="info-title-text">{{ trans("devices.device_info.view.stock_place") }}</td>
                        <td class="b3-text fw-semibold">
                            @isset($device->stockPlace->place)
                                <div><span data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.stock_place_tooltip') }}">{{ $device->stockPlace->place }}</span></div>
                            @endisset
                            @isset($device->location->name)
                                <div><span data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.location_tooltip') }}">{{ $device->location->name }}</span></div>
                            @endisset
                        </td>
                    </tr>
                @endif
            @endif
            @if($transfer_items)
                <tr>
                    <td class="info-title-text">{{ trans('devices.device_info.view.transfer_status') }}</td>
                    <td class="b3-text fw-semibold">
                        @foreach($transfer_items['transfer_item'] as $ti)
                            <div>
                                @if($ti['transfer_status'] == 1)
                                    <span class="role-badge role-badge-user">{{ trans('devices.device_info.view.under_transfer') }}</span>
                                @endif
                            </div>
                        @endforeach
                        @foreach($transfer_items['tran_from'] as $tf)
                            <div>{{ trans('devices.device_info.view.from') }} : {{ $tf['name'] }}</div> 
                        @endforeach
                        @foreach($transfer_items['tran_to'] as $tt)
                            <div>{{ trans('devices.device_info.view.to') }} : {{ $tt['name']}}</div> 
                        @endforeach
                        @foreach($transfer_items['transfer'] as $bc)
                            <div><span data-bs-toggle="tooltip" data-original-title="{{ trans('devices.device_info.view.batch_code') }}" data-bs-title="{{ trans('devices.device_info.view.batch_code') }}">{{ $bc['batch_code']}}</span></div>
                        @endforeach
                    </td>
                </tr>
            @endif    
            <tr>
                <td class="info-title-text">{{ trans("devices.device_info.view.category") }}</td>
                <td class="b3-text fw-semibold">{{ $device->model->category->name }}</td>
            </tr>
            <tr>
                <td class="info-title-text">{{ trans("devices.device_info.view.device_type") }}</td>
                <td class="b3-text fw-semibold">{{ $device->deviceType->name }}</td>
            </tr>
            <tr>
                <td class="info-title-text">{{ trans("devices.device_info.view.device_from") }}</td>
                <td class="b3-text fw-semibold">
                    @if($device->device_occure_type == 0)
                        <div>{{ trans('devices.device_info.view.purchase_device') }}</div>
                    @elseif($device->device_occure_type == 1)
                        <div>{{ trans('devices.device_info.view.project_device') }}</div>
                        @if($device->lease_id && $device->lease)
                            <div>{{ trans("devices.device_info.view.from_date") }}: {{ $device->lease->start_date }}</div>
                            <div>{{ trans("devices.device_info.view.to_date") }}: {{ $device->lease->end_date }}</div>
                        @endif
                    @elseif($device->device_occure_type == 2)
                        <div>{{ trans('devices.device_info.view.rental') }}</div>
                    @elseif($device->device_occure_type == 3)
                        <div>{{ trans('devices.device_info.view.customer_owned') }}</div>
                    @else
                        <div>{{ trans("devices.device_info.view.purchase_device") }}</div>
                    @endif
                </td>
            </tr>
            <tr class="collapse extra-details">
                <td class="info-title-text">{{ trans("devices.device_info.view.manufacturer") }}</td>
                <td class="b3-text fw-semibold">
                    <div class="d-flex align-items-center mb-1">
                        <img src="{{ asset('uploads/manufacturers/' . $device->model->manufacturer->attachment) }}" alt="{{ $device->model->manufacturer->name }}" class="user-list-avatar rounded-circle me-2" width="28" height="28" onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">

                        <span class="user-list-avatar user-list-avatar-fallback me-2 d-none">{{ strtoupper(substr($device->model->manufacturer->name, 0, 1)) }}</span>

                        @if($device->model->manufacturer)
                            <a class="custom-link" href="{{ url('manufactures') }}" target="_blank" >{{ $device->model->manufacturer->name }}</a>
                        @endif
                    </div>
                </td>
            </tr>
            @if($device->model->name)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.device_model") }}</td>
                    <td class="b3-text fw-semibold">
                        <a class="custom-link" href="{{ url('models') }}" target="_blank" >{{ $device->model->name }}</a>
                    </td>
                </tr>
            @endif
            @if($device->model && $device->model_no)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.model_no") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->model->model_no }}</td>
                </tr>
            @endif
            @if($device->serial)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.serialcode") }}</td>
                    <td class="b3-text fw-semibold">
                        {{ $device->serial }}
                        <i class="bi bi-copy copy-serial" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.click_to_copy_serial') }}" data-serial="{{ $device->serial }}"></i>
                    </td>
                </tr>
            @endif
            @if($device->product_number)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.product_number") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->product_number }}</td>
                </tr>
            @endif
            @if($device->uuid)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.device_UUID_code") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->uuid }}</td>
                </tr>
            @endif
            @if(config('services.assets.rfid_integration'))
                @if($device->deviceRfids->isNotEmpty())
                    <tr class="collapse extra-details">
                        <td class="info-title-text">{{ trans("devices.device_info.view.device_rfid") }}</td>
                        <td class="b3-text fw-semibold">
                            @foreach ($device->deviceRfids as $rfid)
                                <span class="tag ms-1">{{ $rfid->device_rfid }}</span>
                            @endforeach
                        </td>
                    </tr>
                @endif
            @endif
            @if($device->purchaseReference)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.purchase_reference") }}</td>
                    <td class="b3-text fw-semibold">
                        {{ $device->purchaseReference->invoice_no }}
                        @if($device->purchaseReference->invoice_date) 
                            ({{ CommonHelper::displayDateTime($device->purchaseReference->invoice_date, 'date', 'display') }}) 
                        @endif
                    </td>
                </tr>
            @endif
            @if($device->purchase_date)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.purchase_date") }}</td>
                    <td class="b3-text fw-semibold">{{ CommonHelper::displayDateTime($device->purchase_date, 'date', 'display') }}</td>
                </tr>
            @endif
            @if($device->purchase_cost)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.purchase_price") }}</td>
                    <td class="b3-text fw-semibold">{!! $purchase_currency !!} {{ number_format($device->purchase_cost, 2) }}</td>
                </tr>
            @endif
            @if($device->order_number)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.order_number") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->order_number }}</td>
                </tr>
            @endif
            @if($device->supplier)
                <tr class="collapse extra-details">
                    <td>{{ trans("devices.device_info.view.supplier") }}</td>
                    <td>
                        @if($device->supplier)
                            <a class="custom-link" href="{{ url('suppliers') }}" target="_blank" >{{ $device->supplier->name }}</a>
                        @endif
                    </td>
                </tr>
            @endif
            @if($device->location)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.location") }}</td>
                    <td class="b3-text fw-semibold">
                        @if($device->location)
                            <a class="custom-link" href="{{ url('locations') }}" target="_blank" >{{ $device->location->name }}</a>
                        @endif
                    </td>
                </tr>
            @endif
            @if($device->isCheckedOutToUser() && !empty($device->assigneduser->location->name))
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans('devices.device_info.view.checkout_user_location') }}</td>
                    <td class="b3-text fw-semibold">{{ $device->assigneduser->location->name }}</td>
                </tr>
            @endif
            @if($device->place && !empty($device->place->place))
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.internal_place") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->place->place }}</td>
                </tr>
            @endif
            @if($device->niDetectedLocation && !empty($device->niDetectedLocation->name))
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.last_network_location") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->niDetectedLocation->name }}</td>
                </tr>
            @endif
            @if($device->live_location)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.live_location") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->live_location }}</td>
                </tr>
            @endif
            @if($device->company && !empty($device->company->name))
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.company") }}</td>
                    <td class="b3-text fw-semibold">
                        @if($device->company)
                            <a class="custom-link" href="{{ url('companies') }}" target="_blank" >{{ $device->company->name }}</a>
                        @endif
                    </td>
                </tr>
            @endif
            @if($device->asset_owner)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.asset_owner") }}</td>
                    <td class="b3-text fw-semibold">
                        <div>
                            @if($device->assetOwner && $device->assetOwner->getGuranteedNameText(true))
                                <span>{{ $device->assetOwner->getGuranteedNameText(true) }}</span>
                            @else
                                <span>{{ $device->assetOwner->first_name }} {{ $device->assetOwner->last_name }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @endif
             <tr class="collapse extra-details">
                <td class="info-title-text">{{ trans("devices.device_info.view.requestable_device") }}</td>
                <td class="b3-text fw-semibold">{{ $device->requestable ? trans('devices.device_info.view.yes') : trans('devices.device_info.view.no') }}</td>
            </tr>
             <tr class="collapse extra-details">
                <td class="info-title-text">{{ trans("devices.device_info.view.high_priority_device") }}</td>
                <td class="b3-text fw-semibold">{{ $device->high_pririty ? trans('devices.device_info.view.yes') : trans('devices.device_info.view.no') }}</td>
            </tr>
             <tr class="collapse extra-details">
                <td class="info-title-text">{{ trans("devices.device_info.view.device_added_via") }}</td>
                <td class="b3-text fw-semibold">{{ $device->added_from == 2 ? trans('devices.device_info.view.network') : trans('devices.device_info.view.manual') }}</td>
            </tr>
            <tr class="collapse extra-details">
                <td class="info-title-text">{{ trans("devices.device_info.view.device_ip") }}</td>
                <td class="b3-text fw-semibold">{{ $device->ip }}</td>
            </tr>
            @if($device->mac)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.device_mac") }}</td>
                    <td class="b3-text fw-semibold">
                        {{ $device->mac }}
                        <i class="bi bi-copy copy-mac" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.copy_mac_address') }}" data-mac="{{ $device->mac }}"></i>
                    </td>
                </tr>
            @endif
            @if($device->created_at)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.record_created_at") }}</td>
                    <td class="b3-text fw-semibold">{{ CommonHelper::displayDateTime($device->created_at, 'datetime', 'display') }}</td>
                </tr>
            @endif
            @if($device->model->customFieldset && $device->model->customFieldset->fields)
                @foreach($device->model->customFieldset->fields as $f)
                    @php
                        $con = '_itm_' . str_replace(' ', '_', strtolower($f->name));
                        $modelCusValue = CommonHelper::getCustomDataFormate($f, $device->$con ?? null);
                    @endphp
                     @if(!empty($modelCusValue))
                        <tr class="collapse extra-details">
                            <td class="info-title-text">{{ $f->name }}</td>
                            <td class="b3-text fw-semibold">{{ $modelCusValue }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
            @if($device->model->category->customFieldset && $device->model->category->customFieldset->fields)
                @foreach($device->model->category->customFieldset->fields as $f)
                    @php
                        $con = '_itm_' . str_replace(' ', '_', strtolower($f->name));
                        $categoryCusValue = CommonHelper::getCustomDataFormate($f, $device->$con ?? null);
                    @endphp
                    @if(!empty($categoryCusValue))
                        <tr class="collapse extra-details">
                            <td class="info-title-text">{{ $f->name }}</td>
                            <td class="b3-text fw-semibold">{{ $categoryCusValue }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
            @if(!empty($companyFieldset))
                @foreach($companyFieldset as $key => $value)
                    @php
                        $formattedKey = ucwords(str_replace(['_itm_', '_'], ' ', $key));
                        $displayKey = '';

                        foreach ($fieldsetObj->fields as $field) {
                            if (strcasecmp(trim($formattedKey), trim($field->name)) === 0) {
                                $displayKey = $field->name;
                                break;
                            }
                        }
                    @endphp
                    @if($value != '')
                        <tr class="collapse extra-details">
                            <td class="info-title-text">{{ $displayKey }}</td>
                            <td class="b3-text fw-semibold">{{ $value }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
            @if($device->amc_expire_date)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.AMC_expire_date") }}</td>
                    <td class="b3-text fw-semibold">{{ CommonHelper::displayDateTime($device->amc_expire_date, 'date', 'display') }}</td>
                </tr>
            @endif
            @if($device->amcSupplier && !empty($device->amcSupplier->name))
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.amc_supplier") }}</td>
                    <td class="b3-text fw-semibold">
                        <a class="custom-link" href="{{ url('suppliers') }}" target="_blank" >{{ $device->amcSupplier->name }}</a>
                    </td>
                </tr>
            @endif
            @if(!empty($device->ship_date))
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans('devices.device_info.view.device_ship_date') }}</td>
                    <td class="b3-text fw-semibold">{{ CommonHelper::displayDateTime($device->ship_date, 'date', 'display') }}</td>
                </tr>
            @endif
            <tr class="collapse extra-details">
                <td class="info-title-text">{{ trans("devices.device_info.view.warranty_expired_status") }}</td>
                <td class="b3-text fw-semibold">
                    @if($warrantyExpiredStatus)
                        <div>
                            {{ $warrantyExpiredStatus }}
                            @if($device->calc_warranty_expire_date)
                                <span class="fw-light">
                                    - ({{ CommonHelper::displayDateTime($device->calc_warranty_expire_date, 'date', 'display') }})
                                </span>
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
            @if($device->warranty_start_date)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.warranty_start_date") }}</td>
                    <td class="b3-text fw-semibold">{{ CommonHelper::displayDateTime($device->warranty_start_date, 'date', 'display')}}</td>
                </tr>
            @endif
            @if($device->warranty_months)
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.warranty_months") }}</td>
                    <td class="b3-text fw-semibold">{{ $device->warranty_months }}</td>
                </tr>
            @endif
            @if($device->isLiveWarrentyPossible())
                <tr class="collapse extra-details">
                    <td class="info-title-text">{{ trans("devices.device_info.view.manufacturer_warrenty_details") }}</td>
                    <td class="b3-text fw-semibold">
                        @php
                            $anyWarrentyAvail = false;
                        @endphp
                        @if(stripos($device->model->manufacturer->name, "dell") !== false && count($warrentyInfo))
                            @foreach($warrentyInfo as $wi)
                                @if(is_array($wi) && isset($wi['endDate']))
                                    @php
                                        $isInWarrenty = CommonHelper::isInWarrenty($wi['endDate']);
                                    @endphp
                                    @if($isInWarrenty && count($isInWarrenty))
                                        @php
                                            $anyWarrentyAvail = true;
                                        @endphp
                                        <div class="clearfix">
                                            <div class="pull-left">
                                                {{ $wi['serviceLevelDescription'] }}
                                            </div>
                                            <div class="pull-right">
                                                {{ CommonHelper::displayDateTime($wi['endDate'], 'date', 'display') }}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @elseif(stripos($device->model->manufacturer->name, "lenovo") !== false && count($warrentyInfo))
                            @foreach($warrentyInfo as $wi)
                                @if(is_array($wi) && isset($wi['End']))
                                    @php
                                        $isInWarrenty = CommonHelper::isInWarrenty($wi['End']);
                                    @endphp
                                    @if($isInWarrenty && count($isInWarrenty))
                                        @php
                                            $anyWarrentyAvail = true;
                                        @endphp
                                        <div class="clearfix">
                                            <div class="pull-left w-75">
                                                <div class="text-bold" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.warranty_name') }}" data-original-title="{{ trans('devices.device_info.view.warranty_name') }}">{{ $wi['Name'] }}</div>
                                                <div class="text-normal" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.warranty_description') }}" data-original-title="{{ trans('devices.device_info.view.warranty_description') }}">{{ $wi['Description'] }}</div>
                                            </div>
                                            <span class="pull-right text-bold" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.warranty_expire_date') }}" data-original-title="{{ trans('devices.device_info.view.warranty_expire_date') }}">{{ trans('devices.device_info.view.expire_date') }}: <span class="view-more-text">{{ CommonHelper::displayDateTime($wi['End'], 'date', 'display') }}</span></span>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @elseif((stripos(strtolower($device->model->manufacturer->name), "asus") !== false || stripos(strtolower($device->model->manufacturer->name), "acer") !== false || stripos(strtolower($device->model->manufacturer->name), "hp") !== false) && count($warrentyInfo))
                            @if(is_array($warrentyInfo) && isset($warrentyInfo['endDate']))
                                @php
                                    $isInWarrenty = CommonHelper::isInWarrenty($warrentyInfo['endDate']);
                                @endphp
                                @if($isInWarrenty && count($isInWarrenty))
                                    @php
                                        $anyWarrentyAvail = true;
                                    @endphp
                                    <div class="clearfix">
                                        <div class="pull-left w-75">
                                            @if(isset($warrentyInfo['entitlementType']))
                                                <div class="text-bold" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.warranty_name') }}" data-original-title="{{ trans('devices.device_info.view.warranty_name') }}">{{ $warrentyInfo['entitlementType'] }}</div>
                                            @endif
                                            @if(isset($warrentyInfo['entitlementType']))
                                                <div class="text-normal" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.warranty_description') }}" data-original-title="{{ trans('devices.device_info.view.warranty_description') }}">{{ $warrentyInfo['serviceLevelDescription'] }}</div>
                                            @endif
                                        </div>
                                        <span class="pull-right text-bold" data-bs-toggle="tooltip" data-bs-title="{{ trans('devices.device_info.view.warranty_expire_date') }}" data-original-title="{{ trans('devices.device_info.view.warranty_expire_date') }}">Expire Date: <span class="view-more-text">{{ CommonHelper::displayDateTime($warrentyInfo['endDate'], 'date', 'display') }}</span></span>
                                    </div>
                                @endif
                            @endif
                        @endif

                        @if(! $anyWarrentyAvail)
                            {{ trans('devices.device_info.view.no_manufacturer_warranty') }}
                        @endif
                    </td>
                </tr>
            @endif
            <tr class="collapse extra-details">
                <td class="info-title-text">{{ trans("devices.device_info.view.notes") }}</td>
                <td class="b3-text fw-semibold">{{ $device->notes }}</td>
            </tr>
            @if($dispose_data && $device->status_id == 7)
                @if(optional($dispose_data->disposedBy)->first_name || optional($dispose_data->disposedBy)->last_name)
                    <tr class="collapse extra-details">
                        @if($dispose_data ->dispose_type == 1)
                            <td class="info-title-text">{{ trans('devices.device_info.view.sold_by') }}</td>
                        @endif
                        @if($dispose_data ->dispose_type == 2)
                            <td class="info-title-text">{{ trans('devices.device_info.view.donated_by') }}</td>
                        @endif
                        @if($dispose_data ->dispose_type == 3)
                            <td class="info-title-text">{{ trans('devices.device_info.view.recycled_by') }}</td>
                        @endif
                        @if($dispose_data ->dispose_type == 4)
                            <td class="info-title-text">{{ trans('devices.device_info.view.disposed_by') }}</td>
                        @endif
                        <td class="b3-text fw-semibold">
                            <span>{{ optional($dispose_data->disposedBy)->first_name ?? '' }}</span>
                            <span>{{ optional($dispose_data->disposedBy)->last_name ?? '' }}</span>
                        </td>
                    </tr>
                @endif
                @if($dispose_data->dispose_at)
                    <tr class="collapse extra-details">
                        @if($dispose_data ->dispose_type == 1)
                            <td class="info-title-text">{{ trans('devices.device_info.view.sold_at') }}</td>
                        @endif
                        @if($dispose_data ->dispose_type == 2)
                            <td class="info-title-text">{{ trans('devices.device_info.view.donated_at') }}</td>
                        @endif
                        @if($dispose_data ->dispose_type == 3)
                            <td class="info-title-text">{{ trans('devices.device_info.view.recycled_at') }}</td>
                        @endif
                        @if($dispose_data ->dispose_type == 4)
                            <td class="info-title-text">{{ trans('devices.device_info.view.disposed_at') }}</td>
                        @endif
                        <td>{{ CommonHelper::displayDateTime($dispose_data->dispose_at, 'date', 'display') }}</td>
                    </tr>
                @endif
                @if($dispose_data->dispose_price)
                    <tr class="collapse extra-details">
                        @if($dispose_data ->dispose_type == 1)
                            <td class="info-title-text">{{ trans('devices.device_info.view.sold_price') }}</td>
                        @endif
                        <td class="b3-text fw-semibold">{!! $sold_value_format !!} {{ number_format($dispose_data->dispose_price, 2) }}</td>
                    </tr>
                @endif
                @if($dispose_data->dispose_type == 2 && !empty($dispose_data->vendor_org_name))
                    <tr class="collapse extra-details">
                        <td class="info-title-text">{{ trans('devices.device_info.view.ngo_organization_name') }}</td>
                        <td class="b3-text fw-semibold">{{ $dispose_data->vendor_org_name }}</td>
                    </tr>
                @endif
                @if($dispose_data->dispose_type == 3 && optional($dispose_data->vendorName)->name)
                    <tr class="collapse extra-details">
                        <td class="info-title-text">{{ trans('devices.device_info.view.vendor_name') }}</td>
                        <td class="b3-text fw-semibold">{{ $dispose_data->vendorName->name }}</td>
                    </tr>
                @endif
            @endif
        </tbody>
    </table>
    <div class="p-3 bg-white rounded-bottom border-top">
        <a href="#" data-bs-toggle="collapse" data-bs-target=".extra-details" role="button" aria-expanded="false" class="text-decoration-none fw-medium view-more-text">
            {{ trans('devices.device_info.view.view_more_details') }}
        </a>
    </div>
</div>