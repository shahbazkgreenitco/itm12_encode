{{-- @page-meta
{
  "page_no": "",
  "file": "procurements/add-budget-range.blade.php",
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
@section('title', trans('content.procurement_fields.add_budget_range'))
@section('content')
<div id="main-procurement-add-budget-range-wrapper">
    <div class="header-actions-wrapper d-flex align-items-center justify-content-between pe-4">
        <h3 class="h3-text mb-0">{{ trans('content.procurement_fields.add_budget_range') }}</h3>
        <div class="d-flex gap-8">
            <button type="button" class="header-icon-btn header-icon-btn-sm" data-bs-toggle="tooltip" title="{{ trans('button.refresh') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                </svg>
            </button>
        </div>
    </div>
    <main class="main-content" id="mainContent">
        <div class="container-fluid px-0">
            <div class="card rounded-0"><div class="card-body pt-3">
                <form id="addForm" method="POST" action="#" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" name="pab_id" id="pab_id" value="{{ $pab->id }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="amg-form-field">
                                <label for="toggle_default_budget_head" class="form-label">{{ trans("content.procurement_fields.default_budget_head") }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggle_default_budget_head">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="amg-form-field">
                                <label for="budget_from" class="form-label mandatory">{{ trans("content.procurement_fields.Budget_from") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-rupee"></i></span>
                                    <input type="number" name="budget_from" placeholder="{{ trans('content.procurement_fields.Budget_from') }}" id="budget_from" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="amg-form-field">
                                <label for="budget_to" class="form-label mandatory">{{ trans("content.procurement_fields.Budget_to") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-rupee"></i></span>
                                    <input type="number" name="budget_to" placeholder="{{ trans('content.procurement_fields.Budget_to') }}" id="budget_to" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="amg-form-field">
                                <label for="budget_head" class="form-label mandatory">{{ trans("content.procurement_fields.budget_head") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <select id="budget_head" name="budget_head" class="form-select"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="amg-form-field">
                                <label for="default_budget_head" class="form-label mandatory">{{ trans("content.procurement_fields.default_budget_head") }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <select id="default_budget_head" name="default_budget_head" class="form-select"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div></div>
        </div>
    </main>
</div>
