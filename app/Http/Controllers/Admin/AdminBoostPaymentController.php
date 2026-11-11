<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminBoostPaymentController extends Controller
{
    /**
     * Tampilkan antrean dan riwayat verifikasi pembayaran boost iklan.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');

        $baseQuery = BoostPayment::with(['user', 'listing.images', 'verifier']);

        if ($statusFilter !== 'all') {
            $baseQuery->where('status', $statusFilter);
        }

        // Urutan: pending paling atas (prioritas ditinjau), lalu terbaru
        $payments = (clone $baseQuery)
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        // Data statistik transaksi monetisasi
        $stats = [
            'pending' => BoostPayment::where('status', 'pending')->count(),
            'approved_today' => BoostPayment::where('status', 'verified')
                ->whereDate('verified_at', today())
                ->count(),
            'total_verified' => BoostPayment::where('status', 'verified')->count(),
            'total_revenue' => BoostPayment::where('status', 'verified')->sum('amount'),
        ];

        // Item yang sedang aktif dipilih untuk pratinjau struk (?payment=ID)
        $selected = null;
        if ($request->filled('payment')) {
            $selected = BoostPayment::with(['user', 'listing.images', 'verifier'])->find($request->query('payment'));
        }
        $selected ??= BoostPayment::with(['user', 'listing.images', 'verifier'])
            ->where('status', 'pending')
            ->oldest('created_at')
            ->first();

        return view('admin.boost_payments.index', compact('payments', 'selected', 'stats', 'statusFilter'));
    }

    /**
     * Setujui pembayaran boost iklan: aktifkan is_boosted = true dan boosted_until = +30 hari.
     */
    public function approve(Request $request, BoostPayment $boostPayment): RedirectResponse
    {
        // 1. Update status record pembayaran
        $boostPayment->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_note' => null,
        ]);

        // 2. Aktifkan boost pada listing selama 30 Hari penuh
        $expiryDate = now()->addDays(30);
        $boostPayment->listing->update([
            'is_boosted' => true,
            'boosted_until' => $expiryDate,
        ]);

        $formattedExpiry = $expiryDate->translatedFormat('d F Y');

        return redirect()->route('admin.payments.boost')
            ->with('success', "Pembayaran {$boostPayment->invoice_code} berhasil disetujui! Iklan \"{$boostPayment->listing->title}\" otomatis meloncat ke urutan teratas katalog selama 30 hari hingga {$formattedExpiry}. 🎉");
    }

    /**
     * Tolak pembayaran boost iklan dengan catatan alasan penolakan.
     */
    public function reject(Request $request, BoostPayment $boostPayment): RedirectResponse
    {
        $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = trim((string) $request->input('rejection_note'));
        if ($reason === '') {
            $reason = 'Bukti pembayaran tidak terbaca, tidak valid, atau dana transfer belum masuk ke rekening Lungsur.in.';
        }

        $boostPayment->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_note' => $reason,
        ]);

        return redirect()->route('admin.payments.boost')
            ->with('warning', "Pembayaran {$boostPayment->invoice_code} untuk iklan \"{$boostPayment->listing->title}\" telah ditolak.");
    }
}

