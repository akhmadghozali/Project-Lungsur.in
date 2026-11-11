<?php

namespace App\Http\Controllers;

use App\Models\AuctionBid;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
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
            ->with(['category', 'images', 'latestBoostPayment', 'auction.latestPayment'])
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $buyers = User::whereHas('buyerConversations', function ($query) use ($user) {
            $query->where('seller_id', $user->id);
          })
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

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
            'myBids',
            'buyers'
        ));
    }

    /**
     * Tampilkan riwayat transaksi pengguna.
     */
    public function history(): View
    {
        $user = Auth::user();

        // Riwayat barang yang dibeli
        $purchasedListings = $user->purchasedListings()
            ->with(['user', 'category', 'images'])
            ->latest()
            ->get();

        // Riwayat lelang yang dimenangkan
        $wonAuctions = $user->wonAuctions()
            ->with(['listing.category', 'listing.images', 'highestBid'])
            ->latest()
            ->get();

        return view('dashboard.history', compact(
            'user',
            'purchasedListings',
            'wonAuctions'
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
        abort_if(
            $listing->user_id !== Auth::id(),
            403,
            'Anda tidak memiliki hak untuk mengelola iklan ini.'
        );

    $targetUrl = strtok(url()->previous(), '#') . '#listing-' . $listing->id;

    if ($listing->status === 'lelang' && $listing->auction && $listing->auction->status === 'active') {
        return redirect()->to($targetUrl)
            ->with('warning', "Iklan '{$listing->title}' sedang dalam sesi lelang aktif sehingga statusnya tidak dapat diubah langsung.");
    }

    try {
        $validated = $request->validate([
            'status' => ['required', 'in:available,sold,booked,archived'],
            'buyer_id' => ['nullable', 'required_if:status,sold', 'integer', 'exists:users,id'],
        ], [
            'status.required' => 'Status iklan wajib diisi.',
            'status.in' => 'Pilihan status iklan tidak valid.',
            'buyer_id.required_if' => 'Pilih pembeli terlebih dahulu.',
            'buyer_id.exists' => 'Data pembeli tidak ditemukan.',
        ]);



        if ($validated['status'] === 'sold') {
            $validBuyer = $listing->conversations()
                ->where('buyer_id', $validated['buyer_id'])
                ->exists();

            if (! $validBuyer || (int) $validated['buyer_id'] === (int) $listing->user_id) {
                return redirect()->to($targetUrl)
                    ->with('error', 'Pembeli harus merupakan pengguna yang pernah menghubungi iklan ini.');
            }
        }

        $listing->update([
            'status' => $validated['status'],
            'buyer_id' => $validated['status'] === 'sold'
                ? $validated['buyer_id']
                : null,
        ]);

        $statusLabel = [
            'available' => 'Tersedia',
            'sold' => 'Terjual',
            'booked' => 'Dibooking',
            'archived' => 'Diarsipkan',
        ][$validated['status']];

        return redirect()->to($targetUrl)
            ->with('success', "Status iklan '{$listing->title}' berhasil diubah menjadi {$statusLabel}.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->to($targetUrl)
                ->withErrors($e->validator)
                ->with('error', $e->validator->errors()->first() ?? 'Data tidak valid.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->to($targetUrl)
                ->with('error', 'Terjadi kesalahan saat memperbarui status iklan. Silakan coba lagi.');
        }
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
