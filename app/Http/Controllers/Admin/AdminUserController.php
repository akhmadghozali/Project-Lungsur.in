<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna mahasiswa dengan filter & pencarian.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $search = trim($request->query('q', ''));

        $query = User::where('role', '!=', 'admin');

        // Filter tab
        if ($statusFilter === 'active') {
            $query->where('status', 'active')->where('verification_status', 'active');
        } elseif ($statusFilter === 'pending_ktm') {
            $query->where('verification_status', 'pending');
        } elseif ($statusFilter === 'suspended') {
            $query->where('status', 'suspended');
        } elseif ($statusFilter === 'banned') {
            $query->where('status', 'banned');
        }

        // Pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('campus', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('listings')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Hitung total untuk badge tab
        $tabCounts = [
            'all' => User::where('role', '!=', 'admin')->count(),
            'active' => User::where('role', '!=', 'admin')->where('status', 'active')->where('verification_status', 'active')->count(),
            'pending_ktm' => User::where('role', '!=', 'admin')->where('verification_status', 'pending')->count(),
            'suspended' => User::where('role', '!=', 'admin')->where('status', 'suspended')->count(),
            'banned' => User::where('role', '!=', 'admin')->where('status', 'banned')->count(),
        ];

        return view('admin.users.index', compact('users', 'statusFilter', 'search', 'tabCounts'));
    }

    /**
     * Tampilkan detail riwayat aktivitas akun mahasiswa.
     */
    public function show(User $user)
    {
        abort_if($user->role === 'admin', 404);

        $user->loadCount(['listings', 'bids', 'boostPayments', 'auctionPayments']);
        $recentListings = $user->listings()->latest()->take(6)->get();
        $reportsReceived = Report::where('reported_user_id', $user->id)->with('reporter')->latest()->get();

        return view('admin.users.show', compact('user', 'recentListings', 'reportsReceived'));
    }

    /**
     * Tangguhkan (suspend) akses akun sementara.
     */
    public function suspend(Request $request, User $user)
    {
        abort_if($user->role === 'admin', 403, 'Tidak dapat mengubah status administrator.');

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Alasan penangguhan akun wajib diisi.',
        ]);

        $user->update([
            'status' => 'suspended',
            'rejection_reason' => $validated['reason'],
        ]);

        return back()->with('success', "Akun {$user->name} berhasil ditangguhkan sementara.");
    }

    /**
     * Blokir permanen (ban) akun mahasiswa bermasalah dan turunkan seluruh iklan aktif.
     */
    public function ban(Request $request, User $user)
    {
        abort_if($user->role === 'admin', 403, 'Tidak dapat memblokir administrator.');

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Alasan pemblokiran permanen wajib diisi.',
        ]);

        $user->update([
            'status' => 'banned',
            'rejection_reason' => $validated['reason'],
        ]);

        // Otomatis men-takedown (arsip) seluruh iklan aktif miliknya sesuai UC 15
        $takedownCount = $user->listings()
            ->where('status', 'available')
            ->update(['status' => 'archived']);

        $msg = "Akun {$user->name} berhasil diblokir permanen.";
        if ($takedownCount > 0) {
            $msg .= " Sebanyak {$takedownCount} iklan aktif miliknya otomatis diturunkan dari katalog.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Pulihkan kembali status akun menjadi aktif (reactivate / unban).
     */
    public function reactivate(User $user)
    {
        abort_if($user->role === 'admin', 403, 'Tidak dapat memodifikasi administrator.');

        $user->update([
            'status' => 'active',
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Akun {$user->name} berhasil dipulihkan kembali menjadi aktif.");
    }
}
