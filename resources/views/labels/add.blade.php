{{--
/**
* ------------------------------------------------------------
* File: add.blade.php
* Module: Device Status Labels
* DEVSL/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEVSL-001
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout3')
@section('title', 'Add Label')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session('success') }}
        </div>
    @endif
    <section class="content">
        <div class="row" id="label">
            <div class="col-lg-12">
                <form method="post" class="form-horizontal" id="labelFrm" accept-charset="UTF-8" action="">
                    <div class="panel">
                        <div class="panel-heading with-border">
                            <h3 class="panel-title">
                                Add Label Details
                            </h3>
                        </div>
                        <div class="panel-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group  col-md-9">
                                    <label class="control-label col-md-4 mandatory" for="name">Status Label</label>
                                    <div class="col-md-8">
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Label Name" value="{{ count($errors) ? old('name') : '' }}">
                                        {!! $errors->first('name', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-9">
                                    <label for="category_type" class="control-label col-md-4">Status Type</label>
                                    <div class="col-md-8">
                                        <select name="status_type" id="status_type" class="form-control">
                                            <option value=" ">Select Status Type</option>
                                            @foreach ($objLabel->statusTypes as $st)
                                                <option value="{{ $st }}">{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-9">
                                    <label for="notes" class="control-label col-md-4">Notes</label>
                                    <div class="col-md-8">
                                        <textarea name="notes" id="notes" class="form-control" placeholder="Notes"></textarea>
                                        {!! $errors->first('status_type', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" id="btnSubmit"
                                class="btn btn-theme-red">{{ trans('button.save') }}</button>
                            <a class="btn btn-link" href="{{ url('status-labels') }}">@lang('button.cancel')</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@push('lib')
    <script src="{!! CommonHelper::asset('newjs/select2.full.min.js') !!}"></script>
    <script src="{!! CommonHelper::asset('js/status-labels/index2.js') !!}"></script>
    <script>
        $('.select2').select2({
            width: "100%"
        });
    </script>
    <script type="text/javascript">
        var config = new Object;
        config.url = new Object;
        config.url.add = "{{ url('status-label/add') }}";
        config.token = "{{ csrf_token() }}";
    </script>
@endpush
@push('head')
    <link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
@endpush
