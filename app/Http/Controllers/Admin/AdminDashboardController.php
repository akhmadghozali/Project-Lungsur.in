<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionPayment;
use App\Models\BoostPayment;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'all');

        // Tentukan batas waktu berdasarkan filter
        $startDate = match ($period) {
            '7d' => Carbon::now()->subDays(7)->startOfDay(),
            '30d' => Carbon::now()->subDays(30)->startOfDay(),
            'month' => Carbon::now()->startOfMonth(),
            default => null,
        };

        // 1. KPI Cards
        $totalStudents = User::where('role', '!=', 'admin')->where('status', 'active')->count();
        $pendingKtm = User::where('verification_status', 'pending')->count();
        $suspendedUsers = User::whereIn('status', ['suspended', 'banned'])->count();

        $activeListingsQuery = Listing::where('status', 'available');
        $activeAuctionsQuery = Auction::where('status', 'active');
        $boostPaymentQuery = BoostPayment::where('status', 'verified');
        $auctionPaymentQuery = AuctionPayment::where('status', 'verified');
        $reportsQuery = Report::query();

        if ($startDate) {
            $boostPaymentQuery->where('verified_at', '>=', $startDate);
            $auctionPaymentQuery->where('verified_at', '>=', $startDate);
            $reportsQuery->where('created_at', '>=', $startDate);
        }

        $activeListings = $activeListingsQuery->count();
        $activeAuctions = $activeAuctionsQuery->count();

        $boostRevenue = (float) $boostPaymentQuery->sum('amount');
        $auctionRevenue = (float) $auctionPaymentQuery->sum('amount');
        $totalRevenue = $boostRevenue + $auctionRevenue;

        $pendingBoostCount = BoostPayment::where('status', 'pending')->count();
        $pendingAuctionCount = AuctionPayment::where('status', 'pending')->count();
        $pendingReportsCount = Report::where('status', 'pending')->count();

        // 2. Data Grafik Kategori Terpopuler (Listing count per kategori)
        $categoriesData = Category::withCount(['listings' => function ($q) {
            $q->where('status', 'available');
        }])->orderByDesc('listings_count')->take(6)->get();

        $categoryChart = [
            'labels' => $categoriesData->pluck('name')->toArray(),
            'data' => $categoriesData->pluck('listings_count')->toArray(),
        ];

        // 3. Data Grafik Rata-rata Harga Pasar Barang Bekas per Kategori
        $priceAverages = DB::table('listings')
            ->join('categories', 'listings.category_id', '=', 'categories.id')
            ->whereNull('listings.deleted_at')
            ->whereIn('listings.status', ['available', 'sold'])
            ->select('categories.name as category_name', DB::raw('ROUND(AVG(listings.price)) as avg_price'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('avg_price')
            ->take(6)
            ->get();

        $avgPriceChart = [
            'labels' => $priceAverages->pluck('category_name')->toArray(),
            'data' => $priceAverages->pluck('avg_price')->toArray(),
        ];

        // 4. Data Grafik Tren Finansial Mikro (6 Bulan Terakhir)
        $months = [];
        $boostMonthly = [];
        $auctionMonthly = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $mStart = $monthDate->copy()->startOfMonth();
            $mEnd = $monthDate->copy()->endOfMonth();

            $months[] = $monthKey;
            $boostMonthly[] = (float) BoostPayment::where('status', 'verified')
                ->whereBetween('verified_at', [$mStart, $mEnd])
                ->sum('amount');
            $auctionMonthly[] = (float) AuctionPayment::where('status', 'verified')
                ->whereBetween('verified_at', [$mStart, $mEnd])
                ->sum('amount');
        }

        $revenueTrendChart = [
            'labels' => $months,
            'boost' => $boostMonthly,
            'auction' => $auctionMonthly,
        ];

        // 5. Transaksi Finansial Mikro Terbaru (Gabungan verified payments)
        $recentBoosts = BoostPayment::with(['user', 'listing'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'type' => 'Boost Iklan',
                    'user' => $item->user?->name ?? 'Mahasiswa',
                    'campus' => $item->user?->campus ?? '-',
                    'detail' => $item->listing?->title ?? 'Iklan #' . $item->listing_id,
                    'amount' => $item->amount,
                    'status' => $item->status,
                    'date' => $item->created_at,
                ];
            });

        $recentAuctions = AuctionPayment::with(['user', 'auction.listing'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'type' => 'Biaya Buka Lelang',
                    'user' => $item->user?->name ?? 'Mahasiswa',
                    'campus' => $item->user?->campus ?? '-',
                    'detail' => $item->auction?->listing?->title ?? 'Sesi Lelang #' . $item->auction_id,
                    'amount' => $item->amount,
                    'status' => $item->status,
                    'date' => $item->created_at,
                ];
            });

        $recentPayments = $recentBoosts->concat($recentAuctions)
            ->sortByDesc('date')
            ->take(6);

        // 6. Pengguna Terbaru Terdaftar
        $recentUsers = User::where('role', '!=', 'admin')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'period',
            'totalStudents',
            'pendingKtm',
            'suspendedUsers',
            'activeListings',
            'activeAuctions',
            'totalRevenue',
            'boostRevenue',
            'auctionRevenue',
            'pendingBoostCount',
            'pendingAuctionCount',
            'pendingReportsCount',
            'categoryChart',
            'avgPriceChart',
            'revenueTrendChart',
            'recentPayments',
            'recentUsers'
        ));
    }
}
