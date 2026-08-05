@php
    if ($status->item_type == 1) {
        $kanbanItems = $status->kanbanData($status->item_name, $status->board_id, $filters, $search, $company_id, $page, $order);
        $kanbanItems = $kanbanItems['results'];
    } elseif ($status->item_type == 2) {
        $kanbanItems = $status->ticketBoardData(
            $status->item_name,
            $status->id,
            $status->board_id,
            $filters,
            $search,
            $archived,
            $company_id,
            $page,
            $order
        );
        $kanbanItems = $kanbanItems['results'];
    }
@endphp
@foreach ($kanbanItems as $key => $kanban)
    @php
        $attachments = [];
        if (!empty($kanban->embedded_attachments)) {
            try {
                $raw = $kanban->embedded_attachments;
                $attachments = json_decode("[" . trim($raw, ',') . "]", true);
            } catch (\Exception $e) {
                $attachments = [];
            }
        }
        $styles = '';
        if ($kanban->status_name === 'Closed') {
            $styles .= 'background-color:#ECECEC;';
            if ($kanban->priority === 'Critical') $styles .= 'border:1px solid #FFA5A5;';
        } elseif ($kanban->status_name === 'Resolved') {
            $styles .= 'background-color: #E5FFEC;';
            if ($kanban->priority === 'Critical') $styles .= 'border:1px solid #FFA5A5;';
        } elseif ($kanban->priority === 'Critical') {
            $styles .= 'border:1px solid #FFA5A5;';
        }
    @endphp
    <div class="card"
        style="{{ $styles }}"
        data-task-type="${kanban.task_type}"
        @if ($kanban->ticketID != null) data-ticket-id="{{ $kanban->ticketID }}"
            @if ($kanban->item_type == 1)
                data-department-id="{{ $kanban->allDept }}"
                data-comment-id="{{ $kanban->commentRequired }}"
                data-tat-id="{{ $kanban->tat }}"
                data-assigned-id="{{ $kanban->assigned_to }}"
                data-priority-id="{{ $kanban->priority_id }}"
                data-cc_emails="{{ $kanban->cc_emails }}"
            @endif
        @endif
        data-card-id="{{ $kanban->id }}"
        data-user-id="{{ Auth::user()->id }}"
        @if ($status->item_type == 1) data-statusoldname="{{ $status->statusName->name }}" @endif
        data-username="{{ Auth::user()->fullName() }}">
        
        <div class="card-header" style="background: {{ $kanban->color_code ?? '#00008a' }};">
            <div class="subject"
                data-bs-toggle="tooltip"
                data-placement="{{ $dataPlacement ?? 'top' }}"
                data-container="body"
                title="{{ ($kanban->item_type == 1) ? $kanban->subject : $kanban->title }}">

                <span 
                    @if($kanban->item_type == 1)
                        id="ticket_card_id" data-id="{{ $kanban->ticketID }}"
                    @endif
                    style="font-weight: 500;">

                    @php
                        $text = ($kanban->item_type == 1)
                            ? $kanban->subject
                            : $kanban->title;
                    @endphp

                    {{ strlen($text) > 20 ? substr($text, 0, 20) . '...' : $text }}
                </span>

                <span class="expand-collapse-icon collapsed" style="margin-right:10px; margin-left:-5px;" aria-expanded="false" data-target=".collapseSection{{ $kanban->id }}">  
                    <svg width="13" height="11" viewBox="0 0 18 11" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.704104 8.20406L8.2041 0.704065C8.30862 0.599185 8.43281 0.51597 8.56956 0.459189C8.7063 0.402408 8.85291 0.373178 9.00098 0.373178C9.14904 0.373178 9.29565 0.402408 9.4324 0.459189C9.56915 0.51597 9.69334 0.599185 9.79785 0.704066L17.2979 8.20407C17.5092 8.41541 17.6279 8.70206 17.6279 9.00094C17.6279 9.29983 17.5092 9.58647 17.2979 9.79782C17.0865 10.0092 16.7999 10.1279 16.501 10.1279C16.2021 10.1279 15.9154 10.0092 15.7041 9.79782L9.00004 3.09375L2.29598 9.79875C2.08463 10.0101 1.79799 10.1288 1.4991 10.1288C1.20022 10.1288 0.913574 10.0101 0.702229 9.79875C0.490885 9.58741 0.37215 9.30076 0.372151 9.00188C0.372151 8.70299 0.490885 8.41635 0.702229 8.205L0.704104 8.20406Z" fill="white"/>
                    </svg>
                </span>
            </div>
        </div>
        <div class="card-body"
            @if($kanban->item_type == 1)
                data-id="{{ $kanban->ticketID }}"
            @elseif($kanban->task_type == 2)
                data-id="{{ $kanban->id }}" data-task-type="{{ $kanban->task_type }}"
            @endif >
            <div class="view-card"
                @if($kanban->item_type == 1)
                    data-id="{{ $kanban->ticketID }}"
                @elseif($kanban->task_type == 2)
                    data-id="{{ $kanban->id }}" data-task-type="{{ $kanban->task_type }}"
                @endif >
                    <div class="card-row">
                        <div>
                        <div class="left-column">
                            <span class="field priorityIcons label-{{ strtolower($kanban->priority->name ?? $kanban->priority ?? '-') }} priority-box">
                                <span data-bs-toggle="tooltip" title="Priority">
                                    {{ $kanban->priority->name ?? $kanban->priority ?? '-' }}
                                </span>
                            </span>

                            @if(!empty($kanban->ticketID))
                                <span class="ticket-id-box" data-bs-toggle="tooltip" title="Ticket ID">
                                    <a href="{{ config('app.url') }}ticket/{{ $kanban->ticketID }}" target="_blank" style="color:#000000;">
                                        #{{ $kanban->ticketID }}
                                    </a>
                                </span>
                            @endif

                            @if(isset($kanban->status_name) && trim($kanban->status_name) && ($kanban->task_type == 2 || $kanban->item_type == 2))
                                <div class="field tat-box" data-toggle="tooltip" data-title="Status">
                                    <span>
                                        {{ strlen($kanban->status_name) > 8 ? substr($kanban->status_name, 0, 8) . '...' : $kanban->status_name }}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($kanban->ticketID) && $kanban->task_count > 0)
                                <div class="task-box ticket-id-box task-data" data-bs-toggle="tooltip" title="Tasks" data-ticket-id="{{ $kanban->ticketID }}">
                                    <span style="color:#E3200B;">
                                        {{ $kanban->completed_tasks_count }}/{{ $kanban->task_count }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if(isset($kanban->id) && ($kanban->task_type == 2))
                            <div style="display:flex;flex-direction:row; align-items:center; margin-top:12px; gap:2px;font-size:smaller" class="collapseSection{{ $kanban->id }}">
                                <span class="ticket-id-box" data-bs-toggle="tooltip" title="Card Id">#{{ $kanban->id }}</span>
                            </div>
                        @endif
                        @php

                            $timeText = 'No expiry';
                            if (!empty($kanban->tat_expire)) {
                                try {
                                    // Parse using DB timezone
                                    $expireDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $kanban->tat_expire, 'UTC')
                                                        ->setTimezone(config('app.timezone'));

                                    $now = \Carbon\Carbon::now();

                                    $diff = $expireDate->diff($now);

                                    if ($diff->days >= 1) {
                                        $timeText = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ' . ($expireDate->greaterThan($now) ? 'left' : 'ago');
                                    } else {
                                        $timeText = $diff->h . 'h ' . $diff->i . 'm ' . ($expireDate->greaterThan($now) ? 'left' : 'ago');
                                    }

                                } catch (\Exception $e) {
                                    $timeText = 'Invalid date';
                                }
                            }
                        @endphp

                        <div style="display:flex;flex-direction:row; align-items:center; margin-top:12px; gap:2px;font-size:smaller">
                            <svg width="14" height="11" viewBox="0 0 14 13" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 0C5.71443 0 4.45772 0.381218 3.3888 1.09545C2.31988 1.80968 1.48676 2.82484 0.994786 4.01256C0.502816 5.20028 0.374095 6.50721 0.624899 7.76809C0.875703 9.02896 1.49477 10.1872 2.40381 11.0962C3.31285 12.0052 4.47104 12.6243 5.73192 12.8751C6.99279 13.1259 8.29973 12.9972 9.48745 12.5052C10.6752 12.0132 11.6903 11.1801 12.4046 10.1112C13.1188 9.04229 13.5 7.78558 13.5 6.5C13.4982 4.77665 12.8128 3.12441 11.5942 1.90582C10.3756 0.687224 8.72335 0.00181989 7 0ZM7 12C5.91221 12 4.84884 11.6774 3.94437 11.0731C3.0399 10.4687 2.33495 9.60975 1.91867 8.60476C1.50238 7.59977 1.39347 6.4939 1.60568 5.427C1.8179 4.36011 2.34173 3.3801 3.11092 2.61091C3.8801 1.84172 4.86011 1.3179 5.92701 1.10568C6.9939 0.893462 8.09977 1.00238 9.10476 1.41866C10.1098 1.83494 10.9687 2.53989 11.5731 3.44436C12.1774 4.34883 12.5 5.4122 12.5 6.5C12.4984 7.95818 11.9184 9.35617 10.8873 10.3873C9.85617 11.4184 8.45819 11.9983 7 12ZM11 6.5C11 6.63261 10.9473 6.75979 10.8536 6.85355C10.7598 6.94732 10.6326 7 10.5 7H7C6.8674 7 6.74022 6.94732 6.64645 6.85355C6.55268 6.75979 6.5 6.63261 6.5 6.5V3C6.5 2.86739 6.55268 2.74022 6.64645 2.64645C6.74022 2.55268 6.8674 2.5 7 2.5C7.13261 2.5 7.25979 2.55268 7.35356 2.64645C7.44733 2.74022 7.5 2.86739 7.5 3V6H10.5C10.6326 6 10.7598 6.05268 10.8536 6.14645C10.9473 6.24022 11 6.36739 11 6.5Z"
                                    fill="currentColor" opacity="0.6"/>
                            </svg>
                            <span class="b5-text">{{ $timeText }}</span>
                        </div>
                        </div>
                        
                        <div class="progress-circle-container collapseSection{{ $kanban->id }}"
                            style="--percentage: {{ $kanban->progress_data['per'] ?? 0 }};
                                --border-color: {{ $kanban->progress_data['borderColor'] ?? '#000' }};"
                            data-bs-toggle="tooltip"
                            title="{{ round($kanban->progress_data['per'] ?? 0) }}%">
                            <div class="progress-circle">
                                <div class="progress-bar-circle" style="display:flex;align-items:center;justify-content:center">
                                    <span style="font-weight:500;" class="b5-text">{{ round($kanban->progress_data['per'] ?? 0) }}%</span>
                                </div>
                            </div>
                            <div style="color:{{ $kanban->progress_data['borderColor'] ?? '#000' }}; text-align:center;font-weight:600;margin-top:2px;font-size:10px">
                                {{ $kanban->progress_data['text'] }}
                            </div>
                        </div>
                    </div>

                {{-- Created & Expected Date --}}
                @if(!empty($kanban->created_at))
                    <div style="display:flex;flex-direction:row; align-items:center; margin-top:10px; gap:2px;font-size:smaller" class="collapseSection{{ $kanban->id }} collapse">
                        <svg width="12" height="11" viewBox="0 0 12 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 1.5H9.5V1C9.5 0.867392 9.44732 0.740215 9.35355 0.646447C9.25979 0.552678 9.13261 0.5 9 0.5C8.86739 0.5 8.74021 0.552678 8.64645 0.646447C8.55268 0.740215 8.5 0.867392 8.5 1V1.5H3.5V1C3.5 0.867392 3.44732 0.740215 3.35355 0.646447C3.25979 0.552678 3.13261 0.5 3 0.5C2.86739 0.5 2.74021 0.552678 2.64645 0.646447C2.55268 0.740215 2.5 0.867392 2.5 1V1.5H1C0.734784 1.5 0.48043 1.60536 0.292893 1.79289C0.105357 1.98043 0 2.23478 0 2.5V12.5C0 12.7652 0.105357 13.0196 0.292893 13.2071C0.48043 13.3946 0.734784 13.5 1 13.5H11C11.2652 13.5 11.5196 13.3946 11.7071 13.2071C11.8946 13.0196 12 12.7652 12 12.5V2.5C12 2.23478 11.8946 1.98043 11.7071 1.79289C11.5196 1.60536 11.2652 1.5 11 1.5ZM2.5 2.5V3C2.5 3.13261 2.55268 3.25979 2.64645 3.35355C2.74021 3.44732 2.86739 3.5 3 3.5C3.13261 3.5 3.25979 3.44732 3.35355 3.35355C3.44732 3.25979 3.5 3.13261 3.5 3V2.5H8.5V3C8.5 3.13261 8.55268 3.25979 8.64645 3.35355C8.74021 3.44732 8.86739 3.5 9 3.5C9.13261 3.5 9.25979 3.44732 9.35355 3.35355C9.44732 3.25979 9.5 3.13261 9.5 3V2.5H11V4.5H1V2.5H2.5ZM11 12.5H1V5.5H11V12.5Z"
                                fill="currentColor" opacity="0.6"/>
                        </svg>

                        <div>
                            <span data-bs-toggle="tooltip" title="Created Date" class="b5-text">
                                {{ \Carbon\Carbon::parse($kanban->created_at)->format('d M, h:i a') }}
                            </span>

                            @if(!empty($kanban->expected_date) || !empty($kanban->tat_expire))
                                -
                                @if($kanban->item_type == 1)
                                    <span data-bs-toggle="tooltip" title="TAT Expire Date" class="b5-text">
                                        {{ \Carbon\Carbon::parse($kanban->tat_expire)->format('d M, h:i a') }}
                                    </span>
                                @elseif($kanban->task_type == 2)
                                    <span data-bs-toggle="tooltip" title="Expected Date" class="b5-text">
                                        {{ \Carbon\Carbon::parse($kanban->expected_date)->format('d M, h:i a') }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row" style="display:{{$attachments? 'block' : 'none'}}">
            @if(!empty($attachments) && isset($attachments[0]['file_name']))
                @php
                    $firstAttachment = $attachments[0];
                    $fileExt = pathinfo($firstAttachment['file_name'], PATHINFO_EXTENSION);
                    $fileExt = strtolower($fileExt);

                    // Determine URL
                    if($kanban->item_type == 1) {
                        $fileUrl = asset('ticket/attachment/view/' . $firstAttachment['id'] . '/1');
                    } elseif($kanban->task_type == 2) {
                        $fileUrl = asset('ticket/kanban-board/card_attachment/view/' . $firstAttachment['id']);
                    }

                    // Determine icon for non-images
                    $iconClass = 'fa-file text-muted'; // default grey
                    if ($fileExt === 'pdf') {
                        $iconClass = 'fa-file-pdf-o text-danger'; // red
                    } elseif (in_array($fileExt, ['xls', 'xlsx'])) {
                        $iconClass = 'fa-file-excel-o text-success'; // green
                    } elseif (in_array($fileExt, ['doc', 'docx'])) {
                        $iconClass = 'fa-file-word-o text-primary'; // blue
                    } elseif (in_array($fileExt, ['ppt','pptx'])) {
                        $iconClass = 'fa-file-powerpoint-o text-warning'; // orange
                    } elseif (in_array($fileExt, ['zip','rar'])) {
                        $iconClass = 'fa-file-archive-o text-warning'; // yellow/orange
                    }
                    $attachmentCount = count($attachments);
                @endphp

                @if(in_array($fileExt, ['jpg','jpeg','png','gif','webp']))
                    <!-- Image -->
                    <div class="card-image collapseSection{{ $kanban->id }}" style="width:100%; position:relative; height:100px; overflow:hidden;margin:10px 0px 10px 0px; border-radius:12px 12px 12px 12px;box-shadow: rgba(0, 0, 0, 0.06) 0px 2px 4px 0px inset;">
                        <img src="{{ $fileUrl }}"
                            alt="{{ $firstAttachment['file_name'] }}"
                            style="width:100%; height:100%; object-fit:cover;" />
                            @if($attachmentCount > 0)
                                <div class="more-attachments-icon"
                                    @if($kanban->item_type == 1) 
                                        data-ticketid="{{ $kanban->ticketID }}"
                                    @elseif($kanban->task_type == 2) 
                                        data-cardid="{{ $kanban->id }}"
                                    @endif
                                >
                                    <i class="fa fa-paperclip"></i> {{ $attachmentCount }}
                                </div>
                            @endif
                        <span style="position: absolute; bottom: 10px; left: 18px; color: white; font-weight: 500; text-shadow: 0 0 5px black;">
                            {{ \Illuminate\Support\Str::limit($attachments[0]['file_name'], 20, '...') }}
                        </span>
                    </div>
                @else
                    <!-- Non-image file -->
                    <div class="card-file collapseSection{{ $kanban->id }}" style="width:100%; position:relative; height:100px; display:flex; align-items:center;margin:10px 0px 10px 0px; justify-content:center; border-radius:12px 12px 12px 12px; background:#f0f0f0;box-shadow: rgba(0, 0, 0, 0.06) 0px 2px 4px 0px inset;">
                        <span style="text-align:center; color:#333; text-decoration:none;">
                            <i class="fa {{ $iconClass }}" style="font-size:48px;"></i>
                             @if($attachmentCount > 1)
                                <div class="more-attachments-icon"
                                    @if($kanban->item_type == 1) 
                                        data-ticketid="{{ $kanban->ticketID }}"
                                    @elseif($kanban->task_type == 2) 
                                        data-cardid="{{ $kanban->id }}"
                                    @endif
                                >
                                    <i class="fa fa-paperclip"></i> {{ $attachmentCount }}
                                </div>
                            @endif
                        </span>
                        <span style="position: absolute; bottom: 10px; left: 18px; color: white; font-weight: 500; text-shadow: 0 0 5px black;">
                            {{ \Illuminate\Support\Str::limit($attachments[0]['file_name'], 20, '...') }}
                        </span>
                    </div>
                @endif
            @endif
        </div>
        <!-- <hr class="light-gray-line"> -->
        <div class="card-footer">
            <div style=" display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;white-space: normal;">
                <div class="card-row" style="margin-top:0px">
                    <div>
                        <div class="left-column">
                            {{-- Assigned Users --}}
                            <div style="display:flex;flex-direction:row;">
                                @if(!empty($kanban->assigned_name))
                                    @php
                                        $assignedNames = explode(',', $kanban->assigned_name);
                                        $assignedAvatars = !empty($kanban->profile_image) ? explode(',', $kanban->profile_image) : [];
                                        $totalUsers = count($assignedNames);
                                        $visibleCount = $totalUsers >= 3 ? 2 : $totalUsers;
                                        $extraCount = $totalUsers > 2 ? $totalUsers - 2 : 0;
                                    @endphp
                                    <div style="display:flex; align-items:center;">
                                        @foreach(array_slice($assignedNames, 0, $visibleCount) as $index => $name)
                                            @php $avatar = $assignedAvatars[$index] ?? null; @endphp
                                            @if($avatar)
                                                <img src="{{ $avatar }}" alt="{{ $name }}"
                                                    class="user-image main-user" data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top" data-content="{{$name}}"
                                                    style="width:20px; height:20px; border-radius:50%;
                                                    {{ $totalUsers > 1 ? 'margin-right:-8px; border:2px solid #fff;' : '' }}
                                                    object-fit:cover;">
                                            @else
                                                <div class="userInitials main-user" data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top" data-content="{{$name}}"
                                                    style="width:20px; height:20px; border-radius:50%; background:#ccc; color:#fff; 
                                                    font-size:8px; display:flex; align-items:center; justify-content:center; 
                                                    margin-right:-8px; border:2px solid #fff;">
                                                    {{ strtoupper(substr($name, 0, 1)) }}
                                                </div>
                                            @endif
                                        @endforeach

                                        @if($extraCount > 0)
                                            <div style="width:20px; height:20px; border-radius:50%; background:#E5F9FF; color:#0044FF;
                                                font-size:8px; display:flex; align-items:center; justify-content:center; 
                                                border:2px solid #fff; margin-right:-8px;"
                                                title="+{{ $extraCount }} more">
                                                +{{ $extraCount }}
                                            </div>
                                        @endif
                                    </div>

                                    <span style="margin-left:{{ $totalUsers == 1 ? '0px' : '10px' }}" data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top" data-content="{!! implode(', <br>', array_map('e', $assignedNames)) !!}">
                                        {{ strlen(implode(', ', $assignedNames)) > 10 ? substr(implode(', ', $assignedNames), 0, 10).'...' : implode(', ', $assignedNames) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: row; gap:2px;">
                        @if($archived !== 'true')  
                            @if(!in_array($kanban->status_id, [5, 6]) && in_array($status->access_type, [1, 2]))
                                    @if($kanban->item_type == 1)
                                        <span class="js-act-edit-ticket"
                                            id="editButton"
                                            data-id="{{ $kanban->ticketID }}"
                                            data-bs-toggle="tooltip"
                                            title="Edit"
                                            data-status='@json($status)'>
                                                <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">
                                                    <svg width="16" height="16" viewBox="0 0 19 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.8167 3.62219L14.5556 0.344414C14.3401 0.130006 14.0485 0.00964355 13.7445 0.00964355C13.4405 0.00964355 13.1489 0.130006 12.9334 0.344414L1.37225 11.8889L0.316698 16.4444C0.280285 16.6109 0.281533 16.7835 0.320352 16.9495C0.35917 17.1155 0.434578 17.2707 0.541067 17.4038C0.647555 17.5369 0.782435 17.6446 0.935852 17.7189C1.08927 17.7932 1.25735 17.8323 1.42781 17.8333C1.50721 17.8419 1.5873 17.8419 1.6667 17.8333L6.27225 16.7777L17.8167 5.24441C18.0311 5.02892 18.1515 4.73729 18.1515 4.4333C18.1515 4.12931 18.0311 3.83769 17.8167 3.62219ZM5.7167 15.7777L1.40003 16.6833L2.38336 12.45L11.0334 3.8333L14.3667 7.16664L5.7167 15.7777ZM15.1111 6.36108L11.7778 3.02775L13.7111 1.10553L16.9889 4.43886L15.1111 6.36108Z" fill="currentColor"/>
                                                    </svg>
                                                </span> 
                                        </span>
                                    @elseif($kanban->task_type == 2)
                                        @can('KanbanCutomBoardCardEdit')
                                            <span id="editcard"
                                                data-id="{{ $kanban->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Edit"
                                                data-status='@json($status)'>
                                                    <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">            
                                                        <svg width="16" height="16" viewBox="0 0 19 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.8167 3.62219L14.5556 0.344414C14.3401 0.130006 14.0485 0.00964355 13.7445 0.00964355C13.4405 0.00964355 13.1489 0.130006 12.9334 0.344414L1.37225 11.8889L0.316698 16.4444C0.280285 16.6109 0.281533 16.7835 0.320352 16.9495C0.35917 17.1155 0.434578 17.2707 0.541067 17.4038C0.647555 17.5369 0.782435 17.6446 0.935852 17.7189C1.08927 17.7932 1.25735 17.8323 1.42781 17.8333C1.50721 17.8419 1.5873 17.8419 1.6667 17.8333L6.27225 16.7777L17.8167 5.24441C18.0311 5.02892 18.1515 4.73729 18.1515 4.4333C18.1515 4.12931 18.0311 3.83769 17.8167 3.62219ZM5.7167 15.7777L1.40003 16.6833L2.38336 12.45L11.0334 3.8333L14.3667 7.16664L5.7167 15.7777ZM15.1111 6.36108L11.7778 3.02775L13.7111 1.10553L16.9889 4.43886L15.1111 6.36108Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                            </span>
                                        @endcan
                                    @endif

                                @if ($status->access_type == 2 || $status->access_type == 1)
                                    @if ($kanban->item_type == 1)
                                        @if (($status->item_type != 5 || $status->item_type != 6) && isset($action_controls['ctrl_transfer']) && $action_controls['ctrl_transfer'] == 1)
                                            <span class="js-act-transfer"
                                                data-id="{{ $kanban->ticketID }}"
                                                data-bs-toggle="tooltip"
                                                title="Transfer"
                                                data-status='@json($status)'>
                                                    <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">
                                                        <svg width="14" height="25" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17 7.03888H1L6.5 1.03888M1 11.0389H17L11.5 17.0389" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                            </span>
                                        @endif
                                    @endif
                                @endif


                                @if ($status->access_type != 3)
                                    @if ($kanban->item_type == 1)
                                        @if (
                                            ($status->item_type != 5 || $status->item_type != 6) &&
                                                isset($action_controls['ctrl_assign']) &&
                                                $action_controls['ctrl_assign'] == 1)
                                                <span id="assignedToButton"
                                                    data-id="{{ $kanban->ticketID }}"
                                                    data-bs-toggle="tooltip"
                                                    title="Assigned To"
                                                    data-status='@json($status)'>
                                                        <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">            
                                                            <svg width="12" height="21" viewBox="0 0 18 21" fill="currentColor"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M9 8.96124C9.39397 8.96124 9.78407 8.88365 10.1481 8.73288C10.512 8.58212 10.8427 8.36114 11.1213 8.08256C11.3999 7.80399 11.6209 7.47327 11.7716 7.10929C11.9224 6.74532 12 6.35521 12 5.96124C12 5.56728 11.9224 5.17717 11.7716 4.81319C11.6209 4.44922 11.3999 4.1185 11.1213 3.83992C10.8427 3.56135 10.512 3.34037 10.1481 3.1896C9.78407 3.03884 9.39397 2.96124 9 2.96124C8.20435 2.96124 7.44129 3.27731 6.87868 3.83992C6.31607 4.40253 6 5.16559 6 5.96124C6 6.75689 6.31607 7.51995 6.87868 8.08256C7.44129 8.64517 8.20435 8.96124 9 8.96124ZM9 10.9612C10.3261 10.9612 11.5979 10.4345 12.5355 9.49678C13.4732 8.55909 14 7.28732 14 5.96124C14 4.63516 13.4732 3.36339 12.5355 2.42571C11.5979 1.48803 10.3261 0.961243 9 0.961243C7.67392 0.961243 6.40215 1.48803 5.46447 2.42571C4.52678 3.36339 4 4.63516 4 5.96124C4 7.28732 4.52678 8.55909 5.46447 9.49678C6.40215 10.4345 7.67392 10.9612 9 10.9612ZM1.639 14.4092C2.784 12.8912 4.509 11.9612 6.714 11.9612H11.286C13.491 11.9612 15.216 12.8912 16.361 14.4092C17.482 15.8962 18 17.8772 18 19.9612C18 20.2265 17.8946 20.4808 17.7071 20.6684C17.5196 20.8559 17.2652 20.9612 17 20.9612C16.7348 20.9612 16.4804 20.8559 16.2929 20.6684C16.1054 20.4808 16 20.2265 16 19.9612C16 18.1792 15.554 16.6612 14.765 15.6132C14 14.5992 12.867 13.9612 11.285 13.9612H6.715C5.133 13.9612 4 14.5992 3.235 15.6132C2.445 16.6612 2 18.1792 2 19.9612C2 20.2265 1.89464 20.4808 1.70711 20.6684C1.51957 20.8559 1.26522 20.9612 1 20.9612C0.734784 20.9612 0.48043 20.8559 0.292893 20.6684C0.105357 20.4808 0 20.2265 0 19.9612C0 17.8772 0.518 15.8962 1.639 14.4092Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                </span>
                                        @endif
                                    @endif
                                @endif
                            @endif

                            @if ($kanban->task_type == 2)
                                @can('KanbanCutomBoardCardDelete')  
                                    <span class="delete-card"
                                        data-id="{{ $kanban->id }}"
                                        data-bs-toggle="tooltip"
                                        title="Delete"
                                        data-status='@json($status)'>
                                            <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">            
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="25" fill="currentColor"
                                                    viewBox="0 0 256 256">
                                                    <path
                                                        d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z">
                                                    </path>
                                                </svg>
                                            </span>
                                    </span>
                                @endcan
                            @endif
                        @endif
                        @if ($kanban->item_type == 1) 
                            <span id="ticketHistoryButton" data-id="{{ $kanban->ticketID }}" data-bs-toggle="tooltip" title="Ticket History" data-status='@json($status)'>
                                <svg width="17" height="25" viewBox="0 0 24 25" xmlns="http://www.w3.org/2000/svg" aria-labelledby="historyIconTitle" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" color="currentColor"> <title id="historyIconTitle">History</title> <polyline points="1 12 3 14 5 12"/> <polyline points="12 7 12 12 15 15"/> <path d="M12,21 C16.9705627,21 21,16.9705627 21,12 C21,7.02943725 16.9705627,3 12,3 C7.02943725,3 3,7.02943725 3,12 C3,11.975305 3,12.3086383 3,13"/> </svg>
                            </span>
                        @else
                            <span class="card-history" data-id="{{ $kanban->id }}" data-bs-toggle="tooltip" title="Card History">
                                <svg width="17" height="25" viewBox="0 0 24 25" xmlns="http://www.w3.org/2000/svg" aria-labelledby="historyIconTitle" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" color="currentColor"> <title id="historyIconTitle">History</title> <polyline points="1 12 3 14 5 12"/> <polyline points="12 7 12 12 15 15"/> <path d="M12,21 C16.9705627,21 21,16.9705627 21,12 C21,7.02943725 16.9705627,3 12,3 C7.02943725,3 3,7.02943725 3,12 C3,11.975305 3,12.3086383 3,13"/> </svg>
                            </span>
                        @endif

                        <div class="view-card"
                            data-bs-toggle="tooltip"
                            title="View Card"
                            @if($kanban->item_type == 1)
                                data-id="{{ $kanban->ticketID }}"
                            @elseif($kanban->task_type == 2)
                                data-id="{{ $kanban->id }}" data-task-type="{{ $kanban->task_type }}"
                            @endif >
                            <span style="display:flex; align-items:center;justify-content:center;height:25px; width:20px;">
                                <svg width="16" height="16" viewBox="0 0 20 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.1649 6.63213C19.1375 6.57041 18.4758 5.10244 17.0047 3.63135C15.0445 1.67119 12.5688 0.635254 9.84375 0.635254C7.11875 0.635254 4.64296 1.67119 2.68281 3.63135C1.21171 5.10244 0.546868 6.57275 0.522649 6.63213C0.487112 6.71206 0.46875 6.79856 0.46875 6.88603C0.46875 6.97351 0.487112 7.06001 0.522649 7.13994C0.549993 7.20166 1.21171 8.66885 2.68281 10.1399C4.64296 12.0993 7.11875 13.1353 9.84375 13.1353C12.5688 13.1353 15.0445 12.0993 17.0047 10.1399C18.4758 8.66885 19.1375 7.20166 19.1649 7.13994C19.2004 7.06001 19.2188 6.97351 19.2188 6.88603C19.2188 6.79856 19.2004 6.71206 19.1649 6.63213ZM9.84375 11.8853C7.43906 11.8853 5.33828 11.011 3.59921 9.2876C2.88565 8.57798 2.27858 7.76881 1.79687 6.88525C2.27845 6.00161 2.88554 5.19242 3.59921 4.48291C5.33828 2.75947 7.43906 1.88525 9.84375 1.88525C12.2484 1.88525 14.3492 2.75947 16.0883 4.48291C16.8032 5.19225 17.4116 6.00144 17.8945 6.88525C17.3313 7.93682 14.8773 11.8853 9.84375 11.8853ZM9.84375 3.13525C9.10207 3.13525 8.37704 3.35519 7.76036 3.76724C7.14367 4.1793 6.66303 4.76497 6.3792 5.45019C6.09537 6.13541 6.02111 6.88941 6.1658 7.61684C6.3105 8.34427 6.66765 9.01246 7.1921 9.53691C7.71654 10.0614 8.38473 10.4185 9.11216 10.5632C9.83959 10.7079 10.5936 10.6336 11.2788 10.3498C11.964 10.066 12.5497 9.58533 12.9618 8.96864C13.3738 8.35196 13.5938 7.62693 13.5938 6.88525C13.5927 5.89101 13.1973 4.93778 12.4943 4.23475C11.7912 3.53171 10.838 3.13629 9.84375 3.13525ZM9.84375 9.38525C9.3493 9.38525 8.86595 9.23863 8.45482 8.96393C8.0437 8.68922 7.72327 8.29878 7.53405 7.84196C7.34483 7.38515 7.29532 6.88248 7.39178 6.39753C7.48825 5.91258 7.72635 5.46712 8.07598 5.11749C8.42561 4.76786 8.87107 4.52975 9.35602 4.43329C9.84098 4.33683 10.3436 4.38634 10.8005 4.57555C11.2573 4.76477 11.6477 5.08521 11.9224 5.49633C12.1971 5.90745 12.3438 6.3908 12.3438 6.88525C12.3438 7.5483 12.0804 8.18418 11.6115 8.65302C11.1427 9.12186 10.5068 9.38525 9.84375 9.38525Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach