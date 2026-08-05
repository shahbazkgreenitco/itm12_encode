{{--
/**
* ------------------------------------------------------------
* File: index.blade.php
* Module: Device Status Labels
* DEVSL/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEVSL-004
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', trans('config.location_fields.locations'))

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel box">
                    <div class="panel-heading box-header with-border">
                        <h3 class="panel-title box-title">
                            <div class="pull-left">
                                Status Labels
                            </div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-link go-add" data-toggle="modal"
                                    data-target="#popup-create-companies" data-href="{{ url('status-label/add') }}">
                                    <i class="fa fa-plus"></i> Create Status Label
                                </button>
                            </div>
                        </h3>
                    </div>
                    <div class="panel-body box-body table-responsive">
                        <table class="table table-striped" id="mytable">
                            <thead>
                                <tr>
                                    <th style="padding-left:23px;">Actions</th>
                                    <th>Status Label</th>
                                    <th>Status Type</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($objLabel::all() as $lbl)
                                    <tr>
                                        @if ($lbl->id != 1 && $lbl->deployed == 0)
                                            <td>
                                                <button class="btn dtActbtn go-edit" style="margin-left:10px;"
                                                    data-toggle="tooltip" data-original-title="Edit Status Label"
                                                    data-id="{{ $lbl->id }}"><i class="fa fa-pencil"></i></button>
                                                <button class="btn dtActbtn dtActDel" data-toggle="tooltip"
                                                    data-original-title="Delete Status Label"
                                                    data-id="{{ $lbl->id }}"><i class="fa fa-trash"></i></button>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                        <td>{{ $lbl->name }}</td>
                                        <td>{{ ucwords($lbl->getStatuslabelType()) }}</td>
                                        <td>{{ $lbl->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('head')
    @include('includes.csslib')
@endpush

@push('lib')
    @include('includes.jslib')
    <script type="text/javascript">
        var config = new Object;
        config.url = new Object;
        config.url.add = "{{ url('status-label/add') }}";
        config.url.edit = "{{ url('status-label/edit') }}";
        config.url.delete = "{{ url('status-label/delete') }}";
        config.token = "{{ csrf_token() }}";
    </script>
    <script src="{!! CommonHelper::asset('js/status-labels/list.js') !!}"></script>
@endpush
