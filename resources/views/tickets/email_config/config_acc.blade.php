@extends('layouts.layout3')
@section('title', 'Email Scheduled Blocker')

@section('content')
<section class="content">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel box">
				<div class="panel-heading box-header with-border">
					<h3 class="panel-title box-title">
						<div class="pull-left">
							Email Scheduled Blocker
						</div>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-theme-red open-acc-add-modal">
								<i class="fa fa-plus"></i> Add
							</button>
						</div>
					</h3>
				</div>
				<div class="panel-body box-body table-responsive">
					<div class="row">
						<div class="col-lg-12">
							<table id="mytable" class="mytable table table-striped">
								<colgroup>
									<col>
									<col>
									<col>
									<col>
									<col style="width: 200px;">
									<col>
								</colgroup>
								<thead>
									<tr>
										<th>Actions</th>
										<th>Email</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Accounts</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="col-lg-4 hide">
								<div id="dt" name="dt"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@include('tickets.email_config.acc_modal_html_view')
	@include('tickets.email_config.acc_modal_html')
</section>
@endsection

@push('head')
<style>
	#img {
		font-size: 20px;
	}
	.t1{
		padding-left:10px;
	}
</style>
@include('includes.csslib')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
<link href="{!! CommonHelper::asset('plugins/bootstrap_datetimepicker/css/bootstrap-datetimepicker.min.css') !!}" rel="stylesheet" />
@endpush

@push('lib')
@include('includes.jslib')
<script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/email_config/index.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('plugins/bootstrap_datetimepicker/js/bootstrap-datetimepicker.min.js') !!}"></script>
<script src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
<script type="text/javascript">
	var config = new Object;
	config.url = new Object;
	config.url.email_config_list = "{{ url('tickets/scheduled-block-list') }}";
	config.auto_creation_acc = {!! json_encode($vd->auto_creation_acc) !!};
	config.acc_status = {!! json_encode($vd->acc_status) !!};
	config.url.add_block = "{{ url('tickets/ajax-add-scheduled-block') }}";
	config.url.get = "{{ url('tickets/ajax-get-scheduled-block') }}";
	config.url.edit = "{{ url('tickets/ajax-edit-scheduled-block') }}";
	config.url.delete = "{{ url('tickets/ajax-delete-scheduled-block') }}";
	config.url.force_complete = "{{ url('tickets/ajax-auto-acc-force-complete') }}";
	config.token = "{{ csrf_token() }}";
  new EmailAccountList(config);
</script>

@endpush