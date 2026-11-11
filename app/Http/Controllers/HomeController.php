<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\CampusDomain;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.verifications.index');
        }

        $isCatalog = $request->routeIs('listings.index') || $request->is('katalog*');

        $search = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', 'latest');
        $kampus = trim((string) $request->query('kampus', ''));
        $kondisi = strtolower(trim((string) $request->query('kondisi', '')));
        $minPrice = $request->query('min');
        $maxPrice = $request->query('max');
        $categoryParam = trim((string) $request->query('category', $request->query('kategori', '')));

        $filters = [
            'q' => $search,
            'kampus' => $kampus,
            'kondisi' => $kondisi,
            'min' => $minPrice,
            'max' => $maxPrice,
            'category' => $categoryParam,
        ];

        $hasFilter = ($search !== '')
            || ($kampus !== '')
            || ($kondisi !== '')
            || ($minPrice !== null && $minPrice !== '')
            || ($maxPrice !== null && $maxPrice !== '')
            || ($categoryParam !== '');

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

        if ($isCatalog) {
            // Halaman Katalog: pisahkan kotak Boost (atas) dan kotak produk non-boost (bawah)
            $boostedQuery = Listing::available()->boosted()->with(['category', 'images', 'user']);
            $this->applyFilters($boostedQuery, $filters);
            $this->applySorting($boostedQuery, $sort);
            $boostedListings = $boostedQuery->get();

            $regularQuery = Listing::available()->notBoosted()->with(['category', 'images', 'user']);
            $this->applyFilters($regularQuery, $filters);
            $sort = $this->applySorting($regularQuery, $sort);
            $listings = auth()->check()
                ? $regularQuery->paginate(12)->withQueryString()
                : $regularQuery->limit(10)->get();

            $hasBoosted = $boostedListings->isNotEmpty();
            $isBoostedOnly = false;
        } else {
            // Halaman Beranda: hanya tampilkan produk yang sedang aktif di-boost
            $boostedQuery = Listing::available()->boosted()->with(['category', 'images', 'user']);
            $this->applyFilters($boostedQuery, $filters);
            $sort = $this->applySorting($boostedQuery, $sort);
            $listings = auth()->check()
                ? $boostedQuery->paginate(12)->withQueryString()
                : $boostedQuery->limit(10)->get();

            $boostedListings = collect();
            $hasBoosted = ($listings instanceof LengthAwarePaginator ? $listings->total() : $listings->count()) > 0;
            $isBoostedOnly = true;
        }

        $allCategories = Category::orderBy('name')->get();
        $campusList = CampusDomain::where('is_active', true)->orderBy('campus_name')->get();

        return view('welcome', compact(
            'listings',
            'boostedListings',
            'isCatalog',
            'sort',
            'search',
            'hasBoosted',
            'isBoostedOnly',
            'spotlight',
            'filters',
            'allCategories',
            'campusList',
            'hasFilter'
        ));
    }

    /**
     * Terapkan filter pencarian & multi-kriteria pada query listing.
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $search = $filters['q'] ?? '';
        $kampus = $filters['kampus'] ?? '';
        $kondisi = $filters['kondisi'] ?? '';
        $minPrice = $filters['min'] ?? null;
        $maxPrice = $filters['max'] ?? null;
        $categoryParam = $filters['category'] ?? '';

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhereHas('category', function ($catQuery) use ($search) {
                      $catQuery->where('name', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($kampus !== '') {
            $campusAliases = [
                'ub' => ['Universitas Brawijaya', 'UB', 'Brawijaya'],
                'polinema' => ['Politeknik Negeri Malang', 'Polinema'],
                'um' => ['Universitas Negeri Malang', 'UM'],
                'uin' => ['UIN Maulana Malik Ibrahim Malang', 'UIN Malang', 'UIN'],
                'umm' => ['Universitas Muhammadiyah Malang', 'UMM'],
                'itn' => ['Institut Teknologi Nasional Malang', 'ITN Malang', 'ITN'],
            ];

            $lowerKampus = strtolower($kampus);
            $aliases = $campusAliases[$lowerKampus] ?? [$kampus];

            $query->whereHas('user', function ($userQuery) use ($aliases) {
                $userQuery->where(function ($uq) use ($aliases) {
                    foreach ($aliases as $alias) {
                        $uq->orWhere('campus', 'ilike', "%{$alias}%");
                    }
                });
            });
        }

        if ($kondisi !== '') {
            if ($kondisi === 'bekas') {
                $query->where('condition_checklist', 'ilike', '%bekas%');
            } elseif ($kondisi === 'baru') {
                $query->where('condition_checklist', 'ilike', '%baru%');
            } else {
                $query->where('condition_checklist', 'ilike', "%{$kondisi}%");
            }
        }

        if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        if ($categoryParam !== '') {
            $query->whereHas('category', function ($catQuery) use ($categoryParam) {
                $catQuery->where('slug', 'ilike', $categoryParam)
                         ->orWhere('name', 'ilike', "%{$categoryParam}%");
            });
        }
    }

    /**
     * Terapkan pengurutan (sorting) pada query listing dan kembalikan nilai sort yang valid.
     */
    private function applySorting(Builder $query, string $sort): string
    {
        if ($sort === 'lowest') {
            $query->orderBy('price', 'asc');
            return 'lowest';
        }

        if ($sort === 'highest') {
            $query->orderBy('price', 'desc');
            return 'highest';
        }

        $query->latest();
        return 'latest';
    }
}
