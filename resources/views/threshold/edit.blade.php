@extends('layouts.layout3')
@section('title', trans("header.threshold_setting_fields.edit_threshold"))

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissable">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ session('success') }}
    </div>
@endif
<section class="content">
    <div class="row" id="threshold">
        <div class="col-lg-12">
            <form method="post" class="form-horizontal" id="thresholdFrm" accept-charset="UTF-8" action="">
                <div class="panel">
                    <div class="panel-heading with-border">
                        <h3 class="panel-title col-md-12">
                            {{ trans("header.threshold_setting_fields.edit_threshold") }}
                        </h3>
                    </div>
                    <div class="panel-body">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group  col-md-7">
                                <label for="name" class="control-label col-md-3">{{ trans("header.threshold_setting_fields.threshold_module") }}&nbsp;</label>
                                <div class="col-md-9">
                                    <label>
                                        <input @if($TS->threshold_enabled) checked @endif  name="threshold_enabled" type="checkbox" value="1" id="threshold_enabled" onchange="$(this).prop('checked') ? $('.thre').show() : $('.thre').hide()" >
                                        Enabled
                                    </label>
                                    {!! $errors->first('name', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="row thre" style="display:<?php echo ((count($errors) ? old('threshold_enabled') : $TS->threshold_enabled) ? 'block' : 'none'); ?>;">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-3">{{ trans("header.threshold_setting_fields.alert_notification") }}</label>
                                <div class="col-md-9">
                                    <label>
                                        <input @if(count($errors) ? old('alerts_enabled') : $TS->alerts_enabled) checked @endif name="alerts_enabled" type="checkbox" value="1" id="alerts_enabled" onchange="$(this).prop('checked') ? $('.alrt').show() : $('.alrt').hide()">
                                        Enabled
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row thre alrt" style="display:<?php echo ((count($errors) ? old('alerts_enabled') : $TS->alerts_enabled && $TS->threshold_enabled) ? 'block' : 'none'); ?>;">
                            <div class="form-group col-md-7">
                                <label for="address" class="control-label col-md-3">{{ trans("header.threshold_setting_fields.send_alerts_to") }}</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="send_alerts" name="send_alerts" onchange="$('#send_alerts').val() == '1' ? $('.email').show() : $('.email').hide()">
                                        <option value="0" @if( ( count($errors) ? old('send_alerts') : $TS->send_alerts ) == '0') selected @endif >As per General Settings Alerts Configuration</option>
                                        <option value="1" @if(  ( count($errors) ? old('send_alerts') : $TS->send_alerts ) =='1') selected @endif >Following Email Address</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row thre alrt" style="display:<?php echo ((count($errors) ? old('alerts_enabled') : $TS->alerts_enabled && $TS->threshold_enabled) ? 'block' : 'none'); ?>;">
                            <div class="form-group col-md-7">
                                <label for="is_enabled" class="control-label col-md-3"></label>
                                <div class="col-md-9">
                                    <input class="form-control email" placeholder="Enter Email Address" value="{{ count($errors) ? old('email') : $TS->email }}" id="email" name="email" type="text" style="display:@if($TS->send_alerts=='1' || old('send_alerts')=='1')block @else none @endif ;">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-theme-red" id="btnSubmit">{{ trans("button.save") }}</button>
                        <a class="btn btn-theme-black" href="{{ url('threshold') }}">@lang('button.cancel')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('lib')
<script src="{!! CommonHelper::asset('js/threshold/index.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function(){
    var config = new Object;
	config.url = new Object;
	config.token = "{{ csrf_token() }}";
	new Threshold(config); 
});
</script>
@endpush