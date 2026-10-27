<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Listing;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.verifications.index');
        }

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

        // Filter pengurutan
        $sort = request('sort', 'latest');

        if ($sort === 'lowest') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('price', 'desc');
        } else {
            $sort = 'latest';
            $query->latest();
        }

        if (auth()->check()) {
            // User login: 12 barang per halaman
            $listings = $query->paginate(12)->withQueryString();
        } else {
            // Guest: maksimal 10 barang acak
            $listings = $query->inRandomOrder()->limit(10)->get();
        }

        return view('welcome', compact('listings', 'sort', 'spotlight'));
    }
}