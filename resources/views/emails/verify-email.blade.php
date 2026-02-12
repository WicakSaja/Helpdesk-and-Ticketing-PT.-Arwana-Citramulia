<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - Verifikasi Email</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 20px 60px rgba(214, 40, 40, 0.15); overflow: hidden; max-width: 600px;">

                    {{-- Header with Arwana Red Gradient + Logo --}}
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #d62828 0%, #ff6b6b 100%); padding: 40px; text-align: center;">
                            @if (isset($message))
                                <img src="{{ $message->embed(public_path('images/logo_arwana.png')) }}"
                                    alt="Arwana Logo"
                                    style="width: 80px; height: auto; margin-bottom: 16px; border-radius: 12px;">
                            @elseif (isset($logoUrl))
                                <img src="{{ $logoUrl }}"
                                    alt="Arwana Logo"
                                    style="width: 80px; height: auto; margin-bottom: 16px; border-radius: 12px;">
                            @endif
                            <h1
                                style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                Ticketing Helpdesk Arwana
                            </h1>
                            <p
                                style="margin: 8px 0 0 0; color: rgba(255, 255, 255, 0.85); font-size: 13px; font-weight: 400; letter-spacing: 0.3px;">
                                PT. Arwana Citramulia Tbk.
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">
                            {{-- Greeting --}}
                            <h2 style="margin: 0 0 8px 0; font-size: 22px; font-weight: 600; color: #1f2937;">
                                Halo!
                            </h2>

                            @if (!empty($userName))
                                <p style="margin: 0 0 24px 0; font-size: 15px; color: #6b7280;">
                                    {{ $userName }},
                                </p>
                            @endif

                            {{-- Intro Text --}}
                            <p style="margin: 0 0 32px 0; font-size: 15px; line-height: 1.7; color: #4b5563;">
                                Terima kasih telah mendaftar di <strong>{{ $appName }}</strong>. Silakan klik
                                tombol di bawah untuk memverifikasi alamat email Anda dan mengaktifkan akun Anda.
                            </p>

                            {{-- CTA Button with Arwana Red --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 0 0 32px 0;">
                                        <a href="{{ $buttonUrl }}" target="_blank"
                                            style="display: inline-block; background: linear-gradient(135deg, #d62828 0%, #ff6b6b 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; padding: 14px 48px; border-radius: 10px; letter-spacing: 0.3px; box-shadow: 0 10px 25px rgba(214, 40, 40, 0.3); transition: all 0.3s ease;">
                                            ✓ Verifikasi Email
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Info Boxes --}}
                            <div
                                style="background-color: #fff5f5; border: 1px solid #ffcdd2; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                <div
                                    style="margin-bottom: 12px; font-size: 14px; color: #475569; display: flex; gap: 12px;">
                                    <span style="color: #d62828; font-size: 16px;">🔒</span>
                                    <span> Verifikasi email meningkatkan keamanan akun Anda</span>
                                </div>
                                <div
                                    style="margin-bottom: 12px; font-size: 14px; color: #475569; display: flex; gap: 12px;">
                                    <span style="color: #d62828; font-size: 16px;">⏱️</span>
                                    <span> Link verifikasi berlaku selama {{ $expirationMinutes }} menit</span>
                                </div>
                                <div style="font-size: 14px; color: #475569; display: flex; gap: 12px;">
                                    <span style="color: #d62828; font-size: 16px;">✓</span>
                                    <span> Setelah verifikasi, Anda dapat langsung menggunakan semua fitur</span>
                                </div>
                            </div>

                            {{-- Outro Text --}}
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #6b7280;">
                                Jika Anda tidak membuat akun atau menerima email ini secara tidak terduga, silakan
                                abaikan email ini. Akun Anda tidak akan diaktifkan sampai link verifikasi diklik.
                            </p>

                            {{-- Fallback URL --}}
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
                                <p style="margin: 0 0 8px 0; font-size: 12px; color: #9ca3af; font-weight: 500;">
                                    Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
                                </p>
                                <p
                                    style="margin: 0; font-size: 11px; word-break: break-all; background-color: #f9fafb; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <a href="{{ $buttonUrl }}" style="color: #d62828; text-decoration: underline;">
                                        {{ $buttonUrl }}
                                    </a>
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="background-color: #f9fafb; padding: 24px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px 0; font-size: 12px; color: #9ca3af;">
                                &copy; {{ date('Y') }} <strong>{{ $appName }}</strong>. All rights reserved.
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #d1d5db;">
                                PT. Arwana Citramulia | Sistem Ticketing Terpadu
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
