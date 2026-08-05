{{-- @page-meta
{
    "page_no": "RD05-26",
    "file": "request_decision.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Revoked Decision Mail"
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

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">Hi,</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">FYI, "{{ $user->username }}" has been revoked his early decision about the approval of the procurement request.</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 20px 0;">You can access the service request using below url</p>

<p style="text-align:left; margin:0 0 20px 0;"><b><a href="{{ Config::get('app.url') }}tickets/requestInfo/{{ $pro_request->id }}" style="color:{{ $themeColor }}; text-decoration:underline; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px;">Click Here</a></b></p>

<table role="presentation" class="table" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; margin-top:10px;">
    @if(!empty($pro_request->procure_tag))
    <tr>
        <th width="35%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Procurement Request</th>
        <td align="left" bgcolor="{{ $themeColor }}" style="{{ $tdBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">{{ $pro_request->procure_tag ?? 'N/A' }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->procure_tag))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Service Request ID</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pro_request->procure_tag ?? 'N/A' }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->department))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Department</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pro_request->department->name }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->creator))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Requester</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ optional($pro_request->creator)->username }}</td>
    </tr>
    @endif

    @if(!empty($pab) && !empty($pab->name))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Authority Board</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $pab->name }}</td>
    </tr>
    @endif

    @if(!empty($pro_request->created_at))
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">Created Date</th>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ CommonHelper::getDateAs($pro_request->created_at, 'd/m/Y', 'Y-m-d H:i:s') }}</td>
    </tr>
    @endif
</table>

@if($site_name)
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:20px 0 0 0;">
Thank you,<br/>
@if($email_thankuby ?? false)
{{ $email_thankuby }}<br/>
@endif
{{ $site_name }}
</p>
@else
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:20px 0 0 0;">Thank you</p>
@endif
@endsection