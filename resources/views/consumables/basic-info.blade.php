{{--
/**
* ------------------------------------------------------------
* File: basic-info.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-04
* Created On: 2026-01-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}
<div class="row g-3">
    <div class="col-lg-8 col-md-7">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.name") }}</div>
                    <div class="col-8 fw-semibold">{{ $consumable->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.consumable_tag") }}</div>
                    <div class="col-8 fw-semibold">
                        @if (!empty($consumable->unique_tag))
                            {{ $consumable->unique_tag }}
                        @else
                            @if (config("app.client") == "etherealmachines")
                                <span>CN</span>{{ $consumable->id }}
                            @else 
                                <span>CNS</span>{{ $consumable->id }}
                            @endif
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.manufacture") }}</div>
                    <div class="col-8 fw-semibold">
                        @can('ManufactureRead')
                            <a href="{{ url('manufactures') }}" target="_blank" >{{ $consumable->manufacture->name }}</a>
                        @else
                            {{ $consumable->manufacture->name }}
                        @endcan
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.category") }}</div>
                    <div class="col-8 fw-semibold">
                       @if($consumable->category)
                            @can('CategoryRead')
                                <a href="{{ url('categories') }}" target="_blank" >{{ optional($consumable->category)->name }}</a>
                            @else
                                {{ optional($consumable->category)->name }}
                            @endcan
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.department") }}</div>
                    <div class="col-8 fw-semibold">
                       @if($consumable->department) 
                            @can('DepartmentRead')
                                <a href="{{ url('departments') }}" target="_blank" >{{ optional($consumable->department)->name }}</a>
                            @else
                                {{ optional($consumable->department)->name }}
                            @endcan
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.quantity") }}</div>
                    <div class="col-8 fw-semibold">{{ $consumable->qty }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.threshold") }}</div>
                    <div class="col-8 fw-semibold">{{ $consumable->consumable_thresholds }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.available") }}</div>
                    <div class="col-8 fw-semibold">
                        @php
                            $checkout = App\Models\Consumable::withCount(['users', 'places', 'device'])->find($consumable->id);
                            $total_allocated = ($checkout->users_count ?? 0) + ($checkout->places_count ?? 0) + ($checkout->device_count ?? 0);
                            $available = ($consumable->qty ?? 0) - $total_allocated - ($consumable->scrap_qty ?? 0);
                        @endphp
                        <div style="display:inline-block;">{{ $available }}</div>
                        @if( $available > 0 )
                            <button type="button" class="amg-btn amg-btn-primary dtActCheckOut check-at-info" data-id="{{$consumable->id }}" data-company_id="{{$consumable->company_id}}" data-name="{{$consumable->name}}" data-bs-target="#checkoutconsumablemodal" data-bs-toggle="modal" action="checkout">
                                {{ trans("consumables.consumables_info.view.checkout") }}
                            </button>
                        @endif
                    </div>
                </div>
                @if(auth()->user()->can('ConsumableEdit') && auth()->user()->can('ConsumableAdd'))
                    <div class="row mb-3">
                        <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.scrap_quantity") }}</div>
                        <div class="col-8 fw-semibold">
                            {{ $consumable->scrap_qty }}
                            @can('ConsumableScrap')
                                @if( $available > 0 )
                                    <div style="display:inline-block; padding: 5px; border-radius: 4px;" class="check-at-info">
                                        <button type="button" class="dtActScrap amg-btn amg-btn-primary" data-id="{{ $consumable->id }}" data-consumable-name="{{ $consumable->name }}" >
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                <path d="M7 19C7 20.1 7.9 21 9 21H15C16.1 21 17 20.1 17 19V7H7V19ZM19 4H15.5L14.5 3H9.5L8.5 4H5V6H19V4Z" fill="currentColor"></path>
                                            </svg>
                                            {{ trans('consumables.consumables_info.view.scrap') }}</button>
                                    </div>
                                @endif
                            @endcan
                            @can('ConsumableRevertScrap')
                                @if( $consumable->scrap_qty  > 0 )
                                <div style="display:inline-block; padding: 5px; border-radius: 4px;" class="check-at-info">
                                    <button type="button" class="amg-btn amg-btn-secondary dtActScrapRevert" data-id="{{ $consumable->id }}" data-consumable-name="{{ $consumable->name }}" >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 5V1L7 6L12 11V7C15.31 7 18 9.69 18 13C18 16.31 15.31 19 12 19C8.69 19 6 16.31 6 13H4C4 17.42 7.58 21 12 21C16.42 21 20 17.42 20 13C20 8.58 16.42 5 12 5Z" fill="currentColor">
                                            </path>
                                        </svg>
                                        {{ trans('consumables.consumables_info.view.scrap_revert') }}</button>
                                </div>
                                @endif
                            @endcan
                        </div>
                    </div>
                @endif
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.unit") }}</div>
                    <div class="col-8 fw-semibold">{{ $consumable->unit->name ?? ''}}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.company") }}</div>
                    <div class="col-8 fw-semibold">
                        @if($consumable->company)
                            <a href="{{ url('companies') }}" target="_blank" >{{ optional($consumable->company)->name }}</a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.location") }}</div>
                    <div class="col-8 fw-semibold">
                        @if($consumable->location)
                            @can('LocationRead') 
                                <a href="{{ url('locations') }}" target="_blank" >{{ optional($consumable->location)->name }}</a>
                            @else
                                {{ optional($consumable->location)->name }}
                            @endcan
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.internal_place") }}</div>
                    <div class="col-8 fw-semibold">
                        @if($consumable->place)
                            @can('PlaceRead') 
                                <a href="{{ url('internal-places') }}" target="_blank" >{{ optional($consumable->place)->place }}</a>
                            @else
                                {{ optional($consumable->place)->place }}
                            @endcan
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.supplier") }}</div>
                    <div class="col-8 fw-semibold">
                        @if($consumable->supplier)
                            <a href="{{ url('suppliers') }}" target="_blank" >{{ $consumable->supplier->name }}</a>
                        @endif
                    </div>
                </div>
                @if($consumable->purchaseReference)
                    <div class="row mb-3">
                        <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.purchase_reference") }}</div>
                        <div class="col-8 fw-semibold">
                            {{ $consumable->purchaseReference->invoice_no }}
                            @if($consumable->purchaseReference->invoice_date)
                                ({{ CommonHelper::displayDateTime($consumable->purchaseReference->invoice_date, 'date', 'display') }})
                            @endif
                        </div>
                    </div>
                @endif
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.purchase_date") }}</div>
                    <div class="col-8 fw-semibold">{{ CommonHelper::displayDateTime($consumable->purchase_date, 'date', 'display') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.order_no") }}</div>
                    <div class="col-8 fw-semibold">{{ $consumable->order_number }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.purchase_cost") }}</div>
                    <div class="col-8 fw-semibold">
                        @php
                            $currencies = App\Models\Currency::getCurrencies();
                            $symbol = $currencies[$consumable->currency]['symbol'] ?? 'Rs';
                        @endphp
                        {{$symbol}} {{ number_format($consumable->purchase_cost,2) }}
                    </div>
                </div>
                @if($consumable->category->customFieldset && $consumable->category->customFieldset->fields)
                    @foreach($consumable->category->customFieldset->fields as $f)
                    @php
                        $con = '_itm_' . str_replace(' ', '_', strtolower($f->name));
                        $modelCusValue = CommonHelper::getCustomDataFormate($f, $consumable->$con ?? null);
                    @endphp
                    @if(!empty($modelCusValue))
                        <tr>
                            <td>{{ $f->name }}</td>
                            <td>{{ $modelCusValue }}</td>
                        </tr>
                    @endif
                    @endforeach
                @endif
                @php
                    $companyFieldset = [];
                    if (App\Models\Settings::first()->consumable_custom_fieldset_id != "") {
                        $fieldsetObj = App\Models\CustomFieldset::where('id', App\Models\Settings::first()->consumable_custom_fieldset_id)->first();
                        if (!empty($fieldsetObj)) {
                            $companyFieldset = CommonHelper::getCustomData($fieldsetObj->fields, $consumable);
                        }
                    }
                @endphp
                @if(!empty($companyFieldset))
                    @foreach($companyFieldset as $key => $fields)
                        @if($fields != '')
                            <tr>
                                <td>{{ ucwords(str_replace(['_itm_', '_'], ' ', $key)) }}</td>
                                <td>{{ $fields }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
                <div class="row mb-3">
                    <div class="col-4 text-muted small">{{ trans("consumables.consumables_info.view.notes") }}</div>
                    <div class="col-8 fw-semibold">{{ $consumable->notes }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-5">
        <div class="rounded-4">
            <div class="card-body p-0">
                <div class="card rounded-4 border-0 shadow-sm mb-3">
                    <div class="card-body text-center p-4">
                        @if(!empty($path))
                            <img src="{{ $path }}" class="img-fluid rounded-4 border" style="max-height:240px; object-fit:contain;">
                        @else
                            <div class="py-5 text-muted">
                                <div>
                                    {{ trans('consumables.consumables_info.view.no_image_available') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-2">
                            {{-- TOTAL QTY --}}
                            <div class="col-6 d-flex">
                                <div class="p-3 rounded-4 user-stats-card border bg-light se-cards w-100">
                                    <div class="text-muted small">
                                        {{ trans("consumables.consumables_info.view.quantity") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $consumable->qty }}
                                        </span>
                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle" style="height:40px; width:40px;">
                                            <svg width="20" height="20" viewBox="0 0 24 25" fill="none" >
                                                <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z" stroke="#E20505" stroke-width="1.5" />
                                                <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 d-flex">
                                <div class="p-3 rounded-4 user-stats-card border se-cards w-100">
                                    <div class="text-muted small">
                                        {{ trans("consumables.consumables_info.view.available") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $available ?? 0 }}
                                        </span>
                                        <span class="d-flex align-items-center justify-content-center border rounded-circle" style="height:40px; width:40px;">
                                            <svg width="20" height="20" viewBox="0 0 24 25" fill="none" >
                                                <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z" stroke="#E20505" stroke-width="1.5" />
                                                <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 d-flex">
                                <div class="p-3 rounded-4 user-stats-card border se-cards w-100">
                                    <div class="text-muted small">
                                        {{ trans("consumables.consumables_info.view.scrap_quantity") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $consumable->scrap_qty ?? 0 }}
                                        </span>
                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle"
                                            style="height:40px; width:40px;">
                                            <svg width="20" height="20" viewBox="0 0 24 25" fill="none" >
                                                <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z" stroke="#E20505" stroke-width="1.5" />
                                                <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 d-flex">
                                <div class="p-3 rounded-4 user-stats-card border se-cards w-100">
                                    <div class="text-muted small">
                                        {{ trans("consumables.consumables_info.view.threshold") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $consumable->consumable_thresholds ?? 0 }}
                                        </span>
                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle"
                                            style="height:40px; width:40px;">
                                            <svg width="20" height="20" viewBox="0 0 24 25" fill="none" >
                                                <path d="M7 22H17" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M2 17V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H20C20.5304 2 21.0391 2.21071 21.4142 2.58579C21.7893 2.96086 22 3.46957 22 4V17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H4C3.46957 19 2.96086 18.7893 2.58579 18.4142C2.21071 18.0391 2 17.5304 2 17Z" stroke="#E20505" stroke-width="1.5" />
                                                <path d="M9 10.5L11 12.5L15 8.5" stroke="#E20505" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .select2-container--open {
        z-index: 999999 !important;
    }

    .select2-dropdown {
        z-index: 999999 !important;
    }
</style>