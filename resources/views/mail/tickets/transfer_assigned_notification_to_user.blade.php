{{-- @page-meta
{
    "page_no": "STTAU-26",
    "file": "transfer_assigned_notification_to_user.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-06",
        "reviewer": null,
        "description": "Ticket transfer notification to user Mail"
        },
        {
        "version": "1.1",
        "writer": "Priya Maru",
        "from": "2026-07",
        "reviewer": null,
        "description": "Outlook-compatible rewrite (inline styles, cell-level backgrounds, width attributes)"
        }
    ]
} 
--}}
@extends('mail.newMailTemplate')
@section('content')
@php
    // Theme colour (Outlook ignores <style> blocks in body, so it must be inline)
    $themeColor = CommonHelper::settings()->css_theme == 2 ? '#08558b' : '#CD3333';

    // Reusable inline styles — Word engine (Outlook desktop) needs padding,
    // font and background on every cell, not on <tr> or in <style>.
    $thBase = "padding:10px; font-size:14px; text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; vertical-align:top;";
    $tdBase = "padding:10px; font-size:14px; text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; color:#333333; vertical-align:top;";

    $rowIndex = 0;
@endphp

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">
    @if($ticket->creator_name) Hello {{ ucfirst($ticket->creator_name) }}, @else Hello, @endif 
</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">
    This is to inform you that ticket <strong>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</strong>, has been assigned/transferred to {{ $engineer_name }}. {{ $engineer_email }} will now work on this ticket for the resolution.
</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">
    Please let us know if you have any more question. You can update / provide additional input by replying to this mail.
</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">We are happy to help.</p>
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 20px 0;">We will keep you posted on the progress.</p>

<table role="presentation" class="table" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; margin-top:20px;">
    @if(!empty($ticket->id))
    <tr>
        <th width="35%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Ticket ID</th>
        <td align="left" bgcolor="{{ $themeColor }}" style="{{ $tdBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">{{ $ticket->ticket_tag ?? '###'. $ticket->id."###" }}</td>
    </tr>
    @endif

    @if(!empty($ticket->decodedSubject()))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Subject</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $ticket->decodedSubject() }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->department)->name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Department</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ optional($ticket->department)->name }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->problemCategory)->name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Category</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ optional($ticket->problemCategory)->name }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->subCategory)->name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Sub Category</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ optional($ticket->subCategory)->name }}</td>
    </tr>
    @endif

    @php
        $embedded_attachments = App\Models\Ticket\Attachment::getEmbeddedAttachments($ticket->id);
        $processedContent = CommonHelper::renderTktContent($ticket->content, $embedded_attachments);
        $processedContent = str_replace('&nbsp;', ' ', $processedContent);
        $cleanContent = trim(strip_tags($processedContent));
        $limitedContent = Illuminate\Support\Str::limit($cleanContent, 500, '...');
    @endphp

    @if(!empty($limitedContent))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Content</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{!! $limitedContent !!}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->status)->name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Status</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ optional($ticket->status)->name }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->priority)->name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Priority</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ optional($ticket->priority)->name }}</td>
    </tr>
    @endif

    @if(!empty($ticket->tat))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">TAT</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $ticket->tat }} Hrs</td>
    </tr>
    @endif

    @if(!empty($formattedTatExpire))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">TAT Expire</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $formattedTatExpire }}</td>
    </tr>
    @endif

    @if(!empty($engineer_name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Assigned To</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $engineer_name }}</td>
    </tr>
    @endif
</table>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:20px 0 10px 0;">You can access further updates on this ticket using the following link.</p>
<p style="text-align:left; margin:0 0 20px 0;"><b><a href="{{ Config::get("app.url") }}/ticket/{{ $ticket->id }}" style="color:{{ $themeColor }}; text-decoration:underline; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px;">Click Here</a></b></p>

@if($site_name)
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0;">Thanks,<br/>{{ $site_name }}</p>
@else
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0;">Thanks</p>
@endif
@endsection