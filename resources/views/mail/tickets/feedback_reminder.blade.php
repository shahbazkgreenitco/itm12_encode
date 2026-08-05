{{-- @page-meta
{
    "page_no": "STFR-26",
    "file": "feedback_reminder.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-06",
        "reviewer": null,
        "description": "Ticket Feedback Reminder Mail"
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

    // Reusable inline styles
    $tdBase = "padding:10px; font-size:14px; text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; color:#333333; vertical-align:top;";
@endphp

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">@if($creator_user) Hello {{ $creator_user->getGuranteedNameText(true) }}, @else Hello, @endif</p>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">We noticed you haven't provided feedback for your Ticket ID <strong>{{ $ticket->ticket_tag ?? $ticket->id }}</strong></p>

@if($creator_user)
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 15px 0;">You can provide your feedback using the link below:</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0 0 15px 0;">
    <tr>
        <td align="left" style="padding:5px 0;">
            <a href="{{ url('ticket/feedback/') }}/{{ base64_encode('###'.$ticket->id) }}" style="display:inline-block; background:{{ $themeColor }}; color:#ffffff; padding:12px 25px; text-decoration:none; border-radius:5px; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold;">Provide Feedback</a>
        </td>
    </tr>
</table>

<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:0 0 10px 0;">Or click the image below:</p>

<p style="text-align:left; margin:0 0 20px 0;">
    <a href="{{ url('ticket/feedback/') }}/{{ base64_encode('###'.$ticket->id) }}" style="text-decoration:none;">
        <img src="{{ asset('images/feedback.png') }}" alt="Provide Feedback" style="border:0; max-width:100%; height:auto;" />
    </a>
</p>
@endif

@if($site_name)
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:20px 0 0 0;">
Thank you,<br>
@if($email_thankuby ?? false)
{{ $email_thankuby }}<br>
@endif
{{ $site_name }}
</p>
@else
<p style="text-align:left; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; margin:20px 0 0 0;">Thank you</p>
@endif
@endsection