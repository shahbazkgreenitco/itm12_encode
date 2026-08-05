{{-- @page-meta
{
"page_no": "TR-26",
"file": "intimate_success_creation.blade.php",
"versions": [
{
"version": "1.0",
"writer": "shivam kumar",
"from": "2026-06",
"reviewer": null,
"description": "Task Remove Mail"
},
]
}
--}}
@extends('mail.ticket_mailtemplates')

@section('content')

    @php
        $themeColor = CommonHelper::settings()->css_theme == 2 ? '#08558b' : '#e30613';
        $thBase = "padding:11px 16px; font-size:14px; line-height:20px; mso-line-height-rule:exactly; margin:0; text-align:left; font-family:'Inter',Arial,sans-serif; vertical-align:middle; font-weight:500; color:#4a5568;";
        $tdBase = "padding:11px 16px; font-size:14px; line-height:20px; mso-line-height-rule:exactly; margin:0; text-align:left; font-family:'Inter',Arial,sans-serif; color:#1a1a1a; font-weight:500; vertical-align:middle;";
        $pBase = "text-align:left; font-family:'Inter',Arial,sans-serif; font-size:14px; line-height:20px; mso-line-height-rule:exactly; color:#1a1a1a; margin:0 0 12px 0;";
        $rowIndex = 0;
        $isDarkRow = false;
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding-bottom:18px;">
                <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block;">
                    <tr>
                        <td style="padding-right:10px; vertical-align:middle;">
                            <span style="display:inline-block; width:28px; height:28px; border-radius:50%; background:#e8eef5; text-align:center; line-height:28px; vertical-align:middle; overflow:hidden;">
                                @if($user && $user->getProfileImg())
                                    <img src="{{ $user->getProfileImg() }}" alt="{{ $user->first_name }}" 
                                        style="width:28px; height:28px; border-radius:50%; display:block; object-fit:cover; border:none;"/>
                                @else
                                    <span style="display:inline-block; width:28px; height:28px; background:#e8eef5; border-radius:50%; font-size:12px; color:#6b7c93; line-height:28px; text-align:center; font-weight:500;">
                                        {{ $user ? strtoupper(substr($user->first_name, 0, 1)) : 'U' }}
                                    </span>
                                @endif
                            </span>
                        </td>
                        <td
                            style="font-size:15px; color:#333; font-weight:400; vertical-align:middle; font-family:'Inter',Arial,sans-serif;">
                            Hello @if($user) {{ $user->first_name??'' }}@else User @endif,
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <h1
        style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
        We have received your ticket<br>
        <span style="color:{{ $themeColor }};">successfully</span>.
    </h1>
    <p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
        Your service ticket reference no: <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b>
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">

        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        
        <tr style="background:{{ $bg }};">
            <td width="38%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                Ticket ID</td>
            <td width="62%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                {{ $ticket->ticket_tag ?? '###' . $ticket->id . '###' }}</td>
        </tr>

        @if(!empty($ticket->decodedSubject()))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Subject</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $ticket->decodedSubject() }}</td>
            </tr>
        @endif

        @if(!empty(optional($ticket->company)->name))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Company</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $ticket->company->name }}</td>
            </tr>
        @endif

        @if(!empty(optional($ticket->department)->name))
            
            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
            
            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Department</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $ticket->department->name }}</td>
            </tr>
        @endif

        @if(!empty(optional($ticket->problemCategory)->name))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Category</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $ticket->problemCategory->name }}</td>
            </tr>
        @endif

        @if(!empty(optional($ticket->subCategory)->name))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Sub Category</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ optional($ticket->subCategory)->name }}</td>
            </tr>
        @endif

        @php
            $embedded_attachments = App\Models\Ticket\Attachment::getEmbeddedAttachments($ticket->id);
            $processedContent = CommonHelper::renderTktContent($ticket->content, $embedded_attachments);
            $processedContent = str_replace('&nbsp;', ' ', $processedContent);
            $limitedContent = \Illuminate\Support\Str::limit(strip_tags($processedContent), 500, '...');
        @endphp

        @if(!empty(trim($limitedContent)))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Content</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $limitedContent }}</td>
            </tr>
        @endif

        <!-- Status -->
        @if(!empty(optional($ticket->status)->name))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Status</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    <span
                        style="color:#7b2d8e; font-weight:550; background:#f8e6ff; border-radius:23px; padding:5px 10px; display:inline-block;">{{ $ticket->status->name }}</span>
                </td>
            </tr>
        @endif

        <!-- Priority -->
        @if(!empty(optional($ticket->priority)->name))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Priority</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $ticket->priority->name }}</td>
            </tr>
        @endif

        <!-- TAT -->
        @if(!empty($ticket->tat))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">TAT</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $ticket->tat }} Hrs</td>
            </tr>
        @endif

        <!-- TAT Expire -->
        @if(!empty($formattedTatExpire))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">TAT Expire</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    {{ $formattedTatExpire }}</td>
            </tr>
        @endif

        <!-- Assigned To -->
        @if(!empty($engineer_name))

            @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp

            <tr style="background:{{ $bg }};">
                <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Assigned To</td>
                <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                    <table cellpadding="0" cellspacing="0" border="0" style="display:inline-table;">
                        <tr>
                            <td style="padding-right:6px; vertical-align:middle;">
                                <span
                                    style="display:inline-block; width:22px; height:22px; border-radius:50%; background:#e8eef5; text-align:center; line-height:22px; vertical-align:middle;">
                                    <svg viewBox="0 0 24 24" width="12" height="12"
                                        style="fill:#6b7c93; vertical-align:middle;">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
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

    <!-- CTA Button (only if user exists and ticket not created via email) -->
    @if($user && !$ticket->isCreatedViaEmail())
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center" style="padding-bottom:32px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center" bgcolor="{{ $themeColor }}"
                                style="border-radius:6px; padding:12px 28px; background:{{ $themeColor }};">
                                <a href="{{ url('ticket/' . $ticket->id) }}"
                                    style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                    View Ticket and Take an Action <span style="font-size:16px;">→</span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif

         
@endsection