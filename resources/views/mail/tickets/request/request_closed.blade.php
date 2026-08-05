{{-- @page-meta
{
    "page_no": "RC05-26",
    "file": "request_closed.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Request Closed Mail"
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

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">Hello {{ $user->fullName() }},</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">This is to inform you that your Service Request ID <strong>{{ $request->procure_tag }}</strong> has been automatically closed as it remained pending for more than <strong>25 days</strong>.</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 20px 0;">If you still require, please raise a new service request.</p>

<table role="presentation" class="table" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; margin-top:10px;">
    @if(!empty($request->procure_tag))
    <tr>
        <th width="35%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Service Request ID</th>
        <td align="left" bgcolor="{{ $themeColor }}" style="{{ $tdBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;"><strong>{{ $request->procure_tag }}</strong></td>
    </tr>
    @endif

    @if(!empty($request->problemCategory))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Problem Category</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $request->problemCategory->name }}</td>
    </tr>
    @endif

    @if(!empty($request->subCategory))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Sub Category</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $request->subCategory->name }}</td>
    </tr>
    @endif

    @if(!empty($request->subject))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Subject</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $request->subject }}</td>
    </tr>
    @endif
</table>

<p style="text-align:left; margin:20px 0 20px 0;"><b><a href="{{ Config::get('app.url') }}/tickets/requestInfo/{{ $request->ticket_id }}" style="color:{{ $themeColor }}; text-decoration:underline; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px;">Click Here</a></b></p>

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
@endsection