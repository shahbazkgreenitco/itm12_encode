@extends('mail.mailTemplate')
@section('content')

<p style="margin:0 0 15px 0; font-size:14px; color:#333;">
    Hello {{ $user->getGuranteedNameText() ?? 'User' }},
</p>

<p style="margin:0 0 20px 0; font-size:14px; color:#333;">
    A License has been checked out under your name. Below are the details:
</p>

<!-- LICENSE DETAILS TABLE -->
<table width="100%" cellpadding="0" cellspacing="0" border="0"
    style="border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0;width:100%;">
    <tr>
        <td style="padding:0;">
            <table width="100%" cellpadding="8" cellspacing="0" border="1"
                style="border-collapse:collapse;border:1px solid #e5e7eb;background:#ffffff;width:100%;">
                <tr>
                    <td style="padding:12px;font-weight:bold;border:1px solid #e5e7eb;background:#f9fafb;width:35%;">
                        License Name:
                    </td>
                    <td style="padding:12px;border:1px solid #e5e7eb;width:65%;">
                        {{ $license->name ?? 'N/A' }}
                    </td>
                </tr>

                @if(isset($license->category) && isset($license->category->name))
                <tr>
                    <td style="padding:12px;font-weight:bold;border:1px solid #e5e7eb;background:#f9fafb;">
                        Category:
                    </td>
                    <td style="padding:12px;border:1px solid #e5e7eb;">
                        {{ $license->category->name }}
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="padding:12px;font-weight:bold;border:1px solid #e5e7eb;background:#f9fafb;">
                        Checkout Date:
                    </td>
                    <td style="padding:12px;border:1px solid #e5e7eb;">
                        @if(isset($logaction->created_at))
                        @if(method_exists($logaction->created_at, 'format'))
                        {{ $logaction->created_at->format('d/m/Y') }}
                        @else
                        {{ $logaction->created_at }}
                        @endif
                        @else
                        N/A
                        @endif
                    </td>
                </tr>

                @if(isset($logaction->note) && !empty($logaction->note))
                <tr>
                    <td style="padding:12px;font-weight:bold;border:1px solid #e5e7eb;background:#f9fafb;">
                        Additional Notes:
                    </td>
                    <td style="padding:12px;border:1px solid #e5e7eb;">
                        {{ $logaction->note }}
                    </td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

<br />

@if(isset($license) && $license->requireAcceptance())
@if(isset($eula) && $eula != "")
<p style="margin:0 0 15px 0; font-size:14px; color:#333;">
    Please read the terms of use below, and click on the link at the bottom to confirm that you read and agree to the
    terms of use, and have received the license.
</p>
@else
<p style="margin:0 0 15px 0; font-size:14px; color:#333;">
    Please click on the link at the bottom to confirm that you have received the license.
</p>
<p style="margin:0 0 15px 0; font-size:14px; color:#333;">
    Please read the terms of use below:
</p>
@endif

@if(isset($logaction->id))
<p style="margin:20px 0;">
    <a href="{{ Config::get('app.url') }}license/accept_checkout/{{ $logaction->id }}"
        style="background:#156FF5;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:5px;display:inline-block;font-weight:bold;">
        I agree to terms of use and have received the license.
    </a>
</p>
@endif
@endif

@if(isset($site_name) && $site_name)
<p style="margin-top:25px;color:#333;font-size:14px;">
    Thank you,<br>
    @if(isset($email_thankuby) && $email_thankuby)
    {{ $email_thankuby }}<br>
    @endif
    {{ $site_name }}
</p>
@endif

@endsection