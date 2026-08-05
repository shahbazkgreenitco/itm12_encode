{{--
/**
------------------------------------------------------------
File: view.blade.php
Module: Accessories
ACC/26/04
------------------------------------------------------------
Version: 1.0.0
Author: Safdar Ali
Page ID: #ACC-005
Created On: 2026-05-12
Reviewed By: -
------------------------------------------------------------
Purpose:
Accessories View Details Page

------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
------------------------------------------------------------
*/
--}}
<div class="row g-3">
    {{-- LEFT SIDE --}}
    <div class="col-lg-8 col-md-7">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body p-4">
                {{-- BASIC INFO --}}
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.accessory_name") }}
                    </div>
                    <div class="col-8 fw-semibold">
                        {{ $accessory->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.batch_no") }}
                    </div>
                    <div class="col-8">
                        {{ $accessory->batch_no() }}
                    </div>
                </div>

                @if($accessory->department)
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.department") }}
                    </div>

                    <div class="col-8">
                        <a href="{{ url('departments') }}" target="_blank" class="text-decoration-none">
                            {{ $accessory->department }}
                        </a>

                    </div>

                </div>

                @endif

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.total_quantity") }}
                    </div>
                    <div class="col-8 fw-medium">
                        {{ $accessory->qty }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.available_quantity") }}
                    </div>

                    <div class="col-8 d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold">
                            {{ $available_qty }}
                        </span>

                        @can("AccessoriesCheckout")

                        @if($available_qty > 0)
                        <button type="button" class="amg-btn amg-btn-primary dtActCheckOut"
                            data-id="{{ $accessory->id }}" data-accessory-name="{{ $accessory->name }}"
                            data-category-name="@if($accessory->category) {{ $accessory->category->name }} @endif"
                            data-company_id="{{ $accessory->company_id }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M10 17V14H3V10H10V7L15 12L10 17ZM21 19H11V21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3H11V5H21V19Z"
                                    fill="currentColor"></path>
                            </svg>
                            {{ trans("accessories.accessory_fields.Check_Out") }}
                        </button>
                        @endif
                        @endcan

                    </div>

                </div>

                <div class="row mb-3">

                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.scrap_quantity") }}
                    </div>

                    <div class="col-8 d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold">
                            {{ $accessory->scrap_qty }}
                        </span>
                        @can('AccessoriesScrap')
                        @if($available_qty > 0)

                        <button type="button" class="amg-btn amg-btn-primary dtActScrap" data-id="{{ $accessory->id }}"
                            data-accessory-name="{{ $accessory->name }}"
                            data-category-name="@if($accessory->category) {{ $accessory->category->name }} @endif"
                            data-company_id="{{ $accessory->company_id }}">

                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M7 19C7 20.1 7.9 21 9 21H15C16.1 21 17 20.1 17 19V7H7V19ZM19 4H15.5L14.5 3H9.5L8.5 4H5V6H19V4Z"
                                    fill="currentColor">
                                </path>
                            </svg>

                            Scrap

                        </button>
                        @endif
                        @endcan

                        @can('AccessoriesRevertScrap')
                        @if($accessory->scrap_qty > 0)
                        <button type="button" class="amg-btn amg-btn-secondary dtActScrapRevert"
                            data-id="{{ $accessory->id }}" data-accessory-name="{{ $accessory->name }}"
                            data-category-name="@if($accessory->category) {{ $accessory->category->name }} @endif"
                            data-company_id="{{ $accessory->company_id }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M12 5V1L7 6L12 11V7C15.31 7 18 9.69 18 13C18 16.31 15.31 19 12 19C8.69 19 6 16.31 6 13H4C4 17.42 7.58 21 12 21C16.42 21 20 17.42 20 13C20 8.58 16.42 5 12 5Z"
                                    fill="currentColor">
                                </path>
                            </svg>
                            Scrap Revert
                        </button>
                        @endif
                        @endcan

                    </div>

                </div>

                <div class="row mb-3">

                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.threshold") }}
                    </div>

                    <div class="col-8">
                        {{ $accessory->accessory_thresholds }}
                    </div>

                </div>

                <hr class="my-4">

                {{-- CATEGORY / COMPANY --}}
                <div class="row mb-3">

                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.category") }}
                    </div>

                    <div class="col-8">
                        @if($accessory->category)
                        <a href="{{ url('categories') }}" target="_blank"
                            class="badge rounded-pill bg-light text-dark border text-decoration-none">
                            {{ $accessory->category->name }}
                        </a>
                        @endif
                    </div>

                </div>

                @if($accessory->manufacturer)
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.manufacturer") }}
                    </div>

                    <div class="col-8">
                        <a href="{{ url('manufactures') }}" target="_blank" class="text-decoration-none">
                            {{ $accessory->manufacturer->name }}
                        </a>
                    </div>

                </div>

                @endif

                @if($accessory->supplier)

                <div class="row mb-3">

                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.supplier") }}
                    </div>

                    <div class="col-8">
                        {{ $accessory->supplier->name }}
                    </div>

                </div>

                @endif

                @if($accessory->purchaseReference)

                <div class="row mb-3">

                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.purchase_reference") }}
                    </div>

                    <div class="col-8">

                        {{ $accessory->purchaseReference->invoice_no }}

                        @if($accessory->purchaseReference->invoice_date)

                        ({{ CommonHelper::getDateAs($accessory->purchaseReference->invoice_date,
                        "d/m/Y", "Y-m-d") }})

                        @endif

                    </div>

                </div>

                @endif

                <hr class="my-4">

                {{-- PURCHASE --}}
                @if($accessory->purchase_date)
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.purchase_date") }}
                    </div>
                    <div class="col-8">
                        {{ CommonHelper::getDateAs($accessory->purchase_date, "d/m/Y", "Y-m-d")
                        }}
                    </div>
                </div>
                @endif

                @if($accessory->purchase_cost)
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.purchase_price") }}
                    </div>
                    <div class="col-8">
                        {!! $currency_symbol !!}
                        {{ number_format($accessory->purchase_cost, 2) }}
                    </div>

                </div>

                @endif

                @if($accessory->order_number)
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.order_number") }}
                    </div>
                    <div class="col-8">
                        {{ $accessory->order_number }}
                    </div>
                </div>
                @endif

                @if($accessory->location)
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.location") }}
                    </div>
                    <div class="col-8">
                        <a href="{{ url('locations') }}" target="_blank" class="text-decoration-none">
                            {{ $accessory->location->name }}
                        </a>
                    </div>
                </div>
                @endif

                @if($accessory->company)
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.company") }}
                    </div>
                    <div class="col-8">
                        <a href="{{ url('companies') }}" target="_blank"
                            class="badge rounded-pill bg-light text-dark border text-decoration-none">
                            {{ $accessory->company->name }}
                        </a>
                    </div>
                </div>
                @endif

                <hr class="my-4">

                {{-- NOTES --}}
                @if(!empty($accessory->notes))

                <div class="row">

                    <div class="col-4 text-muted small">
                        {{ trans("accessories.accessory_fields.notes") }}
                    </div>

                    <div class="col-8">
                        {{ $accessory->notes }}
                    </div>

                </div>

                @endif

            </div>

        </div>

    </div>

    {{-- RIGHT SIDE --}}
    <div class="col-lg-4 col-md-5">
        <div class="rounded-4">

            <div class="card-body p-0">

                {{-- IMAGE PREVIEW --}}
                <div class="card rounded-4 border-0 shadow-sm mb-3">

                    <div class="card-body text-center p-4">

                        @if(!empty($path))

                        <img src="{{ $path }}" class="img-fluid rounded-4 border"
                            style="max-height:240px; object-fit:contain;">

                        @else

                        <div class="py-5 text-muted">

                            <i class="fa fa-image fa-2x mb-3"></i>

                            <div>
                                No image available
                            </div>

                        </div>

                        @endif

                    </div>

                </div>

                {{-- QUICK STATS --}}
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-2">
                            {{-- TOTAL QTY --}}
                            <div class="col-6 d-flex">
                                <div class="p-3 rounded-4 user-stats-card border bg-light se-cards w-100">
                                    <div class="text-muted small">
                                        {{ trans("accessories.accessory_fields.total_quantity") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $accessory->qty }}
                                        </span>
                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle"
                                            style="height:40px; width:40px;">

                                            <i class="fa fa-cubes text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- AVAILABLE --}}
                            <div class="col-6 d-flex">

                                <div class="p-3 rounded-4 user-stats-card border se-cards w-100">

                                    <div class="text-muted small">
                                        {{ trans("accessories.accessory_fields.available_quantity") }}
                                    </div>

                                    <div class="d-flex justify-content-between align-items-end pt-2">

                                        <span class="fw-bold text-dark fs-5">

                                            {{ $available_qty ?? 0 }}

                                        </span>

                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle"
                                            style="height:40px; width:40px;">

                                            <i class="fa fa-check text-success"></i>

                                        </span>

                                    </div>

                                </div>

                            </div>

                            {{-- SCRAP --}}
                            <div class="col-6 d-flex">
                                <div class="p-3 rounded-4 user-stats-card border se-cards w-100">
                                    <div class="text-muted small">
                                        {{ trans("accessories.accessory_fields.scrap_quantity") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $accessory->scrap_qty }}
                                        </span>
                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle"
                                            style="height:40px; width:40px;">
                                            <i class="fa fa-recycle text-secondary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- THRESHOLD --}}
                            <div class="col-6 d-flex">
                                <div class="p-3 rounded-4 user-stats-card border se-cards w-100">
                                    <div class="text-muted small">
                                        {{ trans("accessories.accessory_fields.threshold") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $accessory->accessory_thresholds }}
                                        </span>
                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle"
                                            style="height:40px; width:40px;">
                                            <i class="fa fa-exclamation-triangle text-warning"></i>
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