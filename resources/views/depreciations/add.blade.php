{{--
/**
* ------------------------------------------------------------
* File: add.blade.php
* Module: Depreciation
* DEP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEP-001
* Created On: 2026-04-29
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout3')
@section('title', 'Add Depreciation')

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
            <form method="post" class="form-horizontal" accept-charset="UTF-8" action="">
                <div class="panel">
                    <div class="panel-heading with-border">
                        <h3 class="panel-title">
                            Add Depreciation Details
                        </h3>
                    </div>
                    <div class="panel-body">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="form-group  col-md-7">
                                <label for="name" class="control-label col-md-4 mandatory">Depreciation Name</label>
                                <div class="col-md-6">
                                        <input class="form-control" type="text" name="name" id="name"
                                            value="{{ count($errors) ? old('name') : '' }}">
                                    {!! $errors->first('name', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group  col-md-7">
                                <label for="name" class="control-label col-md-4 mandatory">Number of Months</label>
                                <div class="col-md-6">
                                        <input class="form-control" type="text" name="months" id="months"
                                            value="{{ count($errors) ? old('months') : '' }}">
                                    {!! $errors->first('months', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                            <button type="submit" class="btn btn-theme-red">{{ trans('button.save') }}</button>
                        <a class="btn btn-link" href="{{ url('depreciations') }}">@lang('button.cancel')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
