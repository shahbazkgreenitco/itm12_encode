{{-- @page-meta
{
  "page_no": "",
  "file": "charge_history.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "AI Migration",
      "from": "2026-07",
      "reviewer": null,
      "description": "Migrated from Laravel 8 / Bootstrap 3 legacy module"
    }
  ]
}
--}}
@extends('layouts.layout1')
@section('title', trans('left_nav.procurements.charge_history'))
@section('content')
<div id="main-charge-history-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('left_nav.procurements.charge_history') }}</h3>
        <div class="d-flex gap-8">
            <button class="amg-btn amg-btn-primary amg-btn-sm btn-back" type="button"
                    data-bs-toggle="tooltip" title="{{ trans('button.back') }}">
                <i class="bi bi-arrow-left"></i>
                <span>{{ trans('button.back') }}</span>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table id="charge_history" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                    <th><h4 class="b2-text">{{ trans('content.procurement_fields.Changes_done_by') }}</h4></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{!! CommonHelper::asset('js/procurements/charge_history.js') !!}"></script>
<script>
    var config = {};
    config.url = {};
    config.url.back = "{{ url('procurements/charge_history') }}";
    config.url.user = "{{ url('user/info') }}";
    config.token = "{{ csrf_token() }}";
    config.translations = {
        something_went_wrong: '{{ trans('content.user_fields.something_went_wrong') }}',
        save: '{{ trans('button.save') }}',
        Search: '{{ trans('content.ticket_status.Search') }}',
        Refresh_List: '{{ trans('content.ticket_status.Refresh_List') }}',
        press_enter_with_Search: '{{ trans('content.user_fields.please_enter_valid_search') }}',
        are_you_delete: '{{ trans('content.po_companies.are_you_delete') }}',
        Filter: '{{ trans('content.po_companies.Filter') }}',
        Add_po_company: '{{ trans('content.po_companies.Add_po_company') }}',
        Edit_po_company: '{{ trans('content.po_companies.Edit_po_company') }}',
        Download_report: '{{ trans('content.po_companies.Download_report') }}',
        select_image: '{{ trans('content.procurement_fields.select_image') }}',
        Select_City: '{{ trans('content.procurement_fields.Select_City') }}',
        Select_State: '{{ trans('content.procurement_fields.Select_State') }}',
        Select_Country: '{{ trans('content.procurement_fields.Select_Country') }}',
        Please_select_country: '{{ trans('content.procurement_fields.Please_select_country') }}',
        Please_select_state: '{{ trans('content.procurement_fields.Please_select_state') }}',
        Please_select_country_and_state: '{{ trans('content.procurement_fields.Please_select_country_and_state') }}'
    };
    new ChargeHistory(config);
</script>
@endpush
