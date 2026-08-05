{{--
/**
* ------------------------------------------------------------
* File: edit.blade.php
* Module: Suppliers
* SUP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #SUP-003
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

{{-- <div class="modal fade" id="EditAddressFormModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" id="EditAddressForm" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Address</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label mandatory" for="address">{{ trans("header.supplier_fields.street_1") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" name="address" placeholder="Enter Street" id="address" class="form-control" @if ($vd['address']->address) value="{{ $vd['address']->address }}" @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label" for="address2">{{ trans("header.supplier_fields.street_2") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" name="address2" placeholder="Enter Street" id="address2" class="form-control" @if ($vd['address']->address) value="{{ $vd['address']->address2 }}" @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label mandatory" for="country_id">{{ trans("header.supplier_fields.country") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="country_id" id="country_id" class="form-control">
                                            @if ($vd['country'])
                                                @foreach ($vd['country'] as $country)
                                                    <option value="{{$country->id}}" selected>{{$country->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label mandatory" for="state_id">{{ trans("header.supplier_fields.state") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="state_id" id="state_id" class="form-control">
                                            @if ($vd['city'])
                                                @foreach ($vd['state'] as $state)
                                                    <option value="{{$state->id}}" selected>{{$state->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label mandatory" for="city_id">{{ trans("header.supplier_fields.city") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="city_id" id="city_id" class="form-control">
                                            @if ($vd['city'])
                                                @foreach ($vd['city'] as $city)
                                                    <option value="{{$city->id}}" selected>{{$city->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label" for="phone">{{ trans("header.supplier_fields.phone") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" name="phone" placeholder="Enter Phone" id="phone" class="form-control" @if ($vd['address']->phone) value="{{ $vd['address']->phone }}" @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label" for="fax">{{ trans("header.supplier_fields.fax") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" name="fax" placeholder="Enter Street" id="fax" class="form-control" @if ($vd['address']->fax) value="{{ $vd['address']->fax }}" @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3 text-right">
                                        <label class="control-label" for="zip">{{ trans("header.supplier_fields.zip") }}</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" name="zip" placeholder="Enter Street" id="zip" class="form-control" @if ($vd['address']->zip) value="{{ $vd['address']->zip }}" @endif />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-offset-3 col-sm-9">
                                        <button class="btn btn-theme-red" id="btnSubmit" type="submit">{{ trans("button.add") }}</button>
                                        <button class="btn btn-theme-black go-list" type="button">{{ trans("button.cancel") }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> --}}

{{-- <section class="content">
	<div class="row" id="modelmdl">
		<div class="col-lg-12 col-md-12 col-sm-12">
            <div class="panel">
                <div class="panel-heading">
                    <h2 class="panel-title">
                        <div class="pull-left">
                            Edit Address
                        </div>
                        <div class="box-tools pull-right">
                            <button class="btn btn-link go-list" type="button">
                                {{ trans("header.supplier_fields.back_to_supplier") }}
                            </button>
                        </div>
                    </h2>
                </div>
                <div class="panel-body">
                    <div class="mar-top">
                        <form id="cab" method="POST" action="{{ url('supplier/address/edit/' . request()->supplier_id . '/' . request()->address_id) }}">
                            {{ csrf_field() }}
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}




{{-- @push('head')
    <link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
@endpush
@push('lib')
<script src="{!! CommonHelper::asset('newjs/select2.full.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('js/suppliers/address.js') !!}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".go-list").on("click", function(e) {
            window.location = "{{ url('supplier/info', request()->supplier_id) }}";
        });
    });
</script>

@endpush --}}
