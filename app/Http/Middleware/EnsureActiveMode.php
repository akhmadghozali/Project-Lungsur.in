<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredMode): Response
    {
        $user = $request->user();

        // Jika user belum login, serahkan ke middleware 'auth'
        if (!$user) {
            return $next($request);
        }

        // Admin dibebaskan dari batasan mode
        if ($user->role === 'admin') {
            return $next($request);
        }

        $currentMode = $user->getActiveMode();

        // Jika mode saat ini sesuai, izinkan lewat
        if ($currentMode === $requiredMode) {
            return $next($request);
        }

        $requiredLabel = $requiredMode === 'seller' ? 'Mode Penjual' : 'Mode Pembeli';
        $currentLabel  = $currentMode === 'seller' ? 'Mode Penjual' : 'Mode Pembeli';

        // Jika pengguna menyertakan query param switch_mode=1, langsung alihkan mode & lanjutkan
        if ($request->has('switch_mode')) {
            session(['user_mode' => $requiredMode]);
            $user->update(['role' => $requiredMode]);

            return redirect($request->fullUrlWithQuery(['switch_mode' => null]))
                ->with('success', "Akun Anda otomatis dialihkan ke {$requiredLabel}.");
        }

        // Untuk request selain GET (POST/PUT/DELETE)
        if (!$request->isMethod('GET')) {
            return back()->with('warning', "Aksi ini khusus diperuntukkan bagi {$requiredLabel}. Silakan ganti mode akun Anda terlebih dahulu.");
        }

        // Untuk request GET, arahkan ke dasbor dengan flash data modal/banner ramah
        return redirect()->route('dashboard')->with('mode_restriction', [
            'required_mode'  => $requiredMode,
            'required_label' => $requiredLabel,
            'current_label'  => $currentLabel,
            'target_url'     => $request->fullUrlWithQuery(['switch_mode' => 1]),
            'message'        => "Halaman yang ingin Anda buka hanya dapat diakses dalam {$requiredLabel}. Ingin beralih ke {$requiredLabel} sekarang?",
        ]);
    }
}
