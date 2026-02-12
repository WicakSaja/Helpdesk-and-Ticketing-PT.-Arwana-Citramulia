<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - Ticket Baru</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 20px 60px rgba(214, 40, 40, 0.15); overflow: hidden; max-width: 600px;">

                    {{-- Header with White Background + Logo --}}
                    <tr>
                        <td
                            style="background-color: #ffffff; padding: 40px; text-align: center; border-bottom: 3px solid #d62828;">
                            @if (isset($message))
                                <img src="{{ $message->embed(public_path('images/logo_arwana.png')) }}"
                                    alt="Arwana Logo"
                                    style="width: 80px; height: auto; margin-bottom: 16px; border-radius: 12px; background-color: #ffffff; padding: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            @elseif (isset($logoUrl))
                                <img src="{{ $logoUrl }}" alt="Arwana Logo"
                                    style="width: 80px; height: auto; margin-bottom: 16px; border-radius: 12px; background-color: #ffffff; padding: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            @endif
                            <h1
                                style="margin: 0; color: #d62828; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                Ticketing Helpdesk Arwana
                            </h1>
                            <p
                                style="margin: 8px 0 0 0; color: #b91c1c; font-size: 13px; font-weight: 400; letter-spacing: 0.3px;">
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

                            @if (!empty($helpdeskName))
                                <p style="margin: 0 0 24px 0; font-size: 15px; color: #6b7280;">
                                    {{ $helpdeskName }},
                                </p>
                            @endif

                            {{-- Intro --}}
                            <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.7; color: #4b5563;">
                                Ada ticket baru yang masuk ke sistem dan memerlukan perhatian Anda. Berikut detail
                                ticket:
                            </p>

                            {{-- Ticket Detail Card --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        {{-- Ticket Number & Subject --}}
                                        <div
                                            style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #fecaca;">
                                            <p
                                                style="margin: 0 0 4px 0; font-size: 12px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                                                Nomor Ticket
                                            </p>
                                            <p
                                                style="margin: 0 0 12px 0; font-size: 18px; font-weight: 700; color: #d62828; letter-spacing: 0.3px;">
                                                {{ $ticket->ticket_number }}
                                            </p>
                                            <p style="margin: 0; font-size: 16px; font-weight: 600; color: #1f2937;">
                                                {{ $ticket->subject }}
                                            </p>
                                        </div>

                                        {{-- Detail Rows --}}
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td
                                                    style="padding: 6px 0; font-size: 13px; color: #6b7280; width: 120px; vertical-align: top;">
                                                    📋 Kategori
                                                </td>
                                                <td
                                                    style="padding: 6px 0; font-size: 13px; color: #1f2937; font-weight: 500;">
                                                    {{ $ticket->category->name ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 6px 0; font-size: 13px; color: #6b7280; width: 120px; vertical-align: top;">
                                                    👤 Requester
                                                </td>
                                                <td
                                                    style="padding: 6px 0; font-size: 13px; color: #1f2937; font-weight: 500;">
                                                    {{ $ticket->requester->name ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 6px 0; font-size: 13px; color: #6b7280; width: 120px; vertical-align: top;">
                                                    🕐 Dibuat
                                                </td>
                                                <td
                                                    style="padding: 6px 0; font-size: 13px; color: #1f2937; font-weight: 500;">
                                                    {{ $ticket->created_at->format('d M Y, H:i') }} WIB
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Description --}}
                            <div style="margin-bottom: 28px;">
                                <p
                                    style="margin: 0 0 8px 0; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                                    Deskripsi
                                </p>
                                <div
                                    style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.7; color: #374151;">
                                    {{ \Illuminate\Support\Str::limit($ticket->description, 500) }}
                                </div>
                            </div>

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 0 0 32px 0;">
                                        <a href="{{ $ticketDetailUrl }}" target="_blank"
                                            style="display: inline-block; background: linear-gradient(135deg, #d62828 0%, #ff6b6b 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; padding: 14px 48px; border-radius: 10px; letter-spacing: 0.3px; box-shadow: 0 10px 25px rgba(214, 40, 40, 0.3);">
                                            🎫 Lihat Detail Ticket
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Reminder --}}
                            <div
                                style="background-color: #fff5f5; border: 1px solid #ffcdd2; border-radius: 12px; padding: 16px;">
                                <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">
                                    💡 Segera tindak lanjuti ticket ini dengan melakukan assign ke teknisi yang sesuai.
                                    Ticket yang cepat ditangani meningkatkan kepuasan pengguna.
                                </p>
                            </div>

                            {{-- Fallback URL --}}
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 24px;">
                                <p style="margin: 0 0 8px 0; font-size: 12px; color: #9ca3af; font-weight: 500;">
                                    Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
                                </p>
                                <p
                                    style="margin: 0; font-size: 11px; word-break: break-all; background-color: #f9fafb; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <a href="{{ $ticketDetailUrl }}"
                                        style="color: #d62828; text-decoration: underline;">
                                        {{ $ticketDetailUrl }}
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
                                &copy; {{ date('Y') }} <strong>{{ $appName }}</strong>
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #d1d5db;">
                                PT. Arwana Citramulia Tbk. | ITN Malang Internship Program
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
