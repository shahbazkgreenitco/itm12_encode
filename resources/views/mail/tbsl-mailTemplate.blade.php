{{--
/**
* ------------------------------------------------------------
* File: tbsl-mailTemplate.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-35
* Created On: 2026-04-06
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
        .mailtemplate a:hover {text-decoration: underline;}
        .mailtemplate p {font-size: 14px; margin-bottom:10px;}
        .footer-logo img {
            width: 200px;
        }
        .footer-copyright p {
            font-size: 12px;
        }
        .footer-copyright .copyright {
            text-align: left;
            float: left;
        }
        .footer-copyright p.copyright a {
            color: #e81212;
            font-size: 13px;
        }
        .mailtemplate .btn {
            padding: 15px;
            margin-right:20px;
            background: green;
            color: #fff !important;
            border-radius: 5px;
        }
        .mailtemplate .btn-declined {
            background: red;
        }
        .playstore-icon {
            width: 95px !important;
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
                                    <td id="header_wrapper" style="padding: 10px 30px; display: block; text-align:center;">
                                        @if(CommonHelper::settings()->logo_thumbnail)
                                            <img src="{{ asset('uploads') . '/'. CommonHelper::settings()->logo_thumbnail}}" style="width: 150px;"/>
                                        @elseif(CommonHelper::settings()->site_name)
                                            <h1 style="color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 #7797b4;">{{ CommonHelper::settings()->site_name }}</h1>
                                        @else
                                            <h1 style="color: #ffffff; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 #7797b4;">{{ config('app.name') }}</h1>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;padding:0px;">
                                        <h3 style="margin: 15px 0px; color:rgb(112,112,112);">TATA STEEL COLORS PVT LTD</h3>
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
                                @if(CommonHelper::settings()->logo_thumbnail)
                                <tr>
                                    <td valign="top">
                                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                            <tr>
                                                <td colspan="2" valign="middle" id="credit" style="padding: 25px 20px 0px; border: 0; border-top: 1px solid #dcdcdc; color: #ffffff; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;">
                                                    <div class="footer-logo" style="color: #fff;">
                                                        <img src="{{ asset('uploads') . '/'. CommonHelper::settings()->logo_thumbnail}}" style="width: 100px; margin-bottom:0px;">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td valign="top">
                                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                            <tr>
                                                <td colspan="2" valign="middle" id="credit" style="padding: 10px 20px; border-top: 4px solid #e81212; color: #ffffff; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;">
                                                    <div class="footer-copyright" style="color: #fff;">
                                                        {{--@if(CommonHelper::settings()->default_eula_text)
                                                            <p style="text-align: center; color: #737373;">{!! CommonHelper::settings()->default_eula_text !!}</p>
                                                        @endif--}}
                                                        <div>
                                                            <p class="copyright" style="color: #737373;">Copyright © {{ date('Y') }} <a href="https://greenitco.com/" target="_blank">GreenITCo</a> All rights reserved.</p>
                                                            {{--<span style="float: right; margin-top: 0px;">
                                                                <a href="https://play.google.com/store/apps/details?id=com.nextmegabit.itm">
                                                                    <img src="{{ url('images/android_playstore.png') }}" title="Android App" alt="Playstore" class="playstore-icon" width="95px">
                                                                </a>
                                                                <a href="https://apps.apple.com/in/app/it-asset-management/id1397185443">
                                                                    <img src="{{ url('images/app_store.png') }}" title="Appstore" alt="Appstore" class="playstore-icon" width="95px">
                                                                </a>
                                                            </span>--}}
                                                        </div>
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
