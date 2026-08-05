{{-- * ------------------------------------------------------------
* File: add_problem.blade.php
* Module: Problem Management
* PROBM/26/02
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Hrishikesh Pandey
* Page ID: #002
* Reviewed By: 
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version (Hrishikesh Pandey)
* ------------------------------------------------------------ --}}


<div id="addproblemModal" class="amg-modal amg-form-modal modal fade" tabindex="-1" aria-labelledby="addProblemModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form name="problem-mdl-frm" id="addproblem-mdl-frm" method="post" action="#" class="w-100 amg-form-theme" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="id" name="id" class="hidden" value="" />
            <input type="hidden" id="for_action" name="for_action" class="hidden" value="" />

            <div class="modal-content rounded-5">
                <!-- Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4" id="myLargeModalLabel">
                        {{ trans("content.problem_manager.add_heading") }}
                    </h3>
                    <button type="button" class="modal-close px-4" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="amg-modal-close-icon" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>

                <!-- Body with Tabs -->
                <div class="modal-body bg-white">
                    <ul class="nav nav-underline border-0 border-bottom" id="problem-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="basic-tab" data-bs-toggle="tab" href="#basic-details" role="tab" aria-controls="basic-details" aria-expanded="true">
                                <span class="h4-text">{{ trans("content.problem_manager.basic_details") }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="device-tab" data-bs-toggle="tab" href="#impacted-devices" role="tab" aria-controls="impacted-devices">
                                <span class="h4-text">{{ trans("content.problem_manager.impacted_devices") }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ticket-tab" data-bs-toggle="tab" href="#impacted-tickets" role="tab" aria-controls="impacted-tickets">
                                <span class="h4-text">{{ trans("content.problem_manager.impacted_tickets") }}</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content tabcontent-border pt-3" id="ProblemTabContent">
                        <!-- ================= TAB 1: BASIC DETAILS ================= -->
                        <div role="tabpanel" class="tab-pane fade show active" id="basic-details" aria-labelledby="basic-tab">
                            <div class="container-fluid py-3">
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="name" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("content.problem_manager.Subject") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-pencil-square"></i></span>
                                                <input type="text" name="name" id="name" class="form-control" placeholder="{{ trans("content.problem_manager.Subject") }}" />
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="company_id" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("content.service_ticket_fields.company") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <select name="company_id" id="company_id" class="form-select error_shows"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description (Summernote) -->
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-start amg-form-field-row">
                                            <label for="content" class="form-label b1-text me-2 mb-0 text-end required pt-2">{{ trans("content.problem_manager.Description") }}</label>
                                            <div class="input-group flex-column" style="flex:1">
                                                <div class="w-100">
                                                    <textarea name="content" id="content" class="amg-summernote form-control summernote" placeholder="{{ trans("content.problem_manager.details") }}"></textarea>
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Department -->
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="department_id" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("content.problem_manager.Department") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                                <select name="department_id" id="department_id" class="form-select error_shows"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Problem Category -->
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="problem_category_id" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("content.problem_manager.Category") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-list-ul"></i></span>
                                                <select name="problem_category_id" id="problem_category_id" class="form-select two_select_error_shows"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subcategory -->
                                <div class="row mb-4 px-4" id="sub_category_id_cvr">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label id="sub_category_id_lbl" for="sub_category_id" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("content.problem_manager.Subcategory") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-sitemap"></i></span>
                                                <select name="sub_category_id" id="sub_category_id" class="form-select two_select_error_shows"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="priority_id" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("content.problem_manager.Priority") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                                <select name="priority_id" id="priority_id" class="form-select two_select_error_shows"></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Handler (multiple) -->
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label id="problem_handler" for="problem_handler_id" class="form-label b1-text me-2 mb-0 text-end required">{{ trans("content.problem_manager.Handler") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                <select name="problem_handler_id[]" id="problem_handler_id" class="form-select error_shows" multiple></select>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ================= TAB 2: IMPACTED DEVICES ================= -->
                        <div role="tabpanel" class="tab-pane fade" id="impacted-devices" aria-labelledby="device-tab">
                            <div class="container-fluid py-3">
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-center amg-form-field-row">
                                            <label for="device_id" class="form-label b1-text me-2 mb-0 text-end">{{ trans("content.problem_manager.Impacted_Devices") }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-hdd-stack"></i></span>
                                                <select name="device_id[]" id="device_id" class="form-select" multiple="multiple" placeholder="{{ trans('content.service_ticket_fields.Choose_Device') }}"></select>
                                                <span class="input-group-text filterImpactedDevices" style="cursor:pointer;"><i class="bi bi-funnel"></i></span>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row px-4">
                                    <div style="width: 250px; margin-bottom: 15px;">
                                        <div class="amg-list-searchbar">
                                            <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                            </svg>
                                            <input type="text" class="amg-list-searchbar__input device-search-input" placeholder="{{ trans('content.problem_manager.Search') }}...........">
                                        </div>
                                    </div>                                    
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table id="impactDevicesTable" class="table table-bordered table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th><h4>{{ trans("content.problem_manager.asset_tag") }}<</h4></th>
                                                        <th><h4>{{ trans("content.problem_manager.device_name") }}<</h4></th>
                                                        <th><h4>{{ trans("content.problem_manager.device_type") }}<</h4></th>
                                                        <th><h4>{{ trans("content.problem_manager.Model") }}<</h4></th>
                                                        <th><h4>{{ trans("content.problem_manager.serial_number") }}<</h4></th>
                                                        <th><h4>{{ trans("content.service_ticket_fields.Actions") }}<</h4></th>
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

                        <!-- ================= TAB 3: IMPACTED TICKETS ================= -->
                        <div role="tabpanel" class="tab-pane fade" id="impacted-tickets" aria-labelledby="ticket-tab">
                            <div class="container-fluid py-3">
                                <div class="row mb-4 px-4">
                                    <div class="col-12">
                                        <div class="amg-form-field d-flex align-items-start amg-form-field-row">
                                            <label for="ticket_id" class="form-label b1-text me-2 mb-0 text-end">{{ trans("content.problem_manager.Impacted_Ticket") }}</label>
                                            <div class="input-group flex-column w-100">
                                                <div class="w-100 mb-2">
                                                    <select name="ticket_id[]" id="ticket_id" class="form-select" multiple="multiple"></select>
                                                </div>
                                                <div class="w-100">
                                                    <input id="selectAllTickets" name="selectAllTickets" type="checkbox" class="form-check-input me-2" />
                                                    <label class="text-bold text-primary" for="selectAllTickets">Select all tickets from selected department and categories</label>
                                                </div>
                                            </div>
                                            <div class="amg-form-error-wrap"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row px-4">
                                    <div style="width: 250px; margin-bottom: 15px;">
                                        <div class="amg-list-searchbar">
                                            <svg class="amg-list-searchbar__icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z" fill="currentColor"></path>
                                            </svg>
                                            <input type="text" class="amg-list-searchbar__input ticket-search-input" placeholder="{{ trans('content.problem_manager.Search') }}...........">
                                        </div>
                                    </div>                                    
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table id="impactTicketsTable" class="table display app-data-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans("content.problem_manager.Id") }}</th>
                                                        <th>{{ trans("content.problem_managerTag") }}</th>
                                                        <th>{{ trans("content.problem_manager.Subject") }}</th>
                                                        <th>{{ trans("content.problem_manager.Department") }}</th>
                                                        <th>{{ trans("content.problem_manager.Category") }}</th>
                                                        <th>{{ trans("content.problem_manager.Subcategory") }}</th>
                                                        <th>{{ trans("content.problem_manager.Expires") }}</th>
                                                        <th>{{ trans("content.problem_manager.UpdatedAt") }}</th>
                                                        <th>{{ trans("content.problem_manager.Status") }}</th>
                                                        <th>{{ trans("content.service_ticket_fields.Actions") }}</th>
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

                <!-- Footer -->
                <div class="amg-form-footer modal-footer d-flex justify-content-end py-4 border-top">
                    <button type="button" id="btnSubmit" class="amg-btn amg-btn-primary amg-btn-md mb-2" style="width:150px">
                        {{ trans("button.create") }}
                    </button>
                    <button type="button" id="btnClear" class="amg-btn amg-btn-secondary amg-btn-md mb-2 ms-2" data-bs-dismiss="modal" style="width:150px">
                        {{ trans("button.close") }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .note-editor {
        width: 100%;
    }
   
</style>