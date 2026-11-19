<?php

namespace App\Http\Controllers;

use App\Models\AuctionBid;
use App\Models\User;
use App\Models\WantedAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DaganganController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $mode = $user->getActiveMode();

        // Hitung jumlah dari sumber yang sama persis dengan DashboardController
        $myListingsCount = $user->listings()->count();

        $myListings = $user->listings()
            ->with(['category', 'images', 'latestBoostPayment', 'auction.latestPayment'])
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $myAuctions = $user->listings()
            ->whereHas('auction')
            ->with(['auction.highestBid.user', 'auction.bids', 'images', 'category'])
            ->latest()
            ->get();
        $myAuctionsCount = $myAuctions->count();

        $wantedOpportunities = WantedAd::active()
            ->where('user_id', '!=', $user->id)
            ->with(['user', 'category'])
            ->latest()
            ->take(12)
            ->get();

        $myActiveListings = $user->listings()
            ->where('status', 'available')
            ->orderBy('title')
            ->get();

        $buyers = User::whereHas('buyerConversations', function ($query) use ($user) {
            $query->where('seller_id', $user->id);
        })
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        // Data pendukung jika dibuka saat Mode Pembeli
        $myBids = AuctionBid::with(['auction.listing.images', 'auction.highestBid'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(12)
            ->get();

        $myBidsCount = AuctionBid::where('user_id', $user->id)
            ->distinct('auction_id')
            ->count('auction_id');

        $myWantedAds = $user->wantedAds()
            ->with('category')
            ->latest()
            ->get();
        $myWantedAdsCount = $myWantedAds->count();

        $purchasedListings = $user->purchasedListings()
            ->with(['user', 'category', 'images'])
            ->latest()
            ->take(6)
            ->get();

        $wonAuctions = $user->wonAuctions()
            ->with(['listing.category', 'listing.images', 'highestBid'])
            ->latest()
            ->take(6)
            ->get();

        $activeTab = $request->query('tab', $mode === 'seller' ? 'dagangan' : 'lelang');

        return view('dagangan.index', compact(
            'user',
            'mode',
            'activeTab',
            'myListingsCount',
            'myListings',
            'myAuctionsCount',
            'myAuctions',
            'wantedOpportunities',
            'myActiveListings',
            'buyers',
            'myBids',
            'myBidsCount',
            'myWantedAds',
            'myWantedAdsCount',
            'purchasedListings',
            'wonAuctions'
        ));
    }
}