<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller
{
    public function index(Request $request)
    {
        $pendingQuery = fn () => User::where('verification_status', 'pending');

        // Pendaftar yang sedang dibuka (?user=ID), default: antrean paling lama
        $selected = null;
        if ($request->filled('user')) {
            $selected = $pendingQuery()->find($request->query('user'));
        }
        $selected ??= $pendingQuery()->oldest()->first();

        // Antrean lainnya (diurutkan berdasarkan waktu masuk)
        $queue = $pendingQuery()
            ->when($selected, fn ($q) => $q->where('id', '!=', $selected->id))
            ->oldest()
            ->limit(10)
            ->get();

        $stats = [
            'pending' => $pendingQuery()->count(),
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

        $recent = User::whereIn('verification_status', ['active', 'rejected'])
            ->whereNotNull('verified_at')
            ->latest('verified_at')
            ->limit(6)
            ->get();

        return view('admin.verifications.index', compact('selected', 'queue', 'stats', 'recent'));
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
}