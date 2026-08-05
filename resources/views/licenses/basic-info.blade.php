
<div class="row g-3">
    {{-- LEFT SIDE --}}
    <div class="col-lg-8 col-md-7">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body p-4">
                {{-- BASIC INFO --}}
                
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.name") }}
                    </div>
                    <div class="col-8 fw-semibold">
                        {{ $licence->name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.uniqe_tag") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->unique_tag }}
                    </div>
                </div>
     
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.batch_no") }}
                    </div>
                    <div class="col-8">
                        LIC {{ $licence->id }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.manufacturer") }}
                    </div>
                    <div class="col-8 fw-medium">
                            @if($licence->manufacture)
                                <a href="#" target="_blank">{{ $licence->manufacture->name }}</a>
                            @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.category") }}
                    </div>
                    <div class="col-8 d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold">
                            @if($licence->category)
                                <a href="#" target="_blank">{{ $licence->category->name }}</a>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.company") }}
                    </div>
                    <div class="col-8 d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold">
                            @if($licence->company)
                                <a href="#" target="_blank">{{ $licence->company->name }}</a>
                            @endif
                        </span>
                    </div>
                </div>  

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.licensed_to_email") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->license_email }}
                    </div>
                </div>

                <hr class="my-4">

                
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.seats") }}
                    </div>
                    <div class="col-8">
                    {{ $licence->seats }}
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.available_seats") }}
                    </div>

                    <div class="col-8 d-flex align-items-center gap-3">
                        <span>{{ $licence->available_seats ?? 0 }}</span>
                        @can("LicenseCheckout")
                        @if($licence->available_seats > 0)
                        <button type="button" id="dtActCheckOut" class="amg-btn amg-btn-primary dtActCheckOut"
                        data-id="{{$licence->id}}" data-accessory-name="{{$licence->name}}" data-category-name="{{$licence->category_id}}" data-company_id="{{$licence->company_id}}" data-serial_num="{{$licence->serial}}"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M10 17V14H3V10H10V7L15 12L10 17ZM21 19H11V21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3H11V5H21V19Z"
                                    fill="currentColor">
                                </path>
                            </svg>
                            {{ trans("licenses.license_detail.check_out") }}
                        </button>
                        @endif
                        @endcan
                    </div>
                </div>     
                 
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.uniqe_serial") }}
                    </div>
                    <div class="col-8">
                         {{ $licence->serial }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.supplier") }}
                    </div>
                    <div class="col-8">
                        @if($licence->supplier)
                            <a href="#" target="_blank">{{ $licence->supplier->name }}</a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.purchase_order_number") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->purchase_order }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.depriciation") }}
                    </div>
                    <div class="col-8">
                        @if($licence->depreciation)
                            <a href="#" target="_blank">
                                {{ $licence->depreciation->name }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.purchase_date") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->purchase_date ? CommonHelper::displayDateTime($licence->purchase_date, 'date', 'display') : '' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.purchase_cost") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->purchase_cost }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.expiration_date") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->expiration_date ? CommonHelper::displayDateTime($licence->expiration_date, 'date', 'display') : '' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.order_number") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->order_number }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.agreement") }}/{{ trans("licenses.license_detail.oem_number") }}
                    </div>
                    <div class="col-8">
                       {{ $licence->agreement_no }}
                    </div>
                </div>

                <hr class="my-4">
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.reassignable") }}
                    </div>
                    <div class="col-8">
                        <td><?php echo( $licence->reassignable == 1 ? 'Yes' : 'No' ); ?></td>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.support") }}
                    </div>
                    <div class="col-8">
                        {{ $licence->support }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4 text-muted small">
                        {{ trans("licenses.license_detail.notes") }}
                    </div>
                    <div class="col-8">
                       {{ $licence->notes }}
                    </div>
                </div>
                <hr class="my-4">
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
                        @if(!empty($licence->image))
                        <img src="{{ $licence->image}}" class="img-fluid rounded-4 border"
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
                                        {{ trans("licenses.license_detail.seats") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">
                                            {{ $licence->seats ?? 0 }}
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
                                       {{ trans("licenses.license_detail.available_seats") }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end pt-2">
                                        <span class="fw-bold text-dark fs-5">

                                            {{ $licence->available_seats ?? 0 }}

                                        </span>

                                        <span
                                            class="d-flex align-items-center justify-content-center border rounded-circle"
                                            style="height:40px; width:40px;">

                                            <i class="fa fa-check text-success"></i>
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