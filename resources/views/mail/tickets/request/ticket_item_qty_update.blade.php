{{-- @page-meta
{
    "page_no": "TIQU05-26",
    "file": "ticket_item_qty_update.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Custom Form Data quantity update Mail"
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

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 20px 0;">This is an update that the ticket id <strong><a href="{{ Config::get("app.url") }}ticket/{{ $ticketObj->id }}" target="_blank" style="color:{{ $themeColor }}; text-decoration:underline;">{{ $ticketObj->ticket_tag ?? "#". $ticketObj->id }}</a></strong></p>

{{-- Custom Form Data --}}
@if(!empty($customFormData) && isset($customFormData->field_values))
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:20px 0 8px 0; font-weight:bold;">Service Request Form Data</p>

@php
    $custom_form = json_decode($customFormData->field_values, true);
@endphp

@if (!empty($custom_form))
<table role="presentation" class="table" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; margin-top:5px;">
    <tr>
        <th width="35%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Name</th>
        <th width="65%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Value</th>
    </tr>
    @php $customIndex = 0; @endphp
    @foreach ($custom_form as $key => $value)
        @if (!is_array($value) && !in_array($key, ['department_id', 'location_id', 'b_location', 'internal_location_id','user_id','custom_form_id']))
        @php $bg = ($customIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
        <tr>
            <th width="35%" align="left" bgcolor="{{ $bg }}" style="{{ $thBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">
                @if($key == 'wbs_center')
                    WBS Center
                @elseif($key == 'wbs_note')
                    WBS Note
                @else
                    {{ ucwords(str_replace('_', ' ', $key)) }}
                @endif
            </th>
            <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">
                @if (!is_null($value) && !is_array($value))
                    {{ $value }}
                @else
                    <span>N/A</span>
                @endif
            </td>
        </tr>
        @endif
    @endforeach
</table>
@else
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:5px 0 10px 0;">No data available.</p>
@endif

{{-- Form Items --}}
@if (isset($custom_form['category']))
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:15px 0 8px 0; font-weight:bold;">Form Item(s)</p>

<table role="presentation" class="table" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse; margin-top:5px;">
    <tr>
        <th width="10%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">#</th>
        <th width="25%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Category</th>
        <th width="25%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Sub Category</th>
        <th width="15%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Quantity</th>
        <th width="25%" align="left" bgcolor="{{ $themeColor }}" style="{{ $thBase }} background-color: {{ $themeColor }}; color:#ffffff; border:1px solid #dddddd;">Remarks</th>
    </tr>
    @php $itemIndex = 0; @endphp
    @foreach ($custom_form['category'] as $index => $val)
    @php $bg = ($itemIndex++ % 2 == 0) ? '#f4f4f4' : '#ffffff'; @endphp
    <tr>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $index + 1 }}</td>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $custom_form['category'][$index] ?? 'N/A' }}</td>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $custom_form['item'][$index] ?? 'N/A' }}</td>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $custom_form['quantity'][$index] ?? 'N/A' }}</td>
        <td align="left" bgcolor="{{ $bg }}" style="{{ $tdBase }} background-color: {{ $bg }}; border:1px solid #dddddd;">{{ $custom_form['remarks'][$index] ?? 'N/A' }}</td>
    </tr>
    @endforeach
</table>
@endif
@endif

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