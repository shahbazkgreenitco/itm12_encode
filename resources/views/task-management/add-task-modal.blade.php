{{-- @page-meta
{
  "page_no": "ATM-26",
  "file": "add-task-modal.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Priya Maru",
      "from": "2026-06-03",
      "reviewer": null,
      "description": "Add Task Modal"
    }
  ]
}
--}}
<div class="amg-modal amg-form-modal modal fade" id="taskModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form id="task" name="task" method="POST" action="#" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            <div class="modal-content rounded-5">

                <!-- Header -->
                <div class="modal-header d-flex align-items-center py-3 pt-4">
                    <h3 class="modal-title px-4">
                        {{ trans('content.task_management.add_task') }}
                    </h3>

                    <button type="button" data-bs-dismiss="modal" class="modal-close px-4" aria-label="Close">
                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.0277 11.5277L17.1163 15.4375L21.0277 19.3473C21.138 19.4577 21.2255 19.5887 21.2852 19.7328C21.3449 19.877 21.3757 20.0315 21.3757 20.1875C21.3757 20.3435 21.3449 20.498 21.2852 20.6422C21.2255 20.7863 21.138 20.9173 21.0277 21.0277C20.9173 21.138 20.7864 21.2255 20.6422 21.2852C20.498 21.3449 20.3435 21.3757 20.1875 21.3757C20.0315 21.3757 19.877 21.3449 19.7328 21.2852C19.5887 21.2255 19.4577 21.138 19.3473 21.0277L15.4375 17.1163L11.5277 21.0277C11.4173 21.138 11.2864 21.2255 11.1422 21.2852C10.998 21.3449 10.8435 21.3757 10.6875 21.3757C10.5315 21.3757 10.377 21.3449 10.2328 21.2852C10.0887 21.2255 9.95768 21.138 9.84735 21.0277C9.73702 20.9173 9.6495 20.7863 9.58979 20.6422C9.53008 20.498 9.49935 20.3435 9.49935 20.1875C9.49935 20.0315 9.53008 19.877 9.58979 19.7328C9.6495 19.5887 9.73702 19.4577 9.84735 19.3473L13.7587 15.4375L9.84735 11.5277C9.62453 11.3048 9.49935 11.0026 9.49935 10.6875C9.49935 10.3724 9.62453 10.0702 9.84735 9.84734C10.0702 9.62452 10.3724 9.49934 10.6875 9.49934C11.0026 9.49934 11.3048 9.62452 11.5277 9.84734L15.4375 13.7587L19.3473 9.84734C19.4577 9.73701 19.5887 9.64949 19.7328 9.58978C19.877 9.53007 20.0315 9.49934 20.1875 9.49934C20.3435 9.49934 20.498 9.53007 20.6422 9.58978C20.7864 9.64949 20.9173 9.73701 21.0277 9.84734C21.138 9.95767 21.2255 10.0887 21.2852 10.2328C21.3449 10.377 21.3757 10.5315 21.3757 10.6875C21.3757 10.8435 21.3449 10.998 21.2852 11.1422C21.2255 11.2863 21.138 11.4173 21.0277 11.5277ZM30.875 15.4375C30.875 18.4907 29.9696 21.4754 28.2733 24.0141C26.577 26.5528 24.166 28.5315 21.3452 29.6999C18.5243 30.8683 15.4204 31.174 12.4258 30.5784C9.43122 29.9827 6.68052 28.5124 4.52155 26.3535C2.36257 24.1945 0.892294 21.4438 0.296634 18.4492C-0.299025 15.4546 0.00668883 12.3507 1.17512 9.52982C2.34354 6.70899 4.32221 4.29798 6.86089 2.60169C9.39958 0.905394 12.3843 0 15.4375 0C19.5305 0.00432223 23.4545 1.63216 26.3487 4.52631C29.2428 7.42047 30.8707 11.3445 30.875 15.4375ZM28.5 15.4375C28.5 12.854 27.7339 10.3285 26.2986 8.18036C24.8633 6.03225 22.8232 4.35799 20.4363 3.36932C18.0494 2.38065 15.423 2.12197 12.8891 2.62599C10.3553 3.13001 8.02775 4.37409 6.20092 6.20092C4.3741 8.02774 3.13002 10.3553 2.626 12.8891C2.12198 15.423 2.38066 18.0494 3.36933 20.4363C4.358 22.8232 6.03225 24.8632 8.18037 26.2986C10.3285 27.7339 12.854 28.5 15.4375 28.5C18.9007 28.4961 22.2209 27.1186 24.6698 24.6697C27.1186 22.2209 28.4961 18.9007 28.5 15.4375Z"
                                fill="#515151" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <!-- Company -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0 mandatory">Company</label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-building form-field-icon"></i>
                                    </span>

                                    <select id="company_id" name="company_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Task Title -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0 mandatory">
                                    {{ trans('content.task_management.title') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text"> <i class="bi bi-list-task form-field-icon"></i>
                                    </span>

                                    <input type="text" id="name" name="name"
                                        class="form-control filter-input form-with-icon"
                                        placeholder="{{ trans('content.task_management.enter_task_title') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row align-items-center mb-3 amg-form-field  amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0 mandatory">
                                    {{ trans('content.task_management.status') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-flag form-field-icon"></i>
                                    </span>

                                    <select name="status_id" id="status_id"
                                        class="form-select filter-input form-with-icon">
                                        @foreach (\App\Models\TaskManagement\TaskStatus::all() as $status)
                                            <option value="{{ $status->id }}">
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row ">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0 mandatory">
                                    {{ trans('content.task_management.priority') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-exclamation-triangle form-field-icon"></i>
                                    </span>

                                    <select name="priority_id" id="priority_id"
                                        class="form-select filter-input form-with-icon">
                                        @foreach (\App\Models\TaskManagement\TaskPriority::all() as $p)
                                            <option value="{{ $p->id }}">
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Task Related -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0 mandatory">
                                    {{ trans('content.task_management.task_related') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-flag form-field-icon"></i>
                                    </span>

                                    <select name="type_id" id="type_id"
                                        class="form-select filter-input form-with-icon">
                                        @foreach (\App\Models\TaskManagement\TaskType::all() as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Select Ticket --}}
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    Select Ticket
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tags form-field-icon"></i>
                                    </span>
                                    <select id="ticket_id" name="ticket_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">Department</label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-diagram-3 form-field-icon"></i>
                                    </span>

                                    <select id="task_department_id" name="department_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Problem Category -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label for="problem_category_id" class="form-label mb-0">
                                    {{ trans('content.service_ticket_fields.Problem_Category') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-tags form-field-icon"></i>
                                    </span>

                                    <select id="task_problem_category_id" name="problem_category_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sub Category -->
                        <div id="task_sub_category_id_cvr"
                            class="row align-items-center mb-3 amg-form-field amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    {{ trans('content.service_ticket_fields.sub_category') }}
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-folder2-open form-field-icon"></i>
                                    </span>

                                    <select id="task_sub_category_id" name="sub_category_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Project Title --}}
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label
                                    class="form-label mb-0">{{ trans('content.task_management.project_title') }}</label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-diagram-3 form-field-icon"></i>
                                    </span>

                                    <select id="project_id" name="project_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>

                                    <span class="input-group-text show-add-proj-mdl">
                                        <svg width="16" height="16" viewBox="0 0 19 19" fill="none">
                                            <path
                                                d="M18.75 9.375C18.75 9.67337 18.6315 9.95952 18.4205 10.1705C18.2095 10.3815 17.9234 10.5 17.625 10.5H10.5V17.625C10.5 17.9234 10.3815 18.2095 10.1705 18.4205C9.95952 18.6315 9.67337 18.75 9.375 18.75C9.07663 18.75 8.79048 18.6315 8.5795 18.4205C8.36853 18.2095 8.25 17.9234 8.25 17.625V10.5H1.125C0.826631 10.5 0.540483 10.3815 0.329505 10.1705C0.118526 9.95952 0 9.67337 0 9.375C0 9.07663 0.118526 8.79048 0.329505 8.5795C0.540483 8.36853 0.826631 8.25 1.125 8.25H8.25V1.125C8.25 0.826631 8.36853 0.540483 8.5795 0.329505C8.79048 0.118526 9.07663 0 9.375 0C9.67337 0 9.95952 0.118526 10.1705 0.329505C10.3815 0.540483 10.5 0.826631 10.5 1.125V8.25H17.625C17.9234 8.25 18.2095 8.36853 18.4205 8.5795C18.6315 8.79048 18.75 9.07663 18.75 9.375Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Change Record --}}
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label for="change_id"
                                    class="form-label mb-0">{{ trans('content.task_management.change_record') }}</label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-diagram-3 form-field-icon"></i>
                                    </span>

                                    <select id="change_id" name="change_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Select Ticket --}}
                        {{-- <div class="row align-items-center mb-3 amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    Select Ticket
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tags form-field-icon"></i>
                                    </span>
                                    <select id="ticket_id" name="ticket_id"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Work Start Date -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    {{ trans('content.task_management.work_start_date') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-event form-field-icon"></i>
                                    </span>

                                    <input type="text" id="start_date" name="start_date"
                                        class="form-control filter-input date-field form-with-icon" autocomplete="off"
                                        placeholder="{{ trans('content.task_management.Select_task_start') }}">
                                </div>
                            </div>
                        </div>


                        <!-- Due Date -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    {{ trans('content.task_management.planned_complete_date') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-event form-field-icon"></i>
                                    </span>

                                    <input type="text" id="due_date" name="due_date"
                                        class="form-control filter-input date-field form-with-icon" autocomplete="off"
                                        placeholder="{{ trans('content.task_management.Select_task_planned') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Actual Complete Date -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    {{ trans('content.task_management.actual_complete_date') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-event form-field-icon"></i>
                                    </span>

                                    <input type="text" id="end_date" name="end_date"
                                        class="form-control filter-input date-field form-with-icon" autocomplete="off"
                                        placeholder="{{ trans('content.task_management.Select_task_actual') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Cost -->
                        @if (config('app.client') != 'ltts')
                            <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                                <div class="col-md-3 text-md-end">
                                    <label class="form-label mb-0">{{ trans('content.task_management.cost') }}</label>
                                </div>

                                <div class="col-md-9">
                                    <div class="input-group ticket-input-group">

                                        <span class="input-group-text">
                                            <i class="bi bi-currency-rupee form-field-icon"></i>
                                        </span>

                                        <input type="text" id="cost" name="cost"
                                            class="form-control filter-input form-with-icon"
                                            placeholder="{{ trans('content.task_management.Select_task_cost') }}">
                                    </div>
                                </div>
                            </div>
                        @endif



                        <!-- Description -->
                        <div class="row align-items-start mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0 mandatory">
                                    {{ trans('content.task_management.task_description') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <textarea id="task_description" name="description" class="amg-summernote summernote w-100">
                                </textarea>
                            </div>
                        </div>

                        <!-- Assigned To -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    {{ trans('content.task_management.Select_task_assign') }}
                                </label>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group ticket-input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person form-field-icon"></i>
                                    </span>

                                    <select id="task_assigned_to" name="assigned_to"
                                        class="form-select filter-input form-with-icon">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Visible User -->
                        <div class="row align-items-center mb-3 amg-form-field amg-form-field-row d-none">
                            <div class="col-md-3 text-md-end">
                                <label class="form-label mb-0">
                                    Visible To User
                                </label>
                            </div>

                            <div class="col-md-9">
                                <input type="checkbox" id="is_visible_user" name="is_visible_user" value="1"
                                    class="form-check-input">
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="amg-form-footer modal-footer">

                    <button type="button" id="taskSubmit" class="amg-btn amg-btn-primary amg-btn-md col-md-2">
                        {{ trans('button.create') }}
                    </button>

                    <button type="button" id="btnClear" data-bs-dismiss="modal"
                        class="amg-btn amg-btn-ghost bg-black text-white amg-btn-md col-md-2">
                        {{ trans('button.close') }}
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>
