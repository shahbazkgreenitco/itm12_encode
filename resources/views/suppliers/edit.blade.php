{{--
/**
* ------------------------------------------------------------
* File: edit.blade.php
* Module: Suppliers
* SUP/26/04
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #SUP-007
* Created On: 2026-04-28
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

@extends('layouts.layout3')
@section('title', trans('header.supplier_fields.edit_supplier'))

@section('content')
@if (session('success'))
<div class="alert alert-success alert-dismissable">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{ session('success') }}
</div>
@endif
<section class="content">
    <div class="row user-mdl-box">
        <div class="col-lg-12">
            <form method="post" class="form-horizontal" id="SupplierForm" accept-charset="UTF-8" action=""
                onsubmit="return frmValidator.form()" enctype="multipart/form-data" autocomplete="off">
                <div class="panel">
                    <div class="panel-heading with-border">
                        <h3 class="panel-title">
                            {{ trans('header.supplier_fields.edit_supplier') }}
                            <div class="box-tools pull-right">
                                {{-- <a class="btn btn-link" href="{{ url('suppliers') }}">
                                <i class="fa fa-mail-reply"></i> {{ trans("header.supplier_fields.back") }}
                                </a> --}}
                            </div>
                        </h3>
                    </div>
                    <div class="panel-body">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3 mandatory">{{ trans('header.supplier_fields.add_supplier_details') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Enter Supplier Name"
                                            value="{{ count($errors) ? old('name') : $supplier->name }}">
                                    </div>
                                    {!! $errors->first('name', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.business_category') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-building-o"></i>
                                        </div>
                                        <select name="business_category[]" id="business_category"
                                            class="form-control select2" multiple="multiple">
                                            @if (!empty($supplier->supplier_bussiness_categories))
                                            @foreach ($supplier->supplier_bussiness_categories as $category)
                                            <option value="{{ $category['id'] }}" selected="selected">
                                                {{ $category['business_tag'] }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    {!! $errors->first('country', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="contact"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.contact_person_name') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                        <input type="text" name="contact" id="contact" class="form-control"
                                            placeholder="Enter Contact Person Name" value="{{ $supplier->contact }}">
                                        {!! $errors->first('contact', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.street_1') }}</label>

                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-street-view"></i></div>
                                        <input type="text" name="address" id="address" class="form-control"
                                            placeholder="Enter Street 1" value="{{ $supplier->address }}">
                                        {!! $errors->first('address', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.street_2') }}</label>

                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-street-view"></i></div>
                                        <input type="text" name="address2" id="address2" class="form-control"
                                            placeholder="Enter Street 2" value="{{ $supplier->address2 }}">
                                        {!! $errors->first('address2', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="country_id"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.country') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-map-marker"></i>
                                        </div>
                                        <select name="country_id" id="country_id" class="form-control">
                                            @if (!empty($supplier->country_data))
                                            @foreach ($supplier->country_data as $country)
                                            <option value="{{ $country['id'] }}" selected="selected">
                                                {{ $country['name'] }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="state_id"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.state') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-location-arrow"></i>
                                        </div>
                                        <select name="state_id" id="state_id" class="form-control">
                                            @if (!empty($supplier->state_data))
                                            @foreach ($supplier->state_data as $state)
                                            <option value="{{ $state['id'] }}" selected="selected">
                                                {{ $state['name'] }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="city_id"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.city') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-building"></i>
                                        </div>
                                        <select name="city_id" id="city_id" class="form-control">
                                            @if (!empty($supplier->city_data))
                                            @foreach ($supplier->city_data as $city)
                                            <option value="{{ $city['id'] }}" selected="selected">
                                                {{ $city['name'] }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.zip_postal_code') }}</label>

                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-map-pin"></i></div>
                                        <input type="text" name="zip" id="zip" class="form-control"
                                            placeholder="Zip/Postal Code" value="{{ $supplier->zip }}">
                                        {!! $errors->first('zip', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.phone') }}</label>

                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-mobile"></i></div>
                                        <input type="text" name="phone" id="phone" class="form-control"
                                            placeholder="Enter Phone" value="{{ $supplier->phone }}">
                                        {!! $errors->first('phone', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.email_address') }}</label>

                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"> @ </div>
                                        <input type="text" name="email" id="email" class="form-control"
                                            placeholder="Enter Email Address" value="{{ $supplier->email }}">
                                        {!! $errors->first('email', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.pan') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-credit-card"></i></div>
                                        <input type="text" name="pan" id="pan" class="form-control"
                                            placeholder="Enter PAN" value="{{ $supplier->pan }}">
                                        {!! $errors->first(
                                        'email',
                                        '
                                        <label class="error">
                                            <i class="fa fa-times"></i> :message</label>',
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.account_number') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-book"></i></div>
                                        <input type="text" name="bank_acc_number" id="bank_acc_number"
                                            class="form-control" placeholder="Enter Account Number"
                                            value="{{ $supplier->bank_acc_number }}"> {!! $errors->first(
                                        'phone',
                                        '
                                        <label class="error">
                                            <i class="fa fa-times"></i> :message</label>',
                                        ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.bank_name') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-bank"></i></div>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control"
                                            placeholder="Enter Bank Name" value="{{ $supplier->bank_name }}">
                                        {!! $errors->first('phone', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.ifsc_code') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                        <input type="text" name="bank_ifsc" id="bank_ifsc" class="form-control"
                                            placeholder="Enter IFSC Code" value="{{ $supplier->bank_ifsc }}">
                                        {!! $errors->first('phone', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.branch_name') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                        <input type="text" name="bank_branch" id="bank_branch"
                                            class="form-control" placeholder="Enter Branch Name"
                                            value="{{ $supplier->bank_branch }}">
                                        {!! $errors->first('phone', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.account_name_per_bank_account') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-file-o"></i></div>
                                        <input type="text" name="bank_acc_name" id="bank_acc_name"
                                            class="form-control" placeholder="Enter Account Name as Per Bank Account"
                                            value="{{ $supplier->bank_acc_name }}">
                                        {!! $errors->first('phone', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.website_URL') }}</label>

                                <div class="col-md-9">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-internet-explorer"></i></div>
                                        <input type="text" name="url" id="url" class="form-control"
                                            placeholder="Enter Website URL" value="{{ $supplier->url }}">
                                        {!! $errors->first('url', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name"
                                    class="control-label col-md-3">{{ trans('header.supplier_fields.notes') }}</label>

                                <div class="col-md-9">
                                    {{-- <input type="text" name="name" id="name" class="form-control" placeholder="Enter Supplier Name" value="{{ $supplier->name }}"> --}}
                                    <textarea name="notes" id="notes" class="form-control">{{ $supplier->notes }}</textarea>
                                    {!! $errors->first('notes', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <?php $attachments = $supplier->getSupplierAttachments($supplier->id); ?>
                        @if (count($attachments))
                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name" class="control-label col-md-3">Images</label>
                                <div class="col-md-9">
                                    <ul class="list-group">
                                        @foreach ($attachments as $attachment)
                                        <li class="list-group-item">
                                            <span>
                                                <a target="_blank"
                                                    href="{{ url('supplier-image-download/' . $attachment->file_name) }}">{{ $attachment->original_file_name }}</a>
                                            </span>
                                            <span class="badge bg-red">
                                                <a href="{{ url('supplier-image-delete/' . $attachment->file_name) }}"
                                                    class="del-link">
                                                    {{-- <span class="pull-right-container">  --}}
                                                    {{-- <small class="label pull-right bg-red">Delete</small>  --}}
                                                    {{-- </span>  --}}
                                                    Del
                                                </a>
                                            </span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="form-group  col-md-12">
                                <label for="name" class="control-label col-md-3">Upload Image</label>
                                <div class="col-md-9">
                                    {{-- <input type="text" name="name" id="name" class="form-control" placeholder="Enter Supplier Name" value="{{ $supplier->name }}"> --}}
                                    <input type="file" name="image">
                                    {!! $errors->first('image', '<label class="error"><i class="fa fa-times"></i> :message</label>') !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-theme-red">{{ trans('button.save') }}</button>
                        <a class="btn btn-theme-black" id="cancel"
                            href="{{ url('suppliers') }}">@lang('button.cancel')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('head')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.css') !!}" rel="stylesheet" />
@endpush

@push('lib')
<script src="{!! CommonHelper::asset('newjs/select2.full.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('nfy/plugins/summernote/summernote-bs5.min.js') !!}"></script>
<script>
    var config = new Object;
    config.url = new Object;
    config.url.country = "{{ url('getCountryByQuery') }}";
    config.url.state = "{{ url('fetchStateByAjax') }}";
    config.url.city = "{{ url('fetchCityByAjax') }}";
    config.token = "{{ csrf_token() }}";
    config.translations = {
        select_country: '{{ trans('    header.location_fields.select_country ') }}',
        select_state: '{{ trans('    header.location_fields.select_state ') }}',
        select_city: '{{ trans('    header.location_fields.select_city ') }}',
    }
    frm = $("#SupplierForm");
    frmValidator = frm.validate({
        onsubmit: false,
        rules: {
            name: {
                required: true,
                maxlength: 255,
                str_name: true
            },
            'business_category[]': {
                clean_text_only: true,
            },
            contact: {
                maxlength: 100,
                str_name: true
            },
            address: {
                maxlength: 75,
                str_address: false,
                clean_text_only: true
            },
            address2: {
                maxlength: 75,
                str_address: false,
                clean_text_only: true
            },
            city: {
                maxlength: 255,
                str_address: true
            },
            state: {
                maxlength: 255,
                str_address: true
            },
            zip: {
                maxlength: 200,
                number: true
            },
            phone: {
                maxlength: 10,
                number: true
            },
            email: {
                maxlength: 150,
                email: true
            },
            pan: {
                str_name: true
            },
            bank_acc_number: {
                number: true
            },
            bank_name: {
                str_name: true
            },
            bank_ifsc: {
                str_name: true
            },
            bank_branch: {
                str_name: true
            },
            bank_acc_name: {
                str_name: true
            },
            url: {
                maxlength: 250,
                remarks: true
            },
            notes: {
                maxlength: 255,
                remarks: true
            },
            'business_category[]': {
                clean_text_only: true
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") === "notes") {
                error.appendTo(element.parent());
            } else {
                error.appendTo(element.parent().parent());
            }
        }
    });

    $('#notes').summernote({
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        placeholder: 'Enter notes here...',
    });

    $('#business_category').select2({
        width: "100%",
        placeholder: "Business Category",
        tags: true,
        tokenSeparators: [','],
        ajax: {
            url: function(params) {
                return "{{url('getBusinessCategories')}}";
            },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page,
                }
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            },
            cache: true
        }
    });

    function initializeStateSelect() {
        $('#state_id').select2({
            width: "100%",
            placeholder: config.translations.select_state,
            ajax: {
                url: config.url.state,
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                        country_id: $('#country_id').val(),
                    };
                },
                delay: 300
            },
            allowClear: true,
        }).on('change', function() {
            $('#city_id').val('').trigger('change');
            initializeCitySelect();
        });
    }

    function initializeCitySelect() {
        $('#city_id').select2({
            width: "100%",
            placeholder: config.translations.select_city,
            ajax: {
                url: config.url.city,
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                        state_id: $('#state_id').val(),
                    };
                },
                delay: 300
            },
            allowClear: true,
        });
    }

    $('#country_id').select2({
        width: "100%",
        placeholder: config.translations.select_country,
        ajax: {
            url: config.url.country,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
    }).on('change', function() {
        $('#state_id').val('').trigger('change');
        $('#city_id').val('').trigger('change');
        initializeStateSelect();
        initializeCitySelect();
    });
    initializeStateSelect();
    initializeCitySelect();
</script>
@endpush