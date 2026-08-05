<div class="amg-modal amg-form-modal modal fade" id="ticket_trigger_mdl" tabindex="-1"
    aria-labelledby="ticketTriggerModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="ticket_trigger_form" class="w-100 amg-form-theme" method="POST"
            action="{{ url('tickets/trigger/save') }}" autocomplete="off" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="forAction" id="forAction" value="">
            <div class="modal-content rounded-5">
                <!-- Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">
                        <span id="modalTitle">{{ __('trigger.add') }}</span> {{ __('trigger.ticket_trigger') }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="container-fluid py-3">
                        <!-- Row 1: Company -->
                        <div class="row">
                            <div class="col-12 col-xl-12 mb-3">
                                <label for="company_id" class="form-label b1-text required">{{ __('trigger.company') }}</label>
                                <div class="amg-form-field-row align-items-center">
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
                                </div>
                            </div>
                        </div>
                        
                        <!-- Row 2: Title -->
                        <div class="row">
                            <div class="col-12 col-xl-12 mb-3">
                                <label for="name" class="form-label b1-text me-2 required">{{ __('trigger.title') }}</label>
                                <div class="amg-form-field-row align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19 7V5L5 5V7" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12 5L12 19M12 19H10M12 19H14" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <input type="text" name="name" id="subject" class="form-control" placeholder="{{ __('trigger.enter_trigger_name') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Description -->
                        <div class="row">
                            <div class="col-12 col-xl-12 mb-3">
                                <label for="description" class="form-label b1-text required">{{ __('trigger.description') }}</label>
                                <div class="amg-form-field-row align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4 6H20M4 12H20M4 18H12" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="{{ __('trigger.enter_description') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: Match Type (AND/OR) -->
                        <div class="row">
                            <div class="col-12 col-xl-12 mb-3">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <label class="form-label b1-text required mb-0 me-3">{{ __('trigger.match_type') }}</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="matchType" id="all"
                                                value="all" checked>
                                            <label class="form-check-label" for="all">{{ __('trigger.and') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="matchType" id="any"
                                                value="any">
                                            <label class="form-check-label" for="any">{{ __('trigger.or') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- ✅ Error container for Match Type -->
                                <div class="error-container"></div>
                            </div>
                        </div>

                        <!-- Conditions Repeater with Icon -->
                        <div class="row condition-repeater mt-3">
                            <div class="col-12 col-xl-12 mb-3">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-start">
                                    <div class="w-100" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px;">
                                        <div class="d-flex align-items-center mb-3">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                                <path d="M20 7L4 7M20 12L4 12M20 17L4 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                <circle cx="8" cy="7" r="2" stroke="currentColor" stroke-width="1.5"/>
                                                <circle cx="8" cy="12" r="2" stroke="currentColor" stroke-width="1.5"/>
                                                <circle cx="8" cy="17" r="2" stroke="currentColor" stroke-width="1.5"/>
                                            </svg>
                                            <label class="form-label b1-text required mb-0">{{ __('trigger.conditions') }}</label>
                                        </div>
                                        <div data-repeater-list="conditions" class="conditions-list">
                                            <div class="mb-3 row align-items-start" data-repeater-item>
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                        </span>
                                                        <select name="condition_id" class="form-select condition_id">
                                                            <option value="">{{ __('trigger.select_condition') }}</option>
                                                            @foreach($conditions as $key => $condition)
                                                                <option value="{{ $condition->id }}">{{ $condition->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- ✅ Error container for Condition -->
                                                    <div class="error-container"></div>
                                                </div>
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M8 9L16 15M16 9L8 15" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                        </span>
                                                        <select name="operator_id" class="form-select operator_id">
                                                            <option value="">{{ __('trigger.operator') }}</option>
                                                            @foreach($operators as $key => $operator)
                                                                <option value="{{ $operator->id }}">
                                                                    {{ $operator->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- ✅ Error container for Operator -->
                                                    <div class="error-container"></div>
                                                </div>
                                                <div class="col-md-3 mb-2 mb-md-0">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M4 7H20M10 7V5M14 7V5" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M4 11H20M6 11V17M18 11V17M10 15H14" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                        </span>
                                                        <input type="text" name="condition_value"
                                                            class="form-control condition_value" placeholder="{{ __('trigger.value') }}">
                                                    </div>
                                                    <!-- ✅ Error container for Value -->
                                                    <div class="error-container"></div>
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <button type="button" class="amg-btn amg-btn-primary amg-btn-sm"
                                                        data-repeater-delete>
                                                        <i class="bi bi-x-square"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" data-repeater-create
                                                class="amg-btn amg-btn-primary amg-btn-sm">
                                                <i class="bi bi-plus-square"></i> {{ __('trigger.add_condition') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Repeater with Icon -->
                        <div class="row action-repeater mt-3">
                            <div class="col-12 col-xl-12 mb-3">
                                <div class="amg-form-field amg-form-field-row d-flex align-items-start">
                                    <div class="w-100" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px;">
                                        <div class="d-flex align-items-center mb-3">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                                <path d="M12 2V4M12 20V22M4 12H2M6 12H4M20 12H22M18 12H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                                                <path d="M12 8L12 12L14 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            </svg>
                                            <label class="form-label b1-text required mb-0">{{ __('trigger.actions') }}</label>
                                        </div>
                                        <div data-repeater-list="actions" class="actions-list">
                                            <div class="mb-3 row align-items-start" data-repeater-item>
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M12 6V12L16 14" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                <circle cx="12" cy="12" r="9" stroke="#6B7280" stroke-width="1.5"/>
                                                            </svg>
                                                        </span>
                                                        <select name="action_id" class="form-select action_id">
                                                            <option value="">{{ __('trigger.select_actions') }}</option>
                                                            @foreach($actions as $key => $action)
                                                                <option value="{{ $action->id }}">{{ $action->display_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- ✅ Error container for Action -->
                                                    <div class="error-container"></div>
                                                </div>
                                                <div class="col-md-7">
                                                    <!-- Dynamic action fields (note, mail, status, etc.) -->
                                                    <div class="actions note d-none" id="note">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M4 6H20M4 12H20M4 18H12" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                </svg>
                                                            </span>
                                                            <textarea name="action_value" class="form-control"
                                                                placeholder="Add a note" disabled></textarea>
                                                        </div>
                                                        <!-- ✅ Error container for Note -->
                                                        <div class="error-container"></div>
                                                    </div>
                                                    <div class="actions mail d-none" id="mail">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M22 6L12 13L2 6M22 6V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V6M22 6L12 13L2 6" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                </svg>
                                                            </span>
                                                            <select name="action_value" class="form-select user_id"
                                                                disabled>
                                                                @foreach($assign_to as $key => $user)
                                                                    <option value="{{ $user->id }}">{{ $user->text }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <!-- ✅ Error container for Mail User -->
                                                        <div class="error-container"></div>
                                                    </div>
                                                    <div class="actions status d-none" id="status">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M20 12V18H4V12M12 2V6M12 22V18M6 6L8 8M18 6L16 8" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                </svg>
                                                            </span>
                                                            <select name="action_value" class="form-select status_id"
                                                                disabled>
                                                                <option value="">{{ __('trigger.select_status') }}</option>
                                                            </select>
                                                        </div>
                                                        <!-- ✅ Error container for Status -->
                                                        <div class="error-container"></div>
                                                    </div>
                                                    <div class="actions department d-none" id="department">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M3 9H21M3 9V19H21V9M3 9L12 3L21 9" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                </svg>
                                                            </span>
                                                            <select name="action_value" class="form-select department_id prob_cat" disabled>
                                                                <option value="">{{ __('trigger.select_department') }}</option>
                                                            </select>
                                                        </div>
                                                        <!-- ✅ Error container for Department -->
                                                        <div class="error-container"></div>
                                                    </div>
                                                    <div class="actions apidefination d-none" id="apidefination">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M4 7L8 12L4 17M20 7L16 12L20 17" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                </svg>
                                                            </span>
                                                            <select name="action_value" class="form-select apidefination_id"
                                                                disabled>
                                                                <option value="">{{ __('trigger.select_api_defination') }}</option>
                                                                @foreach($apiDefinations as $key => $apiDefination)
                                                                    <option value="{{ $apiDefination->id }}">
                                                                        {{ $apiDefination->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <!-- ✅ Error container for API Definition -->
                                                        <div class="error-container"></div>
                                                    </div>
                                                    <div class="actions priority d-none" id="priority">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M12 2V4M12 20V22M4 12H2M6 12H4M20 12H22M18 12H20" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                    <circle cx="12" cy="12" r="3" stroke="#6B7280" stroke-width="1.5"/>
                                                                </svg>
                                                            </span>
                                                            <select name="action_value" class="form-select priority_id"
                                                                disabled>
                                                                <option value="">{{ __('trigger.select_priority') }}</option>
                                                                @foreach($prioritys as $key => $priority)
                                                                    <option value="{{ $priority->id }}">{{ $priority->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <!-- ✅ Error container for Priority -->
                                                        <div class="error-container"></div>
                                                    </div>
                                                    <div class="actions assign_to d-none" id="assign_to">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="#6B7280" stroke-width="1.5"/>
                                                                    <path d="M20 21V19C20 16.8 18.2 15 16 15H8C5.8 15 4 16.8 4 19V21" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                </svg>
                                                            </span>
                                                            <select name="action_value" class="form-select user_id"
                                                                disabled>
                                                                @foreach($assign_to as $key => $user)
                                                                    <option value="{{ $user->id }}">{{ $user->text }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <!-- ✅ Error container for Assign To -->
                                                        <div class="error-container"></div>
                                                    </div>
                                                    <div class="actions mail_content d-none mt-2" id="mail_content">
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M3 6H21M3 12H21M3 18H15" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                        </svg>
                                                                    </span>
                                                                    <input type="text" name="subject"
                                                                        class="form-control subject" placeholder="Subject"
                                                                        disabled>
                                                                </div>
                                                                <!-- ✅ Error container for Subject -->
                                                                <div class="error-container"></div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M4 6H20M4 12H20M4 18H12" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                        </svg>
                                                                    </span>
                                                                    <textarea name="message" class="form-control message"
                                                                        placeholder="Message" disabled></textarea>
                                                                </div>
                                                                <!-- ✅ Error container for Message -->
                                                                <div class="error-container"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="actions category_fields d-none mt-2"
                                                        id="category_fields">
                                                        <div class="row g-2">
                                                            <div class="col-md-6">
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                        </svg>
                                                                    </span>
                                                                    <select name="problem_category_id"
                                                                        class="form-select problem_category_id" disabled>
                                                                        <option value="">{{ __('trigger.select_category') }}</option>
                                                                    </select>
                                                                </div>
                                                                <!-- ✅ Error container for Category -->
                                                                <div class="error-container"></div>
                                                            </div>
                                                            <div class="col-md-6 prob_sub_cat d-none">
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round"/>
                                                                        </svg>
                                                                    </span>
                                                                    <select name="sub_category_id"
                                                                        class="form-select sub_category_id" disabled>
                                                                        <option value="">{{ __('trigger.select_api_defination') }}</option>
                                                                    </select>
                                                                </div>
                                                                <!-- ✅ Error container for Sub Category -->
                                                                <div class="error-container"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <button type="button" class="amg-btn amg-btn-primary amg-btn-sm"
                                                        data-repeater-delete>
                                                        <i class="bi bi-x-square"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" data-repeater-create
                                                class="amg-btn amg-btn-primary amg-btn-sm">
                                                <i class="bi bi-plus-square"></i>{{ __('trigger.add_action') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mt-3">
                            <div class="col-12 col-xl-12 mb-3">
                                <label for="trigger_status" class="form-label b1-text">{{ __('trigger.status') }}</label>
                                <div class="amg-form-field amg-form-field-row d-flex align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z"
                                                    fill="#131927" stroke="#131927" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                    stroke="#131927" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <select name="status" id="trigger_status" class="form-select">
                                            <option value="1">{{ __('trigger.enable') }}</option>
                                            <option value="2">{{ __('trigger.disable') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- ✅ Error container for Trigger Status -->
                                <div class="error-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="amg-form-footer gap-2 modal-footer d-flex justify-content-end pb-4 py-0">
                    <button type="button" id="btnClear" data-bs-dismiss="modal" class="amg-btn amg-btn-secondary col-md-2 amg-btn-md"> {{ trans('ticket.transfer_ticket.cancel') }}</button>
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-block col-md-2 amg-btn-md" style="min-width: 250px"> {{ trans('ticket.transfer_ticket.update') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>