{{--
/**
* ------------------------------------------------------------
* File: ltts-mailTemplate.blade.php
* Module: Device
* DEV/26/07
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #DEV-019
* Created On: 2026-14-07
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name') }}</title>
    <style type="text/css">
        .mailtemplate {color: #707070;font-family: arial;font-size: 14px;margin: 0 auto;padding: 0;vertical-align: middle;line-height: 150%;}
        .mailtemplate a {text-decoration: none;}
        #wrapper a:hover {text-decoration: underline;}
        .mailtemplate p {font-size: 14px; margin-bottom:10px;}
        .footer-logo img {
            width: 200px;
        }
        .footer-copyright p {
            font-size: 10px;
        }
        .footer-copyright .copyright {
            text-align: center;
            /*float: left;*/
        }
        .footer-copyright p.copyright a {
            color: #e81212;
            font-size: 13px;
        }
        p.left {
            float: left;
            color: white;
            font-size: 13px;
        }
        .left {
            float: left;
            color: white;
            font-size: 13px;
        }
        p.right {
            float: right;
            color: white;
            font-size: 13px;
        }
        .mailtemplate .btn {
            padding: 10px;
            margin-right:20px;
            background: green;
            color: #fff !important;
            border-radius: 5px;
        }
        .color-code-bar.color-code-yellow {
            color: #ffa726 !important;
        }
        .color-code-bar.color-code-blue {
            color: #42518C !important;
        }
        .color-code-bar.color-code-rose {
            color: #f275ad !important;
        }
        .color-code .color-code-bar {
            display: inline-block;
            width: 20px;
            height: 10px;
            background: #ccc;
            border-radius: 2px;
        }
        .color-code .rectengular {
            display: inline-block;
            width: 20px;
            height: 10px;
            background: #ccc;
            border-radius: 2px;
        }
        .color-code-rose.rectangle {
            height: 10px;
            width: 20px;
            background-color: #f275ad !important;
        }
        .color-code-blue.rectangle {
            height: 10px;
            width: 20px;
            background-color: #42518C !important;
        }
        .color-code-yellow.rectangle {
            height: 10px;
            width: 20px;
            background-color: #ffa726 !important;
        }
        #body_content_inner ul li {
            list-style-type: none;
        }
    </style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" >
<div id="wrapper" dir="ltr" class="mailtemplate" style="background-color: #f5f5f5; margin: 0; padding: 70px 0 70px 0; width: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top">
                <table border="0" cellpadding="0" cellspacing="0" width="700" id="template_container" style="background-color: #fdfdfd;border: 1px solid #dcdcdc;box-shadow: 0 1px 4px rgba(0,0,0,0.1);border-radius: 3px;">
                    <tr>
                        <td align="center" valign="top">
                            <!-- Header -->
                            <table border="0" cellpadding="0" cellspacing="0" width="700" id="template_header" style="background-color: #fff; color: #000; border-bottom: 1px solid #dcdcdc; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border-radius: 3px 3px 0 0;">
                                <tr>
                                    <td id="header_wrapper" style="padding: 36px 48px; display: block; text-align:center;">
                                        @if(CommonHelper::settings()->logo_thumbnail)
                                            @if(config('app.client') == "ltts")
                                            <img src="{{ asset("nd/img/mail-template.png") }}" class="greenitco" />
                                            @else
                                            <img src="{{ asset('uploads') . '/'. CommonHelper::settings()->logo_thumbnail}}"/>
                                            @endif
                                        @elseif(CommonHelper::settings()->site_name)
                                            <h1 style="color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 #7797b4;">{{ CommonHelper::settings()->site_name }}</h1>
                                        @else
                                            <h1 style="color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 #7797b4;">{{ config('app.name') }}</h1>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <!-- End Header -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Body -->
                            <table border="0" cellpadding="0" cellspacing="0" width="700" id="template_body">
                                <tr>
                                    <td valign="top" id="body_content">
                                        <!-- Content -->
                                        <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top" style="padding: 48px 20px;">
                                                    <div id="body_content_inner" style="color: #737373; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;">
                                                        @yield('content')
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- End Content -->
                                    </td>
                                </tr>
                            </table>
                            <!-- End Body -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Footer -->
                            <table border="0" cellpadding="10" cellspacing="0" width="700" id="template_footer">
                                <tr>
                                    <td valign="top">
                                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                            <tr>
                                                <td colspan="2" valign="middle" id="credit" style="padding: 25px 20px 5px; border: 0; color: #ffffff; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;">
                                                    @if(config('app.client') == "ltts")
                                                        <img src="{{ asset("nd/img/mail-footer-template.jpg") }}" class="greenitco" />
                                                    @else
                                                        <div class="footer-logo" style="color: #fff;">
                                                            <img src="{{ url('images/itm_logo.png') }}"style="width: 200px; margin-bottom:20px;">
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">
                                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                            <tr>
                                                <td colspan="2" valign="middle" id="credit" style="padding: 10px 20px;  color: #ffffff; font-family: Arial; font-size: 10px; line-height: 125%; text-align: center;">
                                                    {{--<div class="footer-copyright" style="color: #fff;">
                                                        @if(CommonHelper::settings()->default_eula_text)
                                                            <p style="text-align: center;">{!! CommonHelper::settings()->default_eula_text !!}</p>
                                                        @endif
                                                        <div>
                                                            <p class="copyright">Copyright © 2022 GreenITCo All rights reserved.</p>
                                                            <span style="float: right;">
                                                                <a href="https://play.google.com/store/apps/details?id=com.nextmegabit.itm">
                                                                    <img src="{{ url('images/android_playstore.png') }}" title="Android App" alt="Playstore" style="width: 95px;">
                                                                </a>
                                                                <a href="https://apps.apple.com/in/app/it-asset-management/id1397185443">
                                                                    <img src="{{ url('images/app_store.png') }}" title="Appstore" alt="Appstore" style="width: 95px;">
                                                                </a>
                                                            </span>
                                                        </div>
                                                    </div>--}}

                                                    <div class="footer-copyright" style="color: #fff;">
                                                        <table>
                                                            <tr>
                                                                <td colspan="2">
                                                                    {{--@if(CommonHelper::settings()->default_eula_text)
                                                                        <p style="text-align: center;">{!! CommonHelper::settings()->default_eula_text !!}</p>
                                                                    @endif--}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p class="copyright">Copyright © {{ date('Y') }} GreenITCo All rights reserved.</p>
                                                                </td>
                                                                {{--<td style="text-align: right;">
                                                                    <a href="https://play.google.com/store/apps/details?id=com.nextmegabit.itm">
                                                                        <img src= "{{ url('images/android_playstore.png') }}" title="Android App" alt="Playstore" width="100" height="40"  >&nbsp;
                                                                    </a>
                                                                    <a href="https://apps.apple.com/in/app/it-asset-management/id1397185443">
                                                                        <img src="{{ url('images/app_store.png') }}" title="Appstore" alt="Appstore" width="100" height="40">
                                                                    </a>
                                                                </td>--}}
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- End Footer -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>