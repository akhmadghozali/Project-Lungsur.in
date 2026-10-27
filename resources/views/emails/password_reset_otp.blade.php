<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Kata Sandi - Lungsur.in</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 540px; background-color: #FFFFFF; border-radius: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #E2E8F0; overflow: hidden; text-align: left;" cellspacing="0" cellpadding="0">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 36px 36px 20px; background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); text-align: center;">
                            <div style="display: inline-block; background-color: #FFFFFF; padding: 8px 18px; border-radius: 16px; margin-bottom: 16px;">
                                <span style="font-size: 20px; font-weight: 900; color: #0F172A; letter-spacing: -0.5px;">
                                    Lungsur<span style="color: #2563EB;">.in</span>
                                </span>
                            </div>
                            <h1 style="margin: 0; color: #FFFFFF; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">
                                Permintaan Reset Kata Sandi
                            </h1>
                            <p style="margin: 8px 0 0; color: #DBEAFE; font-size: 13px; font-weight: 500;">
                                Akun Mahasiswa Lungsur.in
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 36px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #334155;">
                                Halo, <strong>{{ $name }}</strong>!
                            </p>
                            <p style="margin: 0 0 24px; font-size: 14px; line-height: 1.6; color: #475569;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun Lungsur.in Anda. Gunakan kode OTP 6-digit berikut untuk melanjutkan proses reset password:
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color: #EFF6FF; border: 2px dashed #3B82F6; border-radius: 16px; padding: 22px; text-align: center; margin: 24px 0;">
                                <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #2563EB; letter-spacing: 1px; margin-bottom: 6px;">
                                    Kode OTP Reset Sandi
                                </div>
                                <div style="font-size: 38px; font-weight: 900; letter-spacing: 12px; color: #1E3A8A; font-family: 'Courier New', Courier, monospace; margin-left: 12px;">
                                    {{ $otpCode }}
                                </div>
                                <div style="font-size: 12px; color: #64748B; font-weight: 600; margin-top: 6px;">
                                    Berlaku selama 15 menit
                                </div>
                            </div>

                            <!-- Warning Notice -->
                            <div style="background-color: #FFFBEB; border-left: 4px solid #F59E0B; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #92400E;">
                                    <strong>Peringatan Keamanan:</strong> Jika Anda tidak pernah merasa meminta pengaturan ulang kata sandi, abaikan email ini. Akun Anda tetap aman dan kata sandi Anda tidak akan berubah.
                                </p>
                            </div>

                            <p style="margin: 0; font-size: 13px; color: #64748B; line-height: 1.6;">
                                Salam hangat,<br>
                                <strong>Tim Pengembang Lungsur.in</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 36px; background-color: #F1F5F9; border-top: 1px solid #E2E8F0; text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #94A3B8; line-height: 1.5;">
                                Ini adalah pesan otomatis dari sistem Lungsur.in Politeknik Negeri Malang &bull; Mohon tidak membalas email ini secara langsung.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
