@php
    $settings = CommonHelper::settings();

    if ($settings->css_theme == 2) {
        $themeColor = '#08558b';
    } else {
        $themeColor = '#CD3333';
    }
@endphp

<td style="padding:0;background:#ffffff;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
        <tr>
            <td style="padding: 10px 15px 0px 15px;" valign="middle">

                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="left" valign="middle" width="120">
                            @if($settings->logo_thumbnail)
                                <img
                                    src="{{ asset('uploads/settings/'.$settings->logo_thumbnail) }}"
                                    width="90"
                                    style="display:block;border:0;outline:none;text-decoration:none;"
                                    alt="{{ config('app.name') }}">
                            @elseif($settings->site_name)
                                <span style="font-family:'Inter',Arial,sans-serif;font-size:18px;font-weight:bold;color:#222;">
                                    {{ $settings->site_name }}
                                </span>
                            @else
                                <span style="font-family:'Inter',Arial,sans-serif;font-size:18px;color:#222;">
                                    {{ config('app.name') }}
                                </span>
                            @endif
                        </td>
                        <td align="right" valign="middle">
                            <span style="font-family:'Inter',Arial,sans-serif;font-size:22px;font-weight:700;color:#222;">
                                {{ config('app.name') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:0;">
                <img
                    src="{{ asset('imgs/header.png') }}"
                    width="640"
                    style="display:block;width:100%;max-width:640px;border:0;height:35px;outline:none;text-decoration:none;"
                    alt="">
            </td>
        </tr>
    </table>
</td>