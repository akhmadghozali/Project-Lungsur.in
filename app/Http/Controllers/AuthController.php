<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CampusDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            return redirect()->intended('/dashboard');
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
            return redirect()->intended('/dashboard');
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
            ]);
        }

        return redirect()->intended('/dashboard')->with('success', 'Selamat datang kembali, ' . $user->name . '!');
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

        // Simpan data user baru
        $user = User::create([
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
        ]);

        if ($isCampusDomain) {
            // Email kampus langsung login otomatis
            Auth::login($user);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pendaftaran berhasil. Akun kampus Anda langsung aktif.',
                    'user' => $user,
                ], 201);
            }

            return redirect()->intended('/dashboard')->with('success', 'Pendaftaran berhasil! Akun kampus Anda telah aktif.');
        } else {
            // Email umum menunggu verifikasi
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'pending_verification',
                    'message' => 'Pendaftaran berhasil. Akun Anda sedang menunggu verifikasi berkas KTM oleh administrator.',
                    'user' => $user,
                ], 201);
            }

            return redirect()->route('login')->with('info', 'Pendaftaran berhasil! Akun Anda sedang dalam antrean verifikasi berkas KTM oleh administrator (maksimal 1x24 jam).');
        }
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
}
