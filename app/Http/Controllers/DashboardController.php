<?php

namespace App\Http\Controllers;

use App\Models\AuctionBid;
use App\Models\Listing;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dasbor profil & ringkasan aktivitas pengguna.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $mode = $user->getActiveMode();

        // 1. Data Statistik Riil dari Database
        $myListingsCount = $user->listings()->count();
        $activeListingsCount = $user->listings()->where('status', 'available')->count();
        $soldListingsCount = $user->listings()->where('status', 'sold')->count();

        $myBidsCount = AuctionBid::where('user_id', $user->id)
            ->distinct('auction_id')
            ->count('auction_id');

        $unreadMessagesCount = Message::where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->whereHas('conversation', function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->count();

        // 2. Daftar Iklan Milik User (untuk Mode Penjual)
        $myListings = $user->listings()
            ->with(['category', 'images'])
            ->latest()
            ->paginate(8)
            ->withQueryString();

        // 3. Riwayat Lelang yang Diikuti (untuk Mode Pembeli)
        $myBids = AuctionBid::with(['auction.listing.images', 'auction.highestBid'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'user',
            'mode',
            'myListingsCount',
            'activeListingsCount',
            'soldListingsCount',
            'myBidsCount',
            'unreadMessagesCount',
            'myListings',
            'myBids'
        ));
    }

    /**
     * Alihkan mode antarmuka pengguna (Mode Penjual ⇄ Mode Pembeli).
     */
    public function switchMode(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $targetMode = $request->input('mode');

        if (!in_array($targetMode, ['buyer', 'seller'])) {
            $currentMode = $user->getActiveMode();
            $targetMode = $currentMode === 'seller' ? 'buyer' : 'seller';
        }

        session(['user_mode' => $targetMode]);
        $user->update(['role' => $targetMode]);

        $label = $targetMode === 'seller' ? 'Mode Penjual' : 'Mode Pembeli';

        return back()->with('success', "Berhasil beralih ke {$label}.");
    }

    /**
     * Perbarui status iklan (misal: tandai 'sold' atau 'available').
     */
    public function updateListingStatus(Request $request, Listing $listing): RedirectResponse
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Anda tidak memiliki hak untuk mengelola iklan ini.');

        $validated = $request->validate([
            'status' => ['required', 'in:available,sold,booked,archived'],
        ]);

        $listing->update(['status' => $validated['status']]);

        $statusLabel = [
            'available' => 'Tersedia',
            'sold'      => 'Terjual',
            'booked'    => 'Dibooking',
            'archived'  => 'Diarsipkan',
        ][$validated['status']] ?? $validated['status'];

        return back()->with('success', "Status iklan '{$listing->title}' berhasil diubah menjadi {$statusLabel}.");
    }

    /**
     * Hapus iklan milik sendiri.
     */
    public function destroyListing(Listing $listing): RedirectResponse
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Anda tidak memiliki hak untuk menghapus iklan ini.');

        $title = $listing->title;
        $listing->delete();

        return back()->with('success', "Iklan '{$title}' berhasil dihapus.");
    }
}
