@extends('layouts.layout3')
@section('title', 'Email Based Ticket Generation')

@section('content')
<section class="content" >
	<div class="row">
		<div class="col-lg-12">
			<div class="panel box">
				<div class="panel-heading box-header with-border lite-clr-btn-border">
					<h3 class="panel-title box-title">
						<div class="pull-left">
							{{ trans("content.service_ticket_fields.email_based_ticket_generation") }}
						</div>
						<div class="box-tools pull-right">
							<button type="button" class="open-add-modal btn btn-default btn-white">
								<i class="fa fa-plus"></i> {{ trans("content.service_ticket_fields.Add_Account") }}
							</button>
							<button class="btn btn-default btn-white go-back-config">
								<i class="fa fa-mail-reply"></i> {{ trans("content.service_ticket_fields.Go_Config") }}
							</button>
						</div>
					</h3>
				</div>
				<div class="panel-body box-body table-responsive">
					<div class="row">
						<div class="col-lg-12">
							<table id="mytable" class="mytable table table-striped">
								<thead>
									<tr>
										<th>{{ trans("content.service_ticket_fields.Actions") }}</th>
                    					<th>{{ trans("content.service_ticket_fields.Email_Account") }}</th>
										<th>{{ trans("content.service_ticket_fields.Email_Service_Status") }}</th>
										<th>{{ trans("content.service_ticket_fields.email_host") }}</th>
										<th>{{ trans("content.service_ticket_fields.Email_Port") }}</th>
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

			<div id="alias_account_panel" class="panel box">
				<div class="panel-heading box-header with-border lite-clr-btn-border">
					<h3 class="panel-title box-title">
						<div class="pull-left">
							{{ trans("content.service_ticket_fields.Alias_Accounts") }}
						</div>
						<div class="box-tools pull-right">
							<button type="button" class="open-alias-add-modal btn btn-default btn-white">
								<i class="fa fa-plus"></i> {{ trans("content.service_ticket_fields.Add_Account") }}
							</button>
							<button class="btn btn-default btn-white go-back-config">
								<i class="fa fa-mail-reply"></i> {{ trans("content.service_ticket_fields.Go_Config") }}
							</button>
						</div>
					</h3>
				</div>
				<div class="panel-body box-body table-responsive">
					<table id="alias_accounts" class="alias_accounts table table-striped">
						<thead>
							<tr>
								<th>{{ trans("content.service_ticket_fields.Actions") }}</th>
								<th>{{ trans("content.service_ticket_fields.Alias_Accounts") }}</th>
								<th>{{ trans("content.service_ticket_fields.Status") }}</th>
								<th>{{ trans("content.service_ticket_fields.Departments") }}</th>
								<th>{{ trans("content.service_ticket_fields.Problem_Category") }}</th>
								<th>{{ trans("content.service_ticket_fields.sub_category") }}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	@include('tickets.email_config.modal_html')
	@include('tickets.email_config.modal_html_view')
	@include('tickets.email_config.modal_alias_account')
</section>
@endsection

@push('head')
<style>
	#img {
		font-size: 20px;
	}
	.t1 {
		padding-left:10px;
	}
	.btn-white a:hover {
		color: #fff !important;
	}
</style>
@include('includes.csslib')
<link href="{!! CommonHelper::asset('newcss/select2.min.css') !!}" rel="stylesheet" />
@endpush

@push('lib')
@include('includes.jslib')
<script type="text/javascript" src="{!! CommonHelper::asset('newjs/select2.min.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/email_config/index.js') !!}"></script>
<script type="text/javascript" src="{!! CommonHelper::asset('js/tickets/config.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function() {
	var config = new Object;
	config.url = new Object;
	config.url.email_config = "{{ url('tickets/email_config_list') }}";
	config.url.alias_account_list = "{{ url('tickets/alias_account_list') }}";
	config.url.departments_with_company = "{{ url('tickets/departments') }}";
	config.url.problem_categories_by_company = "{{ url('tickets/problem-categories/by-dept') }}";
	config.url.add_acc = "{{ url('tickets/ajax-auto-acc-create') }}";
	config.url.delete_acc = "{{ url('tickets/ajax-auto-acc-delete') }}";
	config.url.edit_acc = "{{ url('tickets/ajax-auto-acc-edit') }}";
	config.url.get_acc = "{{ url('tickets/ajax-auto-acc-get') }}";
	config.url.goBack = "{{ url('tickets/config') }}";

	config.url.add_alias_acc = "{{ url('tickets/ajax_add_alias_acc') }}";
	config.company_user_detail = {!! json_encode($userDatail) !!};
	config.url.load_alias_acc = "{{ url('tickets/ajax_load_alias_acc') }}";
	config.url.update_alias_acc = "{{ url('tickets/ajax_update_alias_acc') }}";
	config.url.delete_alias_acc = "{{ url('tickets/ajax_delete_alias_acc') }}";
	config.token = "{{ csrf_token() }}";
	config.translations = {
		something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
		serach_option: '{{ trans('content.service_ticket_fields.serach_option') }}',
		Edit_Config: '{{ trans('content.service_ticket_fields.Edit_Config') }}',
		Delete_Config: '{{ trans('content.service_ticket_fields.Delete_Config') }}',
		View_Config: '{{ trans('content.service_ticket_fields.View_Config') }}',
		Select_Problem_Category: '{{ trans('content.service_ticket_fields.Select_Problem_Category') }}',
		Select_Sub_Category: '{{ trans('content.service_ticket_fields.Select_Sub_Category') }}',
		select_department: '{{ trans('content.service_ticket_fields.select_department') }}',
		Select_Status: '{{ trans('content.service_ticket_fields.Select_Status') }}',
	};

  new EmailConfig(config);
  new AliasAccount(config);
});
</script>
@endpush
