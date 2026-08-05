{{-- @page-meta
{
    "page_no": "ML05-26",
    "file": "newMailTemplate.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Mail Template Layout"
        },
        {
        "version": "1.1",
        "writer": "santosh upadhyay",
        "from": "2026-07",
        "reviewer": null,
        "description": "Outlook-compatible rewrite (table-based layout, MSO conditionals, inline styles)"
        }
    ]
}
--}}
@php
    $settings = CommonHelper::settings();

    // Theme colour used for tag-table cells (Outlook needs it inline + bgcolor)
    if ($settings->css_theme == 2) {
        $themeColor = '#08558b';
    } else {
        $themeColor = '#CD3333';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="x-apple-disable-message-reformatting" />
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <!--[if !mso]><!-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <!--<![endif]-->
    <title>{{ config('app.name') }}</title>
    <style>
        /* Theme colours for tag tables injected by child templates */
        .mail-content .tag-table th,
        .mail-content .tag-table td {
            background: {{ $themeColor }};
            background-color: {{ $themeColor }};
            color: #ffffff;
        }

        /* Reset — Outlook (Word engine) friendly */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            font-size: 14px;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        .content h2 {
            color: #333333;
        }
        .mail-content .tag-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .mail-content .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .mail-content .table th,
        .mail-content .table td {
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        .mail-content .table tr:nth-child(even) td {
            background-color: #f4f4f4;
        }
        .mail-content .table tr:nth-child(odd) td {
            /* background-color: #ffffff; */
        }
        .mail-content h3 {
            text-align: left;
            font-weight: bold;
        }

        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
            }
            .email-inner {
                padding: 15px !important;
            }
        }
    </style>
    <!--[if mso]>
    <style>
        body, table, td, th, p, h1, h2, h3, a, span {
            font-family: Arial, Helvetica, sans-serif !important;
        }
    </style>
    <![endif]-->
</head>
<body bgcolor="#f4f4f4" style="margin:0; padding:0; background-color:#f4f4f4;">

    <!-- Outer wrapper: full-width table replaces body margin:auto -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f4f4f4" style="background-color:#f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">

                <!--[if mso]>
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" align="center"><tr><td>
                <![endif]-->

                <!-- Main card: fixed 600px for Outlook via ghost table above, max-width for others -->
                <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:10px;">
                    <tr>
                        <td class="email-inner" style="padding: 20px;">

                            <!-- Header / Logo -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 10px;">
                                        @if($settings->logo_thumbnail)
                                            <img src="{{ asset('uploads') . '/settings/' . $settings->logo_thumbnail }}" width="168" alt="{{ $settings->site_name ?? config('app.name') }}" style="display:block; width:168px; max-width:168px; height:auto;" />
                                        @elseif($settings->site_name)
                                            <h1 style="color:#333333; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:30px; font-weight:normal; line-height:150%; margin:0; text-align:center;">{{ $settings->site_name }}</h1>
                                        @else
                                            <h1 style="color:#333333; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:30px; font-weight:normal; line-height:150%; margin:0; text-align:center;">{{ config('app.name') }}</h1>
                                        @endif
                                    </td>
                                </tr>
                                <!-- Divider (replaces <hr>) -->
                                <tr>
                                    <td style="border-top: 1px solid #e8e8e8; font-size:0; line-height:0;" height="1">&nbsp;</td>
                                </tr>
                            </table>

                            <!-- Body content -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="content" align="center" style="padding: 20px 0; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:14px; color:#333333; text-align:center;">
                                        <div class="main-content mail-content">
                                            @yield('content')
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Footer -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" bgcolor="#F1F1F1" style="background-color:#F1F1F1; padding: 10px;">
                                        <img src="{{ asset('main_asset/assets/images/amg-dark.png') }}" width="280" alt="ITM" style="display:block; width:280px; max-width:280px; height:auto;" />
                                    </td>
                                </tr>
                                @if($settings->default_eula_text)
                                    <tr>
                                        <td align="center" style="padding-top: 10px; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:12px; color:#737373; text-align:center;">
                                            {!! $settings->default_eula_text !!}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td align="center" style="padding-top: 10px; font-family:'Plus Jakarta Sans', Arial, Helvetica, sans-serif; font-size:12px; color:#737373; text-align:center;">
                                        Copyright &copy; {{ date('Y') }} <a href="https://greenitco.com/" target="_blank" style="color:#737373; text-decoration:underline;">GreenITCo</a> All rights reserved.
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

                <!--[if mso]>
                </td></tr></table>
                <![endif]-->

            </td>
        </tr>
    </table>

</body>
</html>