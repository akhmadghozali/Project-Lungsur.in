<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Cek jika akun disuspend / dibanned
        if ($user->status === 'suspended' || $user->status === 'banned') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda ditangguhkan karena pelanggaran ketentuan platform.');
        }

        // Blokir jika akun berstatus pending verifikasi berkas KTM
        if ($user->verification_status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('warning', 'Akun Anda sedang dalam proses verifikasi berkas KTM oleh administrator. Mohon tunggu maksimal 1x24 jam.');
        }

        // Blokir jika verifikasi KTM ditolak
        if ($user->verification_status === 'rejected') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $reason = $user->rejection_reason ?? 'Foto KTM buram atau tidak sesuai identitas.';
            return redirect()->route('login')->with('error', 'Verifikasi KTM Anda ditolak admin: ' . $reason);
        }

        return $next($request);
    }
}
