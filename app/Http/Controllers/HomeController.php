<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Listing;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.verifications.index');
        }

        $search = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'latest');

        $uniqueBiddersQuery = AuctionBid::query()
            ->selectRaw('COUNT(DISTINCT user_id)')
            ->whereColumn('auction_id', 'auctions.id');

        $spotlight = Auction::with([
            'listing.category',
            'listing.images',
            'highestBid.user',
        ])
            ->where('auctions.status', 'active')
            ->where(function ($query) {
                $query->whereNull('auctions.start_time')
                    ->orWhere('auctions.start_time', '<=', now());
            })
            ->whereNotNull('auctions.end_time')
            ->where('auctions.end_time', '>', now())
            ->whereHas('bids')
            ->whereHas('listing')
            ->select('auctions.*')
            ->selectSub($uniqueBiddersQuery, 'unique_bidders_count')
            ->orderByDesc('unique_bidders_count')
            ->orderByDesc('auctions.created_at')
            ->first();

        $query = Listing::available()->with(['category', 'images']);

        $isBoostedOnly = false;

        if ($search !== '') {
            // Mode Pencarian: cari berdasarkan judul, deskripsi, atau nama kategori
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhereHas('category', function ($catQuery) use ($search) {
                      $catQuery->where('name', 'ilike', "%{$search}%");
                  });
            });

            // Pada hasil pencarian, prioritaskan iklan yang di-boost di atas
            $query->orderByDesc('is_boosted');
        } else {
            // Mode Beranda Normal: Cek apakah ada iklan yang aktif di-boost
            $hasBoosted = Listing::available()->boosted()->exists();

            if ($hasBoosted) {
                // Tampilkan HANYA iklan yang di-boost saja sesuai instruksi
                $query->boosted();
                $isBoostedOnly = true;
            } else {
                // Jika tidak ada iklan boost, fallback ke random
                $isBoostedOnly = false;
            }
        }

        // Filter pengurutan
        if ($sort === 'lowest') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('price', 'desc');
        } else {
            $sort = 'latest';
            if (!$isBoostedOnly && $search === '') {
                // Fallback ke random jika tidak ada iklan boost dan tidak sedang mencari
                $query->inRandomOrder();
            } else {
                $query->latest();
            }
        }

        if (auth()->check()) {
            // User login: 12 barang per halaman
            $listings = $query->paginate(12)->withQueryString();
        } else {
            // Guest: maksimal 10 barang
            $listings = $query->limit(10)->get();
        }

        return view('welcome', compact(
            'listings',
            'sort',
            'search',
            'isBoostedOnly',
            'spotlight'
        ));
    }
}