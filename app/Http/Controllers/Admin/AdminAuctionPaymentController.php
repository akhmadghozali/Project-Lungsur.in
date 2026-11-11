<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuctionPaymentController;
use App\Http\Controllers\Controller;
use App\Models\AuctionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminAuctionPaymentController extends Controller
{
    /**
     * Tampilkan antrean dan riwayat verifikasi pembayaran pembukaan sesi lelang.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');

        $baseQuery = AuctionPayment::with(['user', 'auction.listing.images', 'verifier']);

        if ($statusFilter !== 'all') {
            $baseQuery->where('status', $statusFilter);
        }

        $payments = (clone $baseQuery)
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending' => AuctionPayment::where('status', 'pending')->count(),
            'approved_today' => AuctionPayment::where('status', 'verified')
                ->whereDate('verified_at', today())
                ->count(),
            'total_verified' => AuctionPayment::where('status', 'verified')->count(),
            'total_revenue' => AuctionPayment::where('status', 'verified')->sum('amount'),
        ];

        return view('admin.auction_payments.index', compact('payments', 'stats', 'statusFilter'));
    }

    /**
     * Setujui pembayaran pembukaan lelang.
     */
    public function approve(Request $request, AuctionPayment $auctionPayment): RedirectResponse
    {
        if (! $auctionPayment->isPending()) {
            return redirect()->route('admin.payments.auction')
                ->with('warning', "Transaksi {$auctionPayment->invoice_code} sudah diproses sebelumnya.");
        }

        DB::transaction(function () use ($auctionPayment): void {
            $auctionPayment->update([
                'status' => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'rejection_note' => null,
            ]);

            $auction = $auctionPayment->auction()->with('listing')->first();
            if ($auction) {
                $auction->update([
                    'status' => 'active',
                    'start_time' => now(),
                    'end_time' => now()->addDays(AuctionPaymentController::DURATION_DAYS),
                ]);

                if ($auction->listing && $auction->listing->status === 'available') {
                    $auction->listing->update(['status' => 'lelang']);
                }
            }
        });

        return redirect()->route('admin.payments.auction')
            ->with('success', "Pembayaran lelang {$auctionPayment->invoice_code} berhasil disetujui! Sesi lelang kini resmi aktif.");
    }

    /**
     * Tolak pembayaran pembukaan lelang.
     */
    public function reject(Request $request, AuctionPayment $auctionPayment): RedirectResponse
    {
        if (! $auctionPayment->isPending()) {
            return redirect()->route('admin.payments.auction')
                ->with('warning', "Transaksi {$auctionPayment->invoice_code} sudah diproses sebelumnya.");
        }

        $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = trim((string) $request->input('rejection_note'));
        if ($reason === '') {
            $reason = 'Bukti pembayaran QRIS tidak terbaca, nominal kurang dari Rp5.000, atau transaksi belum masuk.';
        }

        DB::transaction(function () use ($auctionPayment, $reason): void {
            $auctionPayment->update([
                'status' => 'rejected',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'rejection_note' => $reason,
            ]);

            if ($auctionPayment->auction && $auctionPayment->auction->status === 'pending_approval') {
                $auctionPayment->auction->update([
                    'status' => 'pending_payment',
                ]);
            }
        });

        return redirect()->route('admin.payments.auction')
            ->with('warning', "Pembayaran lelang {$auctionPayment->invoice_code} telah ditolak.");
    }
}

