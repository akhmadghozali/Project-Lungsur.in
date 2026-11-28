<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Belum login, lanjutkan ke middleware autentikasi.
        if (!$user) {
            return $next($request);
        }

        // Admin tidak terkena pemblokiran akun mahasiswa.
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Memperbarui status jika ban sementara sudah berakhir.
        $user->refreshBanStatus();

        // Ban sementara yang masih berlaku atau ban permanen.
        if (in_array($user->status, ['suspended', 'banned'], true)) {
            if ($user->status === 'banned') {
                $message = 'Akun Anda diblokir secara permanen karena pelanggaran ketentuan platform.';
            } else {
                $expiresAt = $user->ban_expires_at->timezone('Asia/Jakarta');

                $message = 'Akun Anda sedang diblokir sementara karena pelanggaran ketentuan platform. '
                    . 'Masa pemblokiran berakhir pada '
                    . $expiresAt->translatedFormat('d F Y, H:i')
                    . ' WIB.';
            }

            Auth::logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'account_banned',
                    'message' => $message,
                ], 403);
            }

            return redirect()->route('login')->with('error', $message);
        }

        return $next($request);
    }
}
