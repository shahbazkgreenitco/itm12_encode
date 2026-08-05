{{-- Change Request Modal with 4 Steps and Summernote --}}
<div class="amg-modal amg-form-modal modal fade" id="changeRequestModal" tabindex="-1" aria-labelledby="changeRequestModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="changeRequestForm" name="changeRequestForm" method="post"
              action="#" class="form-horizontal w-100 amg-form-theme"
              autocomplete="off" onsubmit="return false;">
                <input type="hidden" name="tmp_id" id="tmp_id"/>
            <div class="modal-content rounded-5">

                {{-- Modal Header --}}
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 id="changeRequestModalLabel" class="modal-title px-4">{{ trans('change_management/list.New_Change') }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        
                        <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                            <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none">
                                <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z" fill="currentColor"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body">
                    <div id="changeRequestLoader" style="display:none;position:absolute;top:0;right:0;bottom:0;left:0;z-index:10;background:rgba(255,255,255,0.6);">
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);">
                            <i class="fa fa-spinner fa-spin fa-3x"></i>
                        </div>
                    </div>

                    {{-- Step Navigation --}}
                    <div class="step-navigation mb-4">
                        <ul class="nav nav-pills nav-fill" id="changeRequestSteps" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab" aria-controls="step1" aria-selected="true">
                                    <span class="step-number">1</span>
                                    <span class="step-label">{{ trans("change_management/list.basic_details") }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab" aria-controls="step2" aria-selected="false">
                                    <span class="step-number">2</span>
                                    <span class="step-label">{{ trans("change_management/list.planing") }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab" aria-controls="step3" aria-selected="false">
                                    <span class="step-number">3</span>
                                    <span class="step-label">{{ trans("change_management/list.upload_documents") }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab" aria-controls="step4" aria-selected="false">
                                    <span class="step-number">4</span>
                                    <span class="step-label">{{ trans("change_management/list.roles") }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="changeRequestTabContent">
                        {{-- Step 1: Basic Details --}}
                        <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                            <div class="container-fluid py-3">
                                {{-- <h5 class="section-title mb-4">1. Basic Details</h5> --}}
                                <div class="row g-4 px-4">
                                    
                                    {{-- Subject --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="company_id" class="form-label b1-text required">{{ __('ticket.create_ticket.company') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7 9.01L7.01 8.99889" stroke="#131927" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M11 9.01L11.01 8.99889" stroke="#131927" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M7 13.01L7.01 12.9989" stroke="#131927" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M11 13.01L11.01 12.9989" stroke="#131927" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M7 17.01L7.01 16.9989" stroke="#131927" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M11 17.01L11.01 16.9989" stroke="#131927" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M15 21H3.6C3.26863 21 3 20.7314 3 20.4V5.6C3 5.26863 3.26863 5 3.6 5H9V3.6C9 3.26863 9.26863 3 9.6 3H14.4C14.7314 3 15 3.26863 15 3.6V9M15 21H20.4C20.7314 21 21 20.7314 21 20.4V9.6C21 9.26863 20.7314 9 20.4 9H15M15 21V17M15 9V13M15 13H17M15 13V17M15 17H17"
                                                            stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <select name="company_id" id="company_id" class="form-select"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="subject" class="form-label b1-text required">{{ trans("change_management/list.Subject") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-book"></i></span>
                                                <input autocomplete="off" type="text" name="subject" placeholder="{{ trans('change_management/list.enter_subject') }}" id="subject" class="form-control required" @if(request()->subject) value="{{ request()->subject }}" @endif />
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                            <div class="subject"></div>
                                        </div>
                                    </div>
                                    {{-- Category --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="category_id" class="form-label b1-text required">{{ trans("change_management/list.Category") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-link"></i></span>
                                                <select name="category_id" id="category_id" class="form-select required">                                                    
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                    {{-- Priority --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="priority_id" class="form-label b1-text required">{{ trans("change_management/list.priority") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                                <select name="priority_id" id="priority_id" class="form-select required">
                                                   
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Change Type --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="change_type_id" class="form-label b1-text required">{{ trans("change_management/list.Change_Type") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-tags"></i></span>
                                                <select name="change_type_id" id="change_type_id" class="form-select required">
                                                    
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Impact --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="impact_id" class="form-label b1-text required">{{ trans("change_management/list.Impact") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-arrow-up-circle"></i></span>
                                                <select name="impact_id" id="impact_id" class="form-select required">
                                                    
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Risk --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="risk_id" class="form-label b1-text required">{{ trans("change_management/list.Risk") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-shield-exclamation"></i></span>
                                                <select name="risk_id" id="risk_id" class="form-select required">
                                                   
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Downtime --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="downtime_id" class="form-label b1-text required">{{ trans("change_management/list.Downtime") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                                <select name="downtime" id="downtime_id" class="form-select required">
                                                    <option value="1">{{ trans("change_management/list.No") }}</option>
                                                    <option value="2">{{ trans("change_management/list.Yes") }}</option>
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Downtime Start Date (conditional) --}}
                                    <div id="downtime_hide_show" class="col-md-12" style="display:none;">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="amg-form-field amg-form-field-row">
                                                    <label for="start_down_time" class="form-label b1-text required">{{ trans("change_management/list.Start_Downtime") }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                        <input autocomplete="off" type="text" name="start_down_time" placeholder="{{ trans("change_management/list.Select_Start_Downtime") }}" id="start_down_time" class="form-control date-field" @if(request()->start_down_time) value="{{ request()->start_down_time }}" @endif />
                                                    </div>
                                                    <div class="amg-form-error-wrap"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="amg-form-field amg-form-field-row">
                                                    <label for="end_down_time" class="form-label b1-text required">{{ trans("change_management/list.End_Downtime") }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                                        <input autocomplete="off" type="text" name="end_down_time" placeholder="{{ trans("change_management/list.Select_End_Downtime") }}" id="end_down_time" class="form-control date-field" @if(request()->end_down_time) value="{{ request()->end_down_time }}" @endif />
                                                    </div>
                                                    <div class="amg-form-error-wrap"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Schedule Start Date --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="scheduled_start_date" class="form-label b1-text {{ config('app.client') == 'ltts' ? 'required' : '' }}">{{ trans("change_management/list.Schedule_Start_Date") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                                <input autocomplete="off" type="text" name="scheduled_start_date" placeholder="{{ trans("change_management/list.select_schedule_start_date") }}" id="scheduled_start_date" class="form-control date-field {{ config('app.client') == 'ltts' ? 'required' : '' }}" @if(request()->scheduled_start_date) value="{{ request()->scheduled_start_date }}" @endif />
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Schedule End Date --}}
                                    <div class="col-md-6">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="scheduled_end_date" class="form-label b1-text {{ config('app.client') == 'ltts' ? 'required' : '' }}">{{ trans("change_management/list.Schedule_End_Date") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                                <input autocomplete="off" type="text" name="scheduled_end_date" placeholder="{{ trans("change_management/list.select_Schedule_end_date") }}" id="scheduled_end_date" class="form-control date-field {{ config('app.client') == 'ltts' ? 'required' : '' }}" @if(request()->scheduled_end_date) value="{{ request()->scheduled_end_date }}" @endif />
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Currency (conditionally shown) --}}
                                    @if(!in_array(config('app.client'), ['ltts']))
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label for="currency" class="form-label b1-text">{{ trans("change_management/list.currency") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                                    <select name="currency" id="currency" class="form-select">
                                                        <option value="" disabled selected>{{ trans("change_management/list.select_currency") }}</option>
                                                        
                                                    </select>
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Cost (conditionally shown) --}}
                                    @if(!in_array(config('app.client'), ['ltts','safari']))
                                        <div class="col-md-6">
                                            <div class="amg-form-field amg-form-field-row">
                                                <label for="cost" class="form-label b1-text">{{ trans("change_management/list.Cost") }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                                    <input autocomplete="off" type="text" name="cost" placeholder="{{ trans("change_management/list.enter_cost") }}" id="cost" class="form-control" @if(request()->cost) value="{{ request()->cost }}" @endif />
                                                </div>
                                                <div class="amg-form-error-wrap"></div>
                                            </div>
                                        </div>
                                    @endif

                                    

                                    

                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Planning --}}
                        <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                            <div class="container-fluid py-3">
                                {{-- <h5 class="section-title mb-4">2. Planning</h5> --}}
                                <div class="row g-4 px-4">
                                    
                                    {{-- Change Description --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="change_description" class="form-label b1-text required">{{ trans("change_management/list.Change_Description") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-textarea"></i></span>
                                                <textarea name="change_description" id="change_description" class="form-control summernote-editor required" placeholder="">@if(request()->change_description){{ request()->change_description }}@endif</textarea>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Reason for Change --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="reason_description" class="form-label b1-text required">{{ trans("change_management/list.Reason_for_Change") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-question-circle"></i></span>
                                                <textarea name="reason_description" id="reason_description" class="form-control summernote-editor required" placeholder="">@if(request()->reason_description){{ request()->reason_description }}@endif</textarea>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                            <div class="reason_description"></div>
                                        </div>
                                    </div>

                                    {{-- Risk Description --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="risk_description" class="form-label b1-text required">{{ trans("change_management/list.Risk_Description") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-shield-exclamation"></i></span>
                                                <textarea name="risk_description" id="risk_description" class="form-control summernote-editor required" placeholder="{{ trans('change_management/list.Enter_Description') }}">@if(request()->risk_description){{ request()->risk_description }}@endif</textarea>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                            <div class="risk_description"></div>
                                        </div>
                                    </div>

                                    {{-- Impact Description --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="impact_description" class="form-label b1-text required">{{ trans("change_management/list.Impact_d") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                                <textarea name="impact_description" id="impact_description" class="form-control summernote-editor required" placeholder="">@if(request()->impact_description){{ request()->impact_description }}@endif</textarea>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                            <div class="impact_description"></div>
                                        </div>
                                    </div>

                                    {{-- Rollout/Rollback Plan --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="rollout_plan" class="form-label b1-text required">{{ trans("change_management/list.rollout_rollback_plan") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-arrow-repeat"></i></span>
                                                <textarea name="rollout_plan" id="rollout_plan" class="form-control summernote-editor required" placeholder="">@if(request()->rollout_plan){{ request()->rollout_plan }}@endif</textarea>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                            <div class="rollout_plan"></div>
                                        </div>
                                    </div>

                                    {{-- Fallback Plan --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="fallback_plan" class="form-label b1-text required">{{ trans("change_management/list.Fallback_Plan") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-arrow-counterclockwise"></i></span>
                                                <textarea name="fallback_plan" id="fallback_plan" class="form-control summernote-editor required" placeholder="">@if(request()->fallback_plan){{ request()->fallback_plan }}@endif</textarea>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                            <div class="fallback_plan"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Step 3: Upload Attachments --}}
                        <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                            <div class="container-fluid py-3">
                                {{-- <h5 class="section-title mb-4">3. Upload Attachments</h5> --}}
                                <div class="row g-4 px-4">
                                    {{-- Attachments --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="attachments" class="form-label b1-text">{{ trans('change_management/list.Add_Attachment') }}</label>
                                             <div class="col-md-9">
                                                <div class="ticket-attachment-wrapper">
                                                    <div id="attachment-dropper-cover"
                                                        class="amg-uploader"
                                                        data-amg-uploader
                                                        data-multiple="true"
                                                        data-auto-upload="true"
                                                        data-max-files="10"
                                                        data-max-size="10"
                                                        data-record-id
                                                        data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv,.eml,msd"
                                                        data-upload-url="{{ url('change-management/change-attachment') }}"
                                                        data-remove-url="{{ url('change-management/remove-attachment') }}"
                                                        data-token="{{ csrf_token() }}"
                                                        data-record-input="#changeRequestForm #tmp_id"
                                                        data-upload-field="attachment">
                                                        <input type="file" id="attachment_input" class="amg-uploader__input" multiple hidden>
                                                        <!-- Dropzone -->
                                                        <div id="attachment-dropper" class="amg-uploader__dropzone">
                                                            <div class="amg-uploader__message">
                                                                <span class="amg-uploader__icon-wrap">
                                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                        <path
                                                                            d="M16.5625 9.05781L9.64687 15.9734C8.76042 16.8599 7.55875 17.3581 6.30469 17.3581C5.05062 17.3581 3.84895 16.8599 2.9625 15.9734C2.07605 15.087 1.57788 13.8853 1.57788 12.6312C1.57788 11.3772 2.07605 10.1755 2.9625 9.28906L9.87812 2.37344C10.4718 1.7797 11.2765 1.44531 12.1156 1.44531C12.9547 1.44531 13.7594 1.7797 14.3531 2.37344C14.9469 2.96719 15.2812 3.77187 15.2812 4.61094C15.2812 5.45 14.9469 6.25469 14.3531 6.84844L7.43 13.764"
                                                                            stroke="black" stroke-width="1.25"
                                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </span>
                                                                <span> {{ trans("content.service_ticket_fields.upload_note") }}
                                                                   {{ trans("content.service_ticket_fields.or") }}
                                                                    <span class="amg-uploader__hint">{{ __('ticket.create_ticket.drop_files_here') }}</span>
                                                                </span>
                                                            </div>
                                                            <button type="button" id="manual_file_trigger" name="manual_file_trigger" class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">{{ __('ticket.create_ticket.add_attachment') }}</button>
                                                        </div>
                                                        <div class="mt-1 fs-1">
                                                            {{ trans('service_ticket.service_detail.supported_file_types') }}
                                                        </div>
                                                        <div class="mt-1 fs-1">
                                                            {{ trans('service_ticket.service_detail.max_files_size_message') }}
                                                        </div>
                                                        <!-- Preview -->
                                                        <div id="attachments" class="amg-uploader__preview"></div>
                                                        <!-- Error -->
                                                        <div class="amg-uploader__error"></div>
                                                    </div>
                                                    <input type="hidden" name="form_type" id="form_type" value="0">
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        {{-- Step 4: Moderators --}}
                        <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                            <div class="container-fluid py-3">
                                {{-- <h5 class="section-title mb-4">4. Moderators</h5> --}}
                                <div class="row g-4 px-4">
                                    
                                    {{-- Change Requester (Hidden - Auto-populated) --}}
                                    <input type="hidden" name="change_requester" id="change_requester" value="{{ auth()->user()->id }}">

                                    {{-- Change Requester (Display Only) --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="change_requester_display" class="form-label b1-text required">{{ trans("change_management/list.Change_Requester") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                <input type="text" id="change_requester_display" class="form-control" value="{{ auth()->user()->fullName() . '(' . auth()->user()->username . ')' }}" disabled />
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Change Manager --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="change_manager" class="form-label b1-text required">{{ trans("change_management/list.Change_Manager") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                <select name="change_manager" id="change_manager"
                                                        class="form-select required plgn-select2-user"
                                                        {{ config('app.client') == 'ltts' ? 'disabled' : '' }}>
                                                    @if(config('app.client') == 'ltts' && isset($viewData['crm_managers']) && $viewData['crm_managers']->id)
                                                        <option value="{{ $viewData['crm_managers']->id }}" selected>
                                                            {{ $viewData['crm_managers']->fullname() . ' (' . $viewData['crm_managers']->username . ')' }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Change Implementer (Multiple) --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="change_implementer" class="form-label b1-text required">{{ trans("change_management/list.Change_Implementer") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-tools"></i></span>
                                                <select name="change_implementer[]" multiple id="change_implementer"
                                                        class="form-select required plgn-select2-user" style="height: 100px;">
                                                    {{-- Options will be populated via Select2 --}}
                                                </select>
                                            </div>
                                            <div id="change_implementer_error" class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>

                                    {{-- Change Reviewer (Multiple) --}}
                                    <div class="col-md-12">
                                        <div class="amg-form-field amg-form-field-row">
                                            <label for="change_reviewer" class="form-label b1-text {{ config('app.client') == 'ltts' ? 'required' : '' }}">{{ trans("change_management/list.Change_Reviewer") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                                                <select name="change_reviewer[]" id="change_reviewer"
                                                        class="form-select plgn-select2-user" multiple style="height: 100px;">
                                                    {{-- Options will be populated via Select2 --}}
                                                </select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                            @if (config('app.client') != "ltts")
                                                <div class="form-text text-muted mt-2">
                                                    <i class="bi bi-info-circle"></i> If set the request will be sent for review before closure.
                                                </div>
                                            @endif
                                            @if (config('app.client') === "ltts")
                                                <div class="mt-2">
                                                    <div class="form-check">
                                                        <input id="enable_communication" name="enable_communication" type="checkbox" class="form-check-input" checked>
                                                        <label class="form-check-label text-bold text-primary" for="enable_communication">Communication Required</label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                                                    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer with Step Navigation Buttons --}}
                <div class="amg-form-footer modal-footer d-flex justify-content-between pb-4 py-0 px-4">
                    <div>
                        <button type="button" id="btnPrevStep" class="amg-btn amg-btn-secondary amg-btn-md" style="display:none;">
                            <i class="bi bi-chevron-left"></i> {{ trans('change_management/list.previous') }}
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnNextStep" class="amg-btn amg-btn-primary amg-btn-md">
                            {{ trans('change_management/list.next') }} <i class="bi bi-chevron-right"></i>
                        </button>
                        <button type="button" id="btnSubmitChange" class="amg-btn amg-btn-primary amg-btn-md" style="display:none;">
                            <i class="bi bi-check-circle"></i> {{ trans("button.create") }}
                        </button>
                        <button type="button" id="btnCloseChange" data-bs-dismiss="modal" class="amg-btn amg-btn-ghost bg-black text-white amg-btn-md">
                            {{ trans("button.close") }}
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
<style>
    /* Step Navigation Styles */
    /* Stepper */
    .step-navigation {
        /* display: flex; */
        justify-content: center;
        margin: 25px 0 35px;
    }

    .step-navigation ul {
        display: flex;
        align-items: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .step-navigation .nav-item {
        position: relative;
        flex: 1;
        text-align: center;
        min-width: 130px;
    }

    /* Line */
    .step-navigation .nav-item::after {
        content: "";
        position: absolute;
        top: 14px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e5e5e5;
        z-index: 0;
    }

    .step-navigation .nav-item:last-child::after {
        display: none;
    }

    /* Button */
    .step-navigation .nav-link {
        background: transparent !important;
        border: none !important;
        padding: 0;
        position: relative;
        z-index: 2;
    }

    /* Circle */
    .step-navigation .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fdeeee;
        color: #000;
        font-size: 13px;
        font-weight: 600;
        margin: auto;
    }

    /* Active Circle */
    .step-navigation .nav-link.active .step-number {
        background: #e60000;
        color: #fff;
    }

    /* Completed Line */
    .step-navigation .nav-item.completed::after {
        background: #e60000;
    }

    /* Label */
    .step-navigation .step-label {
        display: block;
        margin-top: 10px;
        color: #666;
        font-size: 15px;
    }

    .step-navigation .nav-link.active .step-label {
        color: #444;
        font-weight: 500;
    }

    /* Section Title */
    .section-title {
        color: #1e293b;
        font-weight: 600;
        border-bottom: 3px solid #dc2626;
        padding-bottom: 8px;
        display: inline-block;
    }

    /* Dropzone Styles */
    .dropzone-container {
        width: 100%;
    }

    .dropzone-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        background: #fafbfc;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
    }

    .dropzone-area:hover {
        border-color: #dc2626;
        background: #fef2f2;
    }

    .dropzone-area.dragover {
        border-color: #dc2626;
        background: #fef2f2;
    }

    .dropzone-content {
        pointer-events: none;
    }

    .dropzone-icon {
        font-size: 48px;
        color: #9ca3af;
        margin-bottom: 12px;
    }

    .dropzone-text {
        font-size: 16px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .dropzone-link {
        color: #dc2626;
        font-weight: 500;
        text-decoration: underline;
        cursor: pointer;
    }

    .dropzone-hint {
        font-size: 12px;
        color: #9ca3af;
        margin-bottom: 0;
    }

    .dropzone-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .attachment-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .attachment-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        min-width: 200px;
        max-width: 100%;
    }

    .attachment-preview .file-icon {
        font-size: 20px;
        color: #dc2626;
    }

    .attachment-preview .file-name {
        flex: 1;
        font-size: 13px;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .attachment-preview .file-size {
        font-size: 12px;
        color: #94a3b8;
        white-space: nowrap;
    }

    .attachment-preview .file-remove {
        cursor: pointer;
        color: #ef4444;
        font-size: 18px;
        padding: 0 4px;
    }

    /* Summernote Customization */
    .note-editor {
        border-radius: 8px !important;
        border-color: #d1d5db !important;
    }

    .note-editor .note-toolbar {
        background: #f8fafc !important;
        border-radius: 8px 8px 0 0 !important;
        padding: 8px 12px !important;
    }

    .note-editor .note-editable {
        min-height: 120px !important;
        padding: 12px !important;
    }

    .note-editor .note-statusbar {
        border-radius: 0 0 8px 8px !important;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .step-navigation .nav-pills .nav-link {
            flex-direction: column;
            gap: 4px;
            padding: 8px 12px;
            font-size: 12px;
        }
        
        .step-navigation .nav-pills .nav-link .step-number {
            width: 24px;
            height: 24px;
            font-size: 12px;
        }
        
        .step-navigation .nav-pills .nav-link .step-label {
            font-size: 11px;
        }
        
        .dropzone-area {
            padding: 20px;
        }
        
        .attachment-preview {
            min-width: 150px;
            font-size: 12px;
        }
    }
</style>