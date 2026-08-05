{{--
/**
* ------------------------------------------------------------
* File: checkout_confirm.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-06
* Created On: 2026-01-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout1')
@section('title', 'Consumable Checkout Confirmation')

@section('content')
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">
                <span>Consumable Checkout Confirmation</span>
            </h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-7">
                    <p>Consumable Name: {{ $consumable->name }}</p>
                    @if($consumable->category)
                    <p>Category: {{ $consumable->category->name }}</p>
                    @endif
                    @if($consumable->company)
                    <p>Company: {{ $consumable->company->name }}</p>
                    @endif
                </div>
                {{--  <div class="col-md-5">
                    @if($device->image)
                    <img src="{{ url('uploads/devices/' . $device->image) }}" style="max-width:250px;max-height:250px;"/>
                    @endif
                </div>  --}}
            </div>
            <div class="row">
                <div class="col-md-7">
                    <form name="confirmForm" id="confirmForm" class="form-horizondal" method="post" action="{{ url('accept-consumable/'.$log->id) }}">
                        {{ csrf_field() }}
                        <div>
                            <label>Please acknowledge</label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="confirm" value="1" /> Yes. I have received this consumable.
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="confirm" value="0" /> No. I have not recevied this consumable.
                            </label>
                        </div>
                        <div>
                            <input type="submit" name="submitbtn" class="btn btn-theme-red" value="Confirm" />
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection