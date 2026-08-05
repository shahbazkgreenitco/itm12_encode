{{-- @page-meta
{
    "page_no": "UC05-26",
    "file": "user_comment.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "User Comment Mail"
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

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">Hello,</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">This is an update that the service request <strong>#{{ $pro_request->procure_tag ?? $pro_request->ticket_id }}</strong> has been commented.</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">Here is the comment:</p>

<div style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 20px 0;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 20px 0;">You can access the update of service request on below link</p>

<table role="presentation" class="table" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; margin-top:10px;">
    @if(!empty($pro_request->procure_tag))
    <tr>
        <th width="35%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Service Request ID</th>
        <td align="left" bgcolor="{{ $themeColor }}" style="{{ $tdBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;"><strong>{{ $pro_request->procure_tag }}</strong></td>
    </tr>
    @endif

    @if(!empty($pro_request->problemCategory))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Problem Category</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pro_request->problemCategory->name }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->subCategory))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Sub Category</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pro_request->subCategory->name }}</td>
    </tr>
    @endif

    @if(!empty($pab) && !empty($pab->name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Authority Board</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pab->name }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->content))
    @php
        $embedded_attachments = App\Models\Ticket\Attachment::getEmbeddedAttachments($pro_request->id);
        $processedContent = CommonHelper::renderTktContent($pro_request->content, $embedded_attachments);
        $cleanContent = trim(strip_tags($processedContent));
        $limitedContent = Illuminate\Support\Str::limit($cleanContent, 500, '...');
    @endphp
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Description</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $limitedContent }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->subject))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Subject</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pro_request->subject }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->tat))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">TAT</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pro_request->tat }} Hrs</td>
    </tr>
    @endif
</table>

<p style="text-align:left; margin:20px 0 20px 0;"><b><a href="{{ Config::get("app.url") }}/ tickets/requestInfo/{{ $pro_request->id }}" style="color:{{ $themeColor }}; text-decoration:underline; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px;">Click Here</a></b></p>

@if($site_name)
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0;">
Thank you,<br/>
@if($email_thankuby ?? false)
{{ $email_thankuby }}<br/>
@endif
{{ $site_name }}
</p>
@else
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0;">Thank you</p>
@endif

@if($add_back_trail && count($back_trails))
@foreach($back_trails as $bt)
<hr style="border: none; height: 1px; background-color: #e8e8e8; margin: 10px 0;">
<p style="font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:12px; color:#666666; text-align:left; margin:0 0 5px 0;">{!! CommonHelper::renderTktContent($bt->msg) !!}</p>
<p style="font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:13px; color:#333333; text-align:left; margin:0 0 10px 0;">{!! CommonHelper::renderTktContent($bt->remarks, $embedded_attachments, true) !!}</p>
@endforeach
@endif
@endsection