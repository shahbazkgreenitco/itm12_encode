{{--
/**
* ------------------------------------------------------------
* File: add.blade.php
* Module: User Locations
* UL/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #UL-001
* Created On: 2026-04-29
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout3')
@section('title', 'Add Location')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('success') }}
        </div>
    @endif
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <form method="post" class="form-horizontal" accept-charset="UTF-8" action="" autocomplete="off">
                    <div class="panel">
                        <div class="panel-heading with-border">
                            <h3 class="panel-title">
                                Add Location Details
                            </h3>
                        </div>
                        <div class="panel-body">
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="form-group  col-md-7">
                                    <label for="name" class="control-label col-md-3 mandatory">Location Name</label>
                                    <div class="col-md-9">
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Location Name" value="{{ old('name') }}">
                                        {!! $errors->first('name', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="is_enabled" class="control-label col-md-3">Parent</label>
                                    <div class="col-md-9">
                                        <select name="parent_id" id="" class="form-control">
                                            <option value="">Select Parent</option>
                                            @foreach ($location->getLocations() as $loc)
                                                <option value="{{ $loc->id }}"
                                                    @if ($loc->id == old('parent_id')) selected @endif>{{ $loc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="is_enabled" class="control-label col-md-3">Location Currency</label>
                                    <div class="col-md-9">
                                        <select name="currency" id="" class="form-control">
                                            <option value="">Select Currency</option>
                                            @foreach (App\Models\Currency::getCurrencies() as $key => $value)
                                                <option value="{{ $key }}"
                                                    @if ($key == old('currency')) selected @endif>
                                                    {{ $value['name'] . ' (' . $value['symbol_html'] . ')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="group_id" class="control-label col-md-3 mandatory">Street 1</label>
                                    <div class="col-md-9">
                                        <input type="text" name="address" class="form-control" placeholder="Street 1"
                                            value="{{ old('address') }}">
                                        {!! $errors->first('address', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="is_enabled" class="control-label col-md-3">Street 2</label>
                                    <div class="col-md-9">
                                        <input type="text" name="address2" class="form-control" placeholder="Street 2"
                                            value="{{ old('address2') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="is_enabled" class="control-label col-md-3 mandatory">City</label>
                                    <div class="col-md-9">
                                        <input type="text" name="city" class="form-control" placeholder="City"
                                            value="{{ old('city') }}">
                                        {!! $errors->first('city', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="is_enabled" class="control-label col-md-3 mandatory">State</label>
                                    <div class="col-md-9">
                                        <input type="text" name="state" class="form-control" placeholder="State"
                                            value="{{ old('state') }}">
                                        {!! $errors->first('state', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="is_enabled" class="control-label col-md-3 mandatory">Country</label>
                                    <div class="col-md-9">
                                        <select name="country" id="" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($location->getCountries() as $country)
                                                <option value="{{ $country->country_code }}"
                                                    @if (old('country') == $country->country_code) selected @endif>{{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('country', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-7">
                                    <label for="is_enabled" class="control-label col-md-3">Zip/Postal Code</label>
                                    <div class="col-md-9">
                                        <input type="text" name="zip" class="form-control"
                                            placeholder="Zip/Postal Code" value="{{ old('zip') }}">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-theme-red">{{ trans('button.save') }}</button>
                            <a class="btn btn-link" href="{{ url('locations') }}">@lang('button.cancel')</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
