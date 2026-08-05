{{-- @page-meta
{
    "page_no": "STSC-26",
    "file": "status_changed.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-06",
        "reviewer": null,
        "description": "Ticket status change Mail"
        },
        {
        "version": "1.1",
        "writer": "santosh upadhyay",
        "from": "2026-07",
        "reviewer": null,
        "description": "Outlook-compatible rewrite (inline styles, cell-level backgrounds, width attributes)"
        },
        {
        "version": "1.2",
        "writer": "shivam kumar",
        "from": "2026-07",
        "reviewer": null,
        "description": "New modern layout with greeting icon, status change header, details table, dynamic status messages, and CTA"
        }
    ]
}
--}}
@extends('mail.ticket_mailtemplates')

@section('content')
@php
    // Theme colour (Outlook ignores <style> blocks in body, so it must be inline)
    $themeColor = CommonHelper::settings()->css_theme == 2 ? '#08558b' : '#e30613';
    
    // Reusable styles — Outlook (Word engine) needs padding, font,
    // line-height and background on every cell, not on <tr> or in <style>.
    $thBase = "padding:11px 16px; font-size:14px; line-height:20px; mso-line-height-rule:exactly; margin:0; text-align:left; font-family:'Inter',Arial,sans-serif; vertical-align:middle; font-weight:500; color:#4a5568;";
    $tdBase = "padding:11px 16px; font-size:14px; line-height:20px; mso-line-height-rule:exactly; margin:0; text-align:left; font-family:'Inter',Arial,sans-serif; color:#1a1a1a; font-weight:500; vertical-align:middle;";
    $pBase  = "text-align:left; font-family:'Inter',Arial,sans-serif; font-size:14px; line-height:20px; mso-line-height-rule:exactly; color:#333333; margin:0 0 12px 0;";
    $dBase  = "text-align:left; font-family:'Inter',Arial,sans-serif; font-size:14px; line-height:20px; mso-line-height-rule:exactly; color:#333333; margin:0 0 12px 0;";
    
    $rowIndex = 0;
@endphp

    <!-- Greeting with Icon -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding-bottom:18px;">
                <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block;">
                    <tr>
                        <td style="padding-right:10px; vertical-align:middle;">
                            <span style="display:inline-block; width:28px; height:28px; border-radius:50%; background:#e8eef5; text-align:center; line-height:28px; vertical-align:middle; overflow:hidden;">
                                @if($user && $user->getProfileImg())
                                    <img src="{{ $user->getProfileImg() }}" alt="{{ ucfirst($user->first_name) }}" 
                                         style="width:28px; height:28px; border-radius:50%; display:block; object-fit:cover; border:none;"/>
                                @else
                                    <svg viewBox="0 0 24 24" width="16" height="16" style="fill:#6b7c93; vertical-align:middle;">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                @endif
                            </span>
                        </td>
                        <td style="font-size:15px; color:#333; font-weight:400; vertical-align:middle; font-family:'Inter',Arial,sans-serif;">
                            Hello @if($user) {{ ucfirst($user->first_name) }}@else User @endif,
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Header - Status Changed -->
    <h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
        Ticket Status <span style="color:{{ $themeColor }};">Changed</span>
    </h1>
    <p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
        Ticket ID: <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b>
    </p>
    <p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
        Status: <b>{{ optional($ticket->status)->name }}</b>
    </p>

    <!-- Details Table -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
        
        <!-- Ticket ID -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td width="38%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket ID</td>
            <td width="62%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->ticket_tag ?? '###'. $ticket->id."###" }}</td>
        </tr>

        @if(!empty($ticket->decodedSubject()))
        <!-- Subject -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Subject</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->decodedSubject() }}</td>
        </tr>
        @endif

        @if(!empty(optional($ticket->department)->name))
        <!-- Department -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Department</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->department)->name }}</td>
        </tr>
        @endif

        @if(!empty(optional($ticket->problemCategory)->name))
        <!-- Category -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Category</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->problemCategory)->name }}</td>
        </tr>
        @endif

        @if(!empty(optional($ticket->subCategory)->name))
        <!-- Sub Category -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Sub Category</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->subCategory)->name }}</td>
        </tr>
        @endif

        @php
            $embedded_attachments = App\Models\Ticket\Attachment::getEmbeddedAttachments($ticket->id);
            $processedContent = CommonHelper::renderTktContent($ticket->content, $embedded_attachments);
            $processedContent = str_replace('&nbsp;', ' ', $processedContent);
            $limitedContent = Illuminate\Support\Str::limit(strip_tags($processedContent), 500, '...');
        @endphp

        @if(!empty($limitedContent))
        <!-- Content -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Content</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{!! $limitedContent !!}</td>
        </tr>
        @endif

        @if(!empty(optional($ticket->status)->name))
        <!-- Status -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Status</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                <span style="color:#7b2d8e; font-weight:550; background:#f8e6ff; border-radius:23px; padding:5px 10px; display:inline-block;">{{ optional($ticket->status)->name }}</span>
            </td>
        </tr>
        @endif

        @if(!empty(optional($ticket->priority)->name))
        <!-- Priority -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Priority</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->priority)->name }}</td>
        </tr>
        @endif

        @if(!empty($ticket->tat))
        <!-- TAT -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">TAT</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->tat }} Hrs</td>
        </tr>
        @endif

        @if(!empty($formattedTatExpire))
        <!-- TAT Expire -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">TAT Expire At</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $formattedTatExpire }}</td>
        </tr>
        @endif

        @if(!empty($engineer_name))
        <!-- Assigned To -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Assigned To</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                <table cellpadding="0" cellspacing="0" border="0" style="display:inline-table;">
                    <tr>
                        <td style="padding-right:6px; vertical-align:middle;">
                            <span style="display:inline-block; width:22px; height:22px; border-radius:50%; background:#e8eef5; text-align:center; line-height:22px; vertical-align:middle; overflow:hidden;">
                                <svg viewBox="0 0 24 24" width="12" height="12" style="fill:#6b7c93; vertical-align:middle; display:inline-block;">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </span>
                        </td>
                        <td style="vertical-align:middle;">{{ $engineer_name }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        @endif
    </table>

    <!-- Dynamic Status Messages -->
    <div style="margin-top:20px; text-align:left;">
    @if( $tf->updated_status == 3 )
        <!-- Status: In Progress -->
        <p style="{{ $pBase }}">Please be informed that your ticket <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b> status has been moved to <b>"{{ $tf->status->name }}"</b>. Our engineer will reach out to you for further query.</p>

        <p style="{{ $pBase }}">Here is the comment from {{ $engineer_name }},</p>

        <div style="{{ $dBase }} background:#f8f9fa; padding:12px 16px; border-radius:6px; border-left:4px solid {{ $themeColor }}; margin-bottom:16px;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

        <p style="{{ $pBase }}">We will keep you posted on the progress.</p>

        <p style="{{ $pBase }}">You can access the update also on the following link.</p>

        <!-- CTA Button -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" style="padding-bottom:20px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View Ticket <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @elseif( $tf->updated_status == 4 )
        <!-- Status: On Hold -->
        <p style="{{ $pBase }}">This is to update you that ticket id <strong>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</strong> status has been marked <b>"{{ $tf->status->name }}"</b> due to some issues.</p>

        <p style="{{ $pBase }}">Here is the comment from {{ $engineer_name }},</p>

        <div style="{{ $dBase }} background:#f8f9fa; padding:12px 16px; border-radius:6px; border-left:4px solid {{ $themeColor }}; margin-bottom:16px;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

        <p style="{{ $pBase }}">Sorry for the delay and we are doing our best to solve this issue at the earliest.</p>

        <p style="{{ $pBase }}">You can expect a response shortly from our team members. Meanwhile will keep you posted.</p>

        <p style="{{ $pBase }}">You can access further updates on this ticket using the following link</p>

        <!-- CTA Button -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" style="padding-bottom:20px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View Ticket <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @elseif( $tf->updated_status == 7 )
        <!-- Status: Waiting for User -->
        <p style="{{ $pBase }}">This is to inform you that ticket <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b> is marked as <b>"{{ $tf->status->name }}"</b></p>

        <p style="{{ $pBase }}">Your input is required to proceed further for the resolution. You can respond to this mail or update into the ticket portal with necessary information at the earliest.</p>

        <p style="{{ $pBase }}">We appreciate your timely response to help bring this ticket to closure.</p>

        <p style="{{ $pBase }}">Here is the comment from {{ $engineer_name }},</p>

        <div style="{{ $dBase }} background:#f8f9fa; padding:12px 16px; border-radius:6px; border-left:4px solid {{ $themeColor }}; margin-bottom:16px;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

        <p style="{{ $pBase }}">We will update the ticket status as soon as response is received.</p>

        <p style="{{ $pBase }}">You can access further updates on this ticket using the following link</p>

        <!-- CTA Button -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" style="padding-bottom:20px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View and Update Ticket <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @elseif( $tf->updated_status == 9 )
        <!-- Status: Approval Pending -->
        <p style="{{ $pBase }}">It is to remind that ticket request-id <strong>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</strong> is <b>"{{ $tf->status->name }}"</b> by team.</p>

        <p style="{{ $pBase }}">Here is the comment from {{ $engineer_name }},</p>

        <div style="{{ $dBase }} background:#f8f9fa; padding:12px 16px; border-radius:6px; border-left:4px solid {{ $themeColor }}; margin-bottom:16px;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

        <p style="{{ $pBase }}">The team member is requested to respond back soon and update the approval. Make sure the ticket resolved soon.</p>

        <p style="{{ $pBase }}">You can access further updates on this ticket using the following link</p>

        <!-- CTA Button -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" style="padding-bottom:20px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View Ticket <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @elseif( $tf->updated_status == 2 )
        <!-- Status: Reopened -->
        <p style="{{ $pBase }}">We noticed that your ticket <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b> is <b>"{{ $tf->status->name }}"</b>. We apologize that you are facing the issue again. Engineer will work on this and reach out to you in case of any additional information required to resolve this issue.</p>

        <p style="{{ $pBase }}">We appreciate your time and cooperation.</p>

        <p style="{{ $pBase }}">Here is the comment from {{ $engineer_name }},</p>

        <div style="{{ $dBase }} background:#f8f9fa; padding:12px 16px; border-radius:6px; border-left:4px solid {{ $themeColor }}; margin-bottom:16px;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

        <p style="{{ $pBase }}">You can access further updates on this ticket using the following link</p>

        <!-- CTA Button -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" style="padding-bottom:20px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View Ticket <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @elseif( $tf->updated_status == 8 )
        <!-- Status: Request for Information -->
        <p style="{{ $pBase }}">It is an update on the ticket request-id <strong>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</strong> is <b>"{{ $tf->status->name }}"</b>. So, team can resolve the issue at the earliest.</p>

        <p style="{{ $pBase }}">Here is the comment from {{ $engineer_name }},</p>

        <div style="{{ $dBase }} background:#f8f9fa; padding:12px 16px; border-radius:6px; border-left:4px solid {{ $themeColor }}; margin-bottom:16px;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

        <p style="{{ $pBase }}">As soon as we receive the reply, we will update the ticket status.</p>

        <p style="{{ $pBase }}">You can access further updates on this ticket using the following link</p>

        <!-- CTA Button -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" style="padding-bottom:20px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View and Update Ticket <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @else
        <!-- Default/Other Status -->
        <p style="{{ $pBase }}">This is to inform you that ticket <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b> is marked as <b>"{{ $ticket->status->name }}"</b></p>

        <p style="{{ $pBase }}">Here is the comment from {{ $engineer_name }},</p>

        <div style="{{ $dBase }} background:#f8f9fa; padding:12px 16px; border-radius:6px; border-left:4px solid {{ $themeColor }}; margin-bottom:16px;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

        <p style="{{ $pBase }}">We will keep you posted on the progress.</p>

        <p style="{{ $pBase }}">You can access further updates on this ticket using the following link</p>

        <!-- CTA Button -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="left" style="padding-bottom:20px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View Ticket <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif
    </div>
    <!-- Back Trails (if enabled) -->
    @if($add_back_trail && count($back_trails))
        @foreach($back_trails as $bt)
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="border-top:1px solid #e8e8e8; font-size:0; line-height:0; padding:0;" height="1">&nbsp;</td>
                </tr>
            </table>
            <p style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:12px; line-height:18px; mso-line-height-rule:exactly; color:#666666; margin:10px 0 6px 0;">{!! \Illuminate\Support\Str::limit(strip_tags(CommonHelper::renderTktContent($bt->msg)), 500, '...') !!}</p>
            <p style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:13px; line-height:19px; mso-line-height-rule:exactly; color:#333333; margin:0 0 12px 0;">{!! \Illuminate\Support\Str::limit(strip_tags(CommonHelper::renderTktContent($bt->remarks, $embedded_attachments, true)), 500, '...') !!}</p>
        @endforeach
    @endif

@endsection