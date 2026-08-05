{{--
/**
------------------------------------------------------------
File: ticket_mailtemplates.blade.php
Module: Email Templates
MAIL/29/07
------------------------------------------------------------
Version: 1.0.0
Author: Shivam Kumar
Page ID: #MAIL-001
Created On: 2026-07-29
Reviewed By: -
------------------------------------------------------------
Change Log:
[1.0.0] - Initial version of reusable email template
------------------------------------------------------------
*/
--}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Escalation · Knight Frank</title>
</head>

<body style="margin:0; padding:0; background:#f0f2f5; font-family: 'Inter', Arial, Helvetica, sans-serif;">
    <!-- Outlook wrapper table -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="background:#f0f2f5;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <!-- main email container -->
                <table width="640" cellpadding="0" cellspacing="0" border="0" align="center"
                    style="max-width:640px; width:100%; background:#ffffff; border-radius:4px; box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                    @include('mail.mail_partials.header')
                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 36px 28px; text-align:center; background:#ffffff;">
                            @yield('content')
                            <!-- Sign-off -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:left;">
                                <tr>
                                    <td style="padding-bottom:24px;">
                                        <div
                                            style="font-size:15px; font-weight:600; color:#1a1a1a; margin-bottom:2px; font-family:'Inter',Arial,sans-serif;">
                                            @if($site_name) Kind regards, @else Thanks, @endif
                                        </div>
                                        <div
                                            style="font-size:14px; color:#333; margin-bottom:12px; font-family:'Inter',Arial,sans-serif;">
                                            @if($site_name) {{ $site_name }} @else Support Team @endif
                                        </div>
                                        <p
                                            style="font-size:13px; color:#555; line-height:1.5; max-width:520px; margin:0; font-family:'Inter',Arial,sans-serif;">
                                            You have been assigned a support ticket. Kindly check the Service Desk
                                            portal for the details and take necessary action.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer graphic (first image) -->
                    @include('mail.mail_partials.footer')
                </table>
            </td>
        </tr>
    </table>
</body>

</html>