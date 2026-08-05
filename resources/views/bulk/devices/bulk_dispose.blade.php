@extends('layouts.layout3')
@section('title', trans("content.device_fields.bulk_dispose"))

@section('content')
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="box panel tab-bar" style="border-top: none; margin-bottom: 6px;">
                <div class="panel-body pad-no">
                    <div class="pull-left">
                        <ul class="black-slide black-slide-links">
                            <li class="active"><a href="#info-tab">{{ trans("content.device_fields.bulk_dispose") }}</a></li>
                            <li><a href="#check-out-info-tab">{{ trans("content.device_fields.dispose_information") }}</a></li>
                        </ul>            
                    </div>
                    <div class="pull-right">
                        <ul class="black-slide-links">
                            <li><button onclick="location.href='{{ url('devices') }}';" class="btn btn-theme-black " id="back-btn" data-toggle="tooltip" data-original-title="{{ trans("content.tab_header.back") }}"> <i class="fa fa-mail-reply"></i></button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading with-border">
            <h3 class="panel-title">
                <span>{{ trans("content.device_fields.bulk_dispose") }}</span>
            </h3>
        </div>
        <div class="panel-body" style="padding: 0 0 1px 0">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 black-no-right">
                    <div class="black-slide-view active" id="info-tab">
                        <div class="row">
                            <div class="col-md-7">
                                <form name="confirmForm" id="confirmForm" class="form-horizondal" method="post" action="{{ url('devices/bulk/dispose') }}"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="form-group  col-md-12">
                                            <label for="name" class="control-label col-md-3">{{ trans("content.download_format.label_import") }}</label>
                                                <div class="input-group">
                                                    <input type="file" id="import_file" name="import_file" accept=".xlsx" class="form-control"/>
                                                </div>
                                        </div>
                                    </div>

                                    <div class="row mar-top">
                                        <div class="col-md-offset-3 col-md-12">
                                            <input type="submit" name="submitbtn" class="btn btn-theme-red" value="{{ trans("content.device_fields.bulk_dispose") }}" />
                                            <a class="btn btn-link" target="_blank" href="{!! CommonHelper::asset('samples/DeviceBulkDisposeFormat.xlsx') !!}">{{ trans("content.common_doc_list.download_format") }}</a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-12">
                                @if(isset($success) && $success)
                                <div class="text-success">
                                    <p> Device checked in successfully ({{ $success }} record). </p>
                                </div>
                                @endif
                                @if(isset($fail) && $fail)
                                <div class="text-red">
                                    <p>Failure records count: {{ $fail }}</p>
                                </div>
                                @endif
                                @if(isset($fail_msgs) && count($fail_msgs))
                                <p>Failure Records</p>
                                <ul class="red">
                                    @foreach($fail_msgs as $m)
                                    <li>{{ $m }}</li>
                                    @endforeach
                                </ul>
                                @endif
                                @if(isset($given_file_original_name) && $given_file_original_name)
                                <div>
                                <p>File name: <span class="action-line actionDownload "><a download = "{{ $given_file_original_name}}" href="{{ url('uploads/bulk_documents/',[$doc_link]) }}" >{{ $given_file_original_name }}</a></span></p>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="mar-top">{{ trans("content.download_format.data_sheet_validation") }}:</p>
                                <p class="">{{ trans("content.download_format.never_change") }}</p>
                                <ul>
                                    <li><strong>{{ trans("content.report_fields.dispose_by") }}<span style="color: #cc0000;">*</span></strong> {{ trans("content.download_format.assigned_to_con_field") }} </li>
                                    <li><strong>{{ trans("content.report_fields.date") }}<span style="color: #cc0000;">*</span></strong> {{ trans("content.download_format.invoice_date_field") }}</li>
                                    <li><strong>{{ trans("content.download_format.serial") }} <span style="color: #cc0000;">*</span></strong> {{ trans("content.download_format.serial_field_update") }} </li>
                                    <li><strong>{{ trans("content.report_fields.dispose_type") }} <span style="color: #cc0000;">*</span></strong> {{ trans("content.device_fields.dispose_type") }} </li>
                                    <li><strong>{{ trans("content.download_format.notes_usr_up") }}<span style="color: #cc0000;">*</span></strong> {{ trans("content.download_format.notes") }}</li>
                                    <br>
                                    <strong><i>{{ trans("content.device_fields.dispose_information") }}</i>:</strong>
                                    <li><i>{{ trans("content.device_fields.serial_no") }}</i></li> 
                                    <li><i>{{ trans("content.device_fields.reference_no") }}</i></li> 
                                    <li><i>{{ trans("content.device_fields.currency_cost") }}</i></li> 
                                    <li><i>{{ trans("content.device_fields.organization_name") }}</i></li> 
                                    <li><i>{{ trans("content.device_fields.vendor") }}</i></li>
                                </ul>
                                <p>
                                    <i>{{ trans("content.download_format.nvr_ch") }}.</i>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="black-slide-view" id="check-out-info-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="tblHistory" class="mytable table gray_thead">
                                        <thead>
                                            <tr>
                                                <th>{{ trans("content.common_doc_list.actions") }}</th>
                                                <th>{{ trans("content.common_doc_list.document_name") }}</th>
                                                <th>{{ trans("content.common_doc_list.total_upload") }}</th>
                                                <th>{{ trans("content.common_doc_list.total_success") }}</th>
                                                <th>{{ trans("content.common_doc_list.total_failure") }}</th>
                                                <th>{{ trans("content.common_doc_list.updated_at") }}</th>
                                                <th>{{ trans("content.common_doc_list.user_name") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push("head")
@include('includes.csslib')
<style>
    .black-slide-links {
        margin: 0px;
    }
    .black-slide-links li button.btn {
        padding: 16px 22px;
        font-size: 12px;
        display: inline-block;
        margin: 0;
        min-width: 50px;
    }
</style>
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
@endpush


@push('lib')
@include('includes.jslib')
<script src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/bulk/devices/bulk_dispose.js') !!}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var config = {};
        config.bulk_dispose_info = "{{ url('devices/bulk/dispose-info') }}";
        config.document_download = "{{ url('uploads/bulk_documents') }}";
        config.token = "{{ csrf_token() }}";
        config.translations = {
			Download_Document: '{{ trans('content.device_fields.Download_Document') }}',
			reload: '{{ trans('content.device_fields.reload') }}',
            press_enter_with_Search:'{{ trans('content.device_fields.press_enter_with_Search') }}',
            Please_upload_valid_xlsx: '{{ trans('content.device_fields.Please_upload_valid_xlsx') }}',
            search: '{{ trans('content.device_fields.search') }}',
        };
        new MyApp(config);
    });
</script>
@endpush