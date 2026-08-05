{{-- @page-meta
{
    "page_no": "RA05-27",
    "file": "request_approved.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Request Approved Mail"
        },
        {
        "version": "1.1",
        "writer": "Priya Maru",
        "from": "2026-07",
        "reviewer": null,
        "description": "Outlook-compatible rewrite (inline styles, cell-level backgrounds, width attributes)"
        },
        {
        "version": "1.2",
        "writer": "shivam kumar",
        "from": "2026-07",
        "reviewer": null,
        "description": "Theme alignment - updated to match modern layout with greeting icon, approval status, and proper styling"
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
                                <img src="{{ $user->getProfileImg() }}" alt="{{ $user->getGuranteedNameText(true) }}" 
                                     style="width:28px; height:28px; border-radius:50%; display:block; object-fit:cover; border:none;"/>
                            @else
                                <svg viewBox="0 0 24 24" width="16" height="16" style="fill:#6b7c93; vertical-align:middle;">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            @endif
                        </span>
                    </td>
                    <td style="font-size:15px; color:#333; font-weight:400; vertical-align:middle; font-family:'Inter',Arial,sans-serif;">
                        Hello,
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Header - Request Approved -->
<h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
    Service Request<br>
    <span style="color:{{ $themeColor }};">Approved!</span>
</h1>
<p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
    Service Request ID: <b>{{ $pro_request->procure_tag ?? $pro_request->ticket_id }}</b>
</p>
<p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
    ✅ Your request has been approved by our team. Our team is working on it.
</p>

<!-- Details Table -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    
    @if(!empty($pro_request->procure_tag) || !empty($pro_request->ticket_id))
    <!-- Service Request ID -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td width="35%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Service Request ID</td>
        <td width="65%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <strong style="color:{{ $themeColor }};">{{ $pro_request->procure_tag ?? $pro_request->ticket_id }}</strong>
        </td>
    </tr>
    @endif

    @if(!empty($pro_request->problemCategory))
    <!-- Problem Category -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Problem Category</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#7b2d8e; font-weight:550; background:#f8e6ff; border-radius:23px; padding:5px 10px; display:inline-block;">{{ $pro_request->problemCategory->name }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($pro_request->subCategory))
    <!-- Sub Category -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Sub Category</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $pro_request->subCategory->name }}</td>
    </tr>
    @endif

    @if(!empty($pab) && !empty($pab->name))
    <!-- Authority Board -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Authority Board</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#e30613; font-weight:600;">{{ $pab->name }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($pro_request->content))
    @php
        $embedded_attachments = App\Models\Ticket\Attachment::getEmbeddedAttachments($pro_request->id);
        $processedContent = CommonHelper::renderTktContent($pro_request->content, $embedded_attachments);
        $cleanContent = trim(strip_tags($processedContent));
        $limitedContent = Illuminate\Support\Str::limit($cleanContent, 500, '...');
    @endphp
    <!-- Description -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Description</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <div style="word-break: break-word; white-space: normal; padding:8px 12px; background:#f8f9fa; border-radius:6px; border-left:3px solid {{ $themeColor }};">
                {{ $limitedContent }}
            </div>
        </td>
    </tr>
    @endif

    @if(!empty($pro_request->subject))
    <!-- Subject -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Subject</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $pro_request->subject }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->tat))
    <!-- TAT -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">TAT</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $pro_request->tat }} Hrs</td>
    </tr>
    @endif
</table>

<!-- View Ticket Button -->
<p style="font-size:14px; color:#333; margin:0 0 12px 0; font-family:'Inter',Arial,sans-serif;">
    You can access the service ticket using the link below.
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="left" style="padding-bottom:28px;">
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                        <a href="{{ Config::get('app.url') }}/ticket/{{ $pro_request->ticket_id }}" 
                           style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                            View Ticket →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection