<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CampusDomain;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir masuk (Login).
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectAfterAuth(Auth::user());
        }

        $initialMode = old('mode', 'login');
        $campusDomains = CampusDomain::where('is_active', true)->get();

        return view('auth.auth', compact('initialMode', 'campusDomains'));
    }

    /**
     * Tampilkan formulir pendaftaran (Register).
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectAfterAuth(Auth::user());
        }

        $initialMode = old('mode', 'register');
        $campusDomains = CampusDomain::where('is_active', true)->get();

        return view('auth.auth', compact('initialMode', 'campusDomains'));
    }

    /**
     * Proses autentikasi masuk (Login) dengan verifikasi status akun.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email:filter'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
                ], 401);
            }

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
            ]);
        }

        $user = Auth::user();

        // 1. Cek apakah akun diblokir/ditangguhkan
        if ($user->status === 'suspended' || $user->status === 'banned') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $errorMsg = 'Akun Anda ditangguhkan karena pelanggaran ketentuan platform. Silakan hubungi admin.';

            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 403);
            }

            return back()->with('error', $errorMsg);
        }

        // 2. Cek apakah pendaftar email umum masih menunggu verifikasi KTM oleh admin
        if ($user->verification_status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $pendingMsg = 'Akun Anda sedang dalam proses verifikasi berkas KTM oleh administrator. Mohon tunggu maksimal 1x24 jam.';

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'pending_verification',
                    'message' => $pendingMsg,
                ], 403);
            }

            return back()->with('warning', $pendingMsg);
        }

        // 3. Cek apakah verifikasi KTM ditolak oleh admin
        if ($user->verification_status === 'rejected') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $reason = $user->rejection_reason ?? 'Foto KTM buram atau tidak sesuai identitas.';
            $rejectedMsg = 'Verifikasi KTM Anda ditolak admin: ' . $reason;

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => $rejectedMsg,
                    'rejection_reason' => $reason,
                ], 403);
            }

            return back()->with('error', $rejectedMsg);
        }

        // Akun aktif
        $request->session()->regenerate();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil.',
                'user' => $user,
                'redirect' => $user->role === 'admin'
                    ? route('admin.verifications.index')
                    : route('dashboard'),
            ]);
        }

        return $this->redirectAfterAuth($user)
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Proses pendaftaran akun baru (Register) dengan alur ganda kampus vs KTM.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email:filter', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'campus' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:25', 'unique:users,phone'],
            'ktm_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar sebelumnya.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'campus.required' => 'Asal kampus wajib dipilih/diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.unique' => 'Nomor WhatsApp ini sudah terdaftar sebelumnya.',
            'ktm_file.image' => 'Berkas KTM harus berupa gambar (JPG/PNG).',
            'ktm_file.max' => 'Ukuran berkas KTM maksimal 2 MB.',
        ]);

        // Cek domain email
        $emailParts = explode('@', $validated['email']);
        $domain = strtolower($emailParts[1] ?? '');

        // Verifikasi apakah domain cocok dengan whitelist kampus Malang
        $matchedCampus = CampusDomain::where('is_active', true)
            ->where(function ($query) use ($domain) {
                $query->where('domain_name', $domain)
                      ->orWhereRaw('? LIKE CONCAT(\'%.\', domain_name)', [$domain]);
            })
            ->first();

        // 1. Jika domain email kampus milik Kampus A, tapi user memilih Kampus B -> TOLAK!
        if ($matchedCampus && strcasecmp($matchedCampus->campus_name, $validated['campus']) !== 0) {
            $mismatchMsg = "Domain email @{$domain} adalah milik {$matchedCampus->campus_name}, tidak sesuai dengan kampus {$validated['campus']} yang Anda pilih.";

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $mismatchMsg,
                ], 422);
            }

            throw ValidationException::withMessages([
                'campus' => $mismatchMsg,
                'email' => $mismatchMsg,
            ]);
        }

        // 2. Jika user memilih kampus yang memiliki domain resmi, tapi email kampus (.ac.id) milik institusi lain -> TOLAK!
        $selectedCampusDomain = CampusDomain::where('is_active', true)
            ->where('campus_name', $validated['campus'])
            ->first();

        if ($selectedCampusDomain && str_ends_with($domain, '.ac.id')) {
            $isDomainOfSelectedCampus = ($domain === $selectedCampusDomain->domain_name) || str_ends_with($domain, '.' . $selectedCampusDomain->domain_name);
            if (!$isDomainOfSelectedCampus) {
                $mismatchMsg = "Kampus {$validated['campus']} menggunakan domain resmi @{$selectedCampusDomain->domain_name}. Domain @{$domain} tidak sesuai.";

                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $mismatchMsg,
                    ], 422);
                }

                throw ValidationException::withMessages([
                    'campus' => $mismatchMsg,
                    'email' => $mismatchMsg,
                ]);
            }
        }

        $isCampusDomain = (bool) $matchedCampus;

        // Fallback pola domain resmi kampus Indonesia (harus berakhiran .ac.id dan memilih kampus Lainnya)
        if (!$isCampusDomain && str_ends_with($domain, '.ac.id')) {
            $isCampusDomain = true;
        }

        $emailType = $isCampusDomain ? 'campus' : 'public';
        $verificationStatus = $isCampusDomain ? 'active' : 'pending';
        $ktmPath = null;

        // Jika email umum, wajib upload berkas KTM
        if (!$isCampusDomain) {
            if (!$request->hasFile('ktm_file')) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Pengguna dengan email umum wajib melampirkan foto Kartu Tanda Mahasiswa (KTM) aktif.',
                    ], 422);
                }

                throw ValidationException::withMessages([
                    'ktm_file' => 'Pengguna dengan email umum wajib melampirkan foto Kartu Tanda Mahasiswa (KTM) aktif.',
                ]);
            }

            // Simpan foto KTM ke storage/app/public/ktm
            $ktmPath = $request->file('ktm_file')->store('ktm', 'public');
        }

        // 1. Generate 6 digit numeric OTP
        $otp = (string) random_int(100000, 999999);
        $cacheKey = 'reg_otp_' . md5(strtolower($validated['email']));

        // 2. Simpan draft pendaftaran & OTP di Cache selama 10 menit
        Cache::put($cacheKey, [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'email_type' => $emailType,
            'ktm_image_path' => $ktmPath,
            'verification_status' => $verificationStatus,
            'password' => Hash::make($validated['password']),
            'role' => 'buyer',
            'campus' => $validated['campus'],
            'phone' => $validated['phone'],
            'status' => 'active',
            'otp' => $otp,
            'attempts' => 0,
            'resend_available_at' => now()->addSeconds(60)->timestamp,
        ], now()->addMinutes(10));

        // 3. Kirim email berisi kode OTP ke alamat pengguna
        try {
            Mail::to($validated['email'])->send(new OtpVerificationMail($validated['name'], $otp));
        } catch (\Throwable $e) {
            report($e);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'otp_required',
                'message' => 'Kode OTP verifikasi telah dikirimkan ke email Anda (' . $validated['email'] . ').',
                'email' => strtolower($validated['email']),
                'email_type' => $emailType,
                'resend_cooldown' => 60,
            ], 200);
        }

        return redirect()->route('register')->with([
            'otp_required' => true,
            'otp_email' => strtolower($validated['email']),
            'info' => 'Kode OTP verifikasi telah dikirimkan ke email ' . $validated['email'] . '. Silakan periksa kotak masuk atau spam email Anda.',
        ]);
    }

    /**
     * Verifikasi kode 6-digit OTP dan selesaikan pembuatan akun pengguna.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:filter'],
            'otp' => ['required', 'string', 'digits:6'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'otp.required' => 'Kode OTP 6 digit wajib dimasukkan.',
            'otp.digits' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $cacheKey = 'reg_otp_' . md5(strtolower($validated['email']));
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            $errorMsg = 'Sesi verifikasi OTP telah kedaluwarsa atau tidak ditemukan. Silakan kirim ulang OTP atau lakukan pendaftaran kembali.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        // Cek jika batas percobaan terlampaui (maksimal 5 kali)
        if (($cached['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);
            $errorMsg = 'Anda telah melebihi batas 5 kali percobaan OTP. Sesi pendaftaran dibatalkan demi keamanan, silakan daftar ulang.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        // Cek kecocokan kode OTP
        if ($cached['otp'] !== $validated['otp']) {
            $cached['attempts'] = ($cached['attempts'] ?? 0) + 1;
            Cache::put($cacheKey, $cached, now()->addMinutes(10));
            $remaining = 5 - $cached['attempts'];
            $errorMsg = "Kode OTP yang Anda masukkan salah. Sisa kesempatan mencoba: {$remaining} kali.";

            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg, 'remaining_attempts' => $remaining], 422);
            }
            return back()->with('error', $errorMsg);
        }

        // OTP Valid! Simpan user resmi ke tabel database users
        $user = User::create([
            'name' => $cached['name'],
            'email' => $cached['email'],
            'email_type' => $cached['email_type'],
            'ktm_image_path' => $cached['ktm_image_path'],
            'verification_status' => $cached['verification_status'],
            'password' => $cached['password'],
            'role' => $cached['role'],
            'campus' => $cached['campus'],
            'phone' => $cached['phone'],
            'status' => $cached['status'],
            'email_verified_at' => now(),
        ]);

        // Hapus cache OTP setelah berhasil
        Cache::forget($cacheKey);

        if ($cached['email_type'] === 'campus') {
            // Email kampus langsung login otomatis
            Auth::login($user);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Verifikasi email berhasil! Selamat datang di Lungsur.in, ' . $user->name . '.',
                    'redirect' => route('dashboard'),
                    'user' => $user,
                ], 200);
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Verifikasi email berhasil! Akun kampus Anda telah aktif.');
        } else {
            // Email umum menunggu verifikasi berkas KTM oleh admin
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'pending_verification',
                    'message' => 'Verifikasi email berhasil! Berkas KTM Anda sedang dalam antrean verifikasi administrator.',
                    'redirect' => route('login'),
                    'user' => $user,
                ], 200);
            }

            return redirect()->route('login')->with('info', 'Verifikasi email berhasil! Akun Anda sedang dalam antrean verifikasi berkas KTM oleh administrator (maksimal 1x24 jam).');
        }
    }

    /**
     * Kirim ulang kode OTP verifikasi (dengan batas cooldown 60 detik).
     */
    public function resendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:filter'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $cacheKey = 'reg_otp_' . md5(strtolower($validated['email']));
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            $errorMsg = 'Sesi pendaftaran tidak ditemukan atau telah kedaluwarsa. Silakan lakukan pendaftaran ulang.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        // Cek batasan cooldown 60 detik
        if (now()->timestamp < ($cached['resend_available_at'] ?? 0)) {
            $secondsLeft = $cached['resend_available_at'] - now()->timestamp;
            $errorMsg = "Mohon tunggu {$secondsLeft} detik sebelum meminta kode OTP baru.";

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMsg,
                    'seconds_left' => $secondsLeft,
                ], 429);
            }
            return back()->with('warning', $errorMsg);
        }

        // Generate kode OTP baru
        $newOtp = (string) random_int(100000, 999999);
        $cached['otp'] = $newOtp;
        $cached['attempts'] = 0;
        $cached['resend_available_at'] = now()->addSeconds(60)->timestamp;

        Cache::put($cacheKey, $cached, now()->addMinutes(10));

        try {
            Mail::to($cached['email'])->send(new OtpVerificationMail($cached['name'], $newOtp));
        } catch (\Throwable $e) {
            report($e);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP baru telah berhasil dikirimkan ke email Anda (' . $cached['email'] . ').',
                'resend_cooldown' => 60,
            ], 200);
        }

        return back()->with('success', 'Kode OTP baru telah berhasil dikirimkan ke email Anda.');
    }

    /**
     * Proses keluar sistem (Logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil keluar dari sistem.',
            ]);
        }

        return redirect('/')->with('info', 'Anda telah keluar dari akun.');
    }

    /**
     * Arahkan user ke halaman yang sesuai dengan role-nya setelah login.
     */
    protected function redirectAfterAuth(User $user)
    {
        // Admin selalu ke panel admin (tanpa intended, supaya tidak terseret ke URL user)
        if ($user->role === 'admin') {
            return redirect()->route('admin.verifications.index');
        }

        return redirect()->intended(route('dashboard'));
    }
}