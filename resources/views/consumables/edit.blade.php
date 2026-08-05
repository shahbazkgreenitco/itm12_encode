{{--
/**
* ------------------------------------------------------------
* File: edit.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-10
* Created On: 2026-01-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', 'Update Consumable')

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissable">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ session('success') }}
    </div>
@endif
<section class="content-header">
    <h1>
    Update Consumable
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-9">
            <form method="post" class="form-horizontal" accept-charset="UTF-8" action="">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title col-md-12">
                            Consumable Details
                        </h3>
                    </div>
                    <div class="box-body">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-4">Company</label>
                                <div class="col-md-8">
                                    <select name="parent_id" id="" class="form-control">
                                        <option value="">Select Company</option>
                                        {{--  @foreach($location->getLocations() as $loc)  --}}
                                            {{--  <option value="{{ $loc->id }}" @if($loc->id == old('parent_id')) selected @endif>{{ $loc->name }}</option>  --}}
                                        {{--  @endforeach  --}}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group  col-md-7">
                                <label for="name" class="control-label col-md-4">Consumable Name&nbsp;<i class="fa fa-asterisk"></i></label>
                                <div class="col-md-8">
                                <input type="text" name="name" class="form-control" placeholder="Software Name" value="{{ old('name') }}" >
                                    {!! $errors->first('name', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-4">Category&nbsp;<i class="fa fa-asterisk"></i></label>
                                <div class="col-md-8">
                                    <select name="parent_id" id="" class="form-control">
                                        <option value="">Select Category</option>
                                        {{--  @foreach($location->getLocations() as $loc)  --}}
                                            {{--  <option value="{{ $loc->id }}" @if($loc->id == old('parent_id')) selected @endif>{{ $loc->name }}</option>  --}}
                                        {{--  @endforeach  --}}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-4">Location</label>
                                <div class="col-md-8">
                                    <select name="parent_id" id="" class="form-control">
                                        <option value="">Select Location</option>
                                        {{--  @foreach($location->getLocations() as $loc)  --}}
                                            {{--  <option value="{{ $loc->id }}" @if($loc->id == old('parent_id')) selected @endif>{{ $loc->name }}</option>  --}}
                                        {{--  @endforeach  --}}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="group_id" class="control-label col-md-4">Licensed to Name</label>
                                <div class="col-md-8">
                                    <input type="text" name="address" class="form-control" placeholder="Licensed to Name" value="{{ old('address') }}" >
                                    {{--  {!! $errors->first('group_id', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}  --}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-4">Order No. </label>
                                <div class="col-md-8">
                                    <input type="text" name="city" class="form-control" placeholder="Order No." value="{{ old('city') }}" >
                                    {!! $errors->first('city', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-4">Purchase Date</label>
                                <div class="col-md-8">
                                    <input type="text" name="state" class="form-control" placeholder="Purchase Date" value="{{ old('state') }}" >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-4">Purchase Cost</label>
                                <div class="col-md-8">
                                    <input type="text" name="zip" class="form-control" placeholder="Purchase Cost" value="{{ old('zip') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-4">Termination Date</label>
                                <div class="col-md-8">
                                    <input type="text" name="state" class="form-control" placeholder="Termination Date" value="{{ old('state') }}" >
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-theme-red">Save</button>
                            <a class="btn btn-link" href="{{ url('locations') }}">@lang('button.cancel')</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-3">
            <div class="row">
                <h2>About Consumables</h2>
                <p class="text-justify">Consumables are anything purchased that will be used up over time. For example, printer ink or copier paper. </p>
            </div>
        </div>
    </div>
</section>
@endsection 