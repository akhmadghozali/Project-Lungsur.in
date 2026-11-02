<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\CampusDomain;
use App\Models\Category;
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

        // Parameter filter multi-kriteria
        $kampus = trim((string) $request->query('kampus', ''));
        $kondisi = strtolower(trim((string) $request->query('kondisi', '')));
        $minPrice = $request->query('min');
        $maxPrice = $request->query('max');
        $categoryParam = trim((string) $request->query('category', $request->query('kategori', '')));

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

        $query = Listing::available()->with(['category', 'images', 'user']);

        $isBoostedOnly = false;

        if ($hasFilter) {
            // ── 1. Filter Pencarian Teks (Judul / Deskripsi / Kategori) ──
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'ilike', "%{$search}%")
                      ->orWhere('description', 'ilike', "%{$search}%")
                      ->orWhereHas('category', function ($catQuery) use ($search) {
                          $catQuery->where('name', 'ilike', "%{$search}%");
                      });
                });
            }

            // ── 2. Filter Kampus (Asal Penjual / Lokasi) ──
            if ($kampus !== '') {
                // Pemetaan alias kampus perguruan tinggi di Malang
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

            // ── 3. Filter Kondisi Barang (Baru vs Bekas / Varian Bekas) ──
            if ($kondisi !== '') {
                if ($kondisi === 'bekas') {
                    $query->where('condition_checklist', 'ilike', '%bekas%');
                } elseif ($kondisi === 'baru') {
                    $query->where('condition_checklist', 'ilike', '%baru%');
                } else {
                    $query->where('condition_checklist', 'ilike', "%{$kondisi}%");
                }
            }

            // ── 4. Filter Rentang Harga (Min & Max) ──
            if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
                $query->where('price', '>=', (float) $minPrice);
            }
            if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
                $query->where('price', '<=', (float) $maxPrice);
            }

            // ── 5. Filter Kategori ──
            if ($categoryParam !== '') {
                $query->whereHas('category', function ($catQuery) use ($categoryParam) {
                    $catQuery->where('slug', 'ilike', $categoryParam)
                             ->orWhere('name', 'ilike', "%{$categoryParam}%");
                });
            }

            // Pada hasil filter, prioritaskan iklan yang di-boost di posisi teratas
            $query->orderByDesc('is_boosted');
        } else {
            // Mode Beranda Normal (Tanpa Filter): Cek apakah ada iklan yang aktif di-boost
            $hasBoosted = Listing::available()->boosted()->exists();

            if ($hasBoosted) {
                // Tampilkan HANYA iklan yang di-boost saja sesuai instruksi
                $query->boosted();
                $isBoostedOnly = true;
            } else {
                // Jika tidak ada iklan boost, fallback ke katalog normal
                $isBoostedOnly = false;
            }
        }

        // Filter pengurutan (Sorting)
        if ($sort === 'lowest') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('price', 'desc');
        } else {
            $sort = 'latest';
            if (!$isBoostedOnly && !$hasFilter) {
                // Fallback ke random jika tidak ada iklan boost dan tidak sedang memfilter
                $query->inRandomOrder();
            } else {
                $query->latest();
            }
        }

        if (auth()->check()) {
            // User login: 12 barang per halaman
            $listings = $query->paginate(12)->withQueryString();
        } else {
            // Guest: maksimal 10 barang (atau iklan boost)
            $listings = $query->limit(10)->get();
        }

        $allCategories = Category::orderBy('name')->get();
        $campusList = CampusDomain::where('is_active', true)->orderBy('campus_name')->get();

        $filters = [
            'q' => $search,
            'kampus' => $kampus,
            'kondisi' => $kondisi,
            'min' => $minPrice,
            'max' => $maxPrice,
            'category' => $categoryParam,
        ];

        return view('welcome', compact(
            'listings',
            'sort',
            'search',
            'isBoostedOnly',
            'spotlight',
            'filters',
            'allCategories',
            'campusList',
            'hasFilter'
        ));
    }
}