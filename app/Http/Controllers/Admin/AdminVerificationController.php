<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVerificationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $pendingQuery = function () use ($search) {
            $query = User::where('verification_status', 'pending');
            if ($search !== '') {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'ilike', "%{$search}%")
                        ->orWhere('nim', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('campus', 'ilike', "%{$search}%");
                });
            }
            return $query;
        };

        // Pendaftar yang sedang dibuka (?user=ID), default: antrean pertama dari hasil query
        $selected = null;
        if ($request->filled('user')) {
            $selected = User::where('verification_status', 'pending')->find($request->query('user'));
        }
        $selected ??= $pendingQuery()->oldest()->first();

        // Antrean lainnya (diurutkan berdasarkan waktu masuk)
        $queue = $pendingQuery()
            ->when($selected, fn ($q) => $q->where('id', '!=', $selected->id))
            ->oldest()
            ->limit(10)
            ->get();

        $stats = [
            'pending' => User::where('verification_status', 'pending')->count(),
            'approved_today' => User::where('verification_status', 'active')
                ->whereNotNull('verified_by')
                ->whereDate('verified_at', today())
                ->count(),
            'rejected_today' => User::where('verification_status', 'rejected')
                ->whereDate('verified_at', today())
                ->count(),
            'total_verified' => User::where('verification_status', 'active')
                ->whereNotNull('verified_by')
                ->count(),
        ];

        $recentQuery = User::whereIn('verification_status', ['active', 'rejected'])
            ->whereNotNull('verified_at');

        if ($search !== '') {
            $recentQuery->where(function ($sub) use ($search) {
                $sub->where('name', 'ilike', "%{$search}%")
                    ->orWhere('nim', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('campus', 'ilike', "%{$search}%");
            });
        }

        $recent = $recentQuery->latest('verified_at')
            ->limit(6)
            ->get();

        return view('admin.verifications.index', compact('selected', 'queue', 'stats', 'recent', 'search'));
    }

    public function approve(Request $request, User $user)
    {
        // Cegah memproses akun admin atau diri sendiri
        if ($user->role === 'admin' || (int) $user->id === (int) $request->user()->id) {
            return redirect()
                ->route('admin.verifications.index')
                ->with('error', 'Tidak dapat memproses verifikasi pada akun administrator.');
        }

        // Cegah memproses akun yang sudah bukan status pending
        if ($user->verification_status !== 'pending') {
            return redirect()
                ->route('admin.verifications.index')
                ->with('warning', "Akun {$user->name} sudah diproses sebelumnya (Status: {$user->verification_status}).");
        }

        $user->update([
            'verification_status' => 'active',
            'rejection_reason' => null,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.verifications.index')
            ->with('success', "Akun {$user->name} berhasil diverifikasi.");
    }

    public function reject(Request $request, User $user)
    {
        // Cegah memproses akun admin atau diri sendiri
        if ($user->role === 'admin' || (int) $user->id === (int) $request->user()->id) {
            return redirect()
                ->route('admin.verifications.index')
                ->with('error', 'Tidak dapat menolak verifikasi pada akun administrator.');
        }

        // Cegah memproses akun yang sudah bukan status pending
        if ($user->verification_status !== 'pending') {
            return redirect()
                ->route('admin.verifications.index')
                ->with('warning', "Akun {$user->name} sudah diproses sebelumnya (Status: {$user->verification_status}).");
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $user->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.verifications.index')
            ->with('success', "Akun {$user->name} ditolak.");
    }

    /**
     * Sajikan foto KTM secara aman khusus untuk administrator.
     * Mendukung private storage (disk: local), public storage (backward compatibility),
     * dan fallback SVG jika file fisik belum tersedia di server deploy baru.
     */
    public function showKtm(User $user)
    {
        if (empty($user->ktm_image_path)) {
            return response($this->getPlaceholderKtmSvg($user->name), 200, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'private, no-cache',
            ]);
        }

        $path = $user->ktm_image_path;

        // 1. Cek disk 'local' (Private storage utama)
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path, null, [
                'Cache-Control' => 'private, no-cache, must-revalidate',
            ]);
        }

        // 2. Cek disk 'public' (Backward compatibility untuk seeder/upload lama)
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path, null, [
                'Cache-Control' => 'private, no-cache, must-revalidate',
            ]);
        }

        // 3. Fallback SVG jika file fisik tidak ada di server
        return response($this->getPlaceholderKtmSvg($user->name), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, no-cache',
        ]);
    }

    private function getPlaceholderKtmSvg(string $name): string
    {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="380" viewBox="0 0 600 380">
    <rect width="600" height="380" rx="16" fill="#1e293b"/>
    <rect x="24" y="24" width="552" height="332" rx="12" fill="#0f172a" stroke="#334155" stroke-width="2"/>
    <circle cx="100" cy="140" r="45" fill="#3b82f6"/>
    <text x="100" y="150" font-family="sans-serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle">KTM</text>
    <text x="170" y="130" font-family="sans-serif" font-size="20" font-weight="bold" fill="#f8fafc">KARTU TANDA MAHASISWA</text>
    <text x="170" y="160" font-family="sans-serif" font-size="14" fill="#94a3b8">Nama: {$safeName}</text>
    <text x="170" y="185" font-family="sans-serif" font-size="12" fill="#64748b">Pratinjau placeholder (berkas fisik belum tersedia)</text>
    <rect x="24" y="290" width="552" height="66" rx="0" fill="#1e293b"/>
    <text x="300" y="330" font-family="sans-serif" font-size="12" font-weight="600" fill="#cbd5e1" text-anchor="middle">LUNGSUR.IN VERIFIKASI IDENTITAS MAHASISWA</text>
</svg>
SVG;
    }
}