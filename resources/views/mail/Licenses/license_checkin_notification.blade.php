@extends('mail.mailTemplate')
@section('content')

<h1 style="margin:0 0 15px;color:#d72638;font-size:30px;line-height:38px;font-weight:bold;">
    Hello {{ $user->first_name }},
</h1>

<p style="margin:0 0 25px;color:#1e293b;font-size:16px;line-height:28px;">
    The license has been successfully checked in from you. Below are the details:
</p>

<table width="100%" cellpadding="0" cellspacing="0">

    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>License Name:</strong>
                        {{ $license->name }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if($license->category)
    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>Category:</strong>
                        {{ $license->category->name }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>Check-In Date:</strong>
                        {{ CommonHelper::getDateAs($logaction->created_at, "d/m/Y", "Y-m-d H:i:s") }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if($logaction->note)
    <tr>
        <td style="padding-bottom:12px;">
            <table width="100%" cellpadding="0" cellspacing="0"
                style="border:1px solid #f3b7be;border-radius:10px;background:#ffffff;">
                <tr>
                    <td style="padding:14px 18px;font-size:15px;color:#1e293b;">
                        <strong>Additional Notes:</strong>
                        {{ $logaction->note }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

</table>

<p style="margin-top:25px;padding:15px;background:#fff5f5;border-left:4px solid #d72638;color:#1e293b;border-radius:6px;">
    This license has been successfully checked in and is now available for future assignments.
</p>

@if($site_name)
<p style="margin-top:30px;color:#1e293b;font-size:15px;line-height:24px;">
    Thanks,<br>
    <strong>{{ $site_name }}</strong>
</p>
@endif

@endsection