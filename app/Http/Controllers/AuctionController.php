<?php

namespace App\Http\Controllers;

use App\Events\BidPlaced;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuctionController extends Controller
{
    /**
     * Mendapatkan daftar lelang (default: lelang aktif).
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'active');
        $query = Auction::with(['listing.user', 'listing.category', 'highestBid.user'])
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('end_time', 'asc');

        $auctions = $query->paginate($request->query('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $auctions->items(),
            'meta' => [
                'current_page' => $auctions->currentPage(),
                'last_page' => $auctions->lastPage(),
                'total' => $auctions->total(),
            ],
        ]);
    }

    /**
     * Detail lelang lengkap beserta status hitung mundur dan penawar tertinggi.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $auction = Auction::with([
            'listing.user:id,name,campus,phone',
            'listing.category:id,name,slug',
            'winner:id,name,campus',
            'bids.user:id,name,campus',
        ])->find($id);

        if (!$auction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lelang dengan ID tersebut tidak ditemukan.',
            ], 404);
        }

        // Cek jika lelang sudah melewati batas waktu tapi status masih aktif -> tutup otomatis
        if ($auction->status === 'active' && $auction->isExpired()) {
            $highestBid = $auction->highestBid;
            $auction->update([
                'status' => 'closed',
                'winner_id' => $highestBid?->user_id,
            ]);
            $this->markListingBookedIfWinner($auction, $highestBid);
            $auction->refresh();
        }

        $highestBid = $auction->highestBid;

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $auction->id,
                'status' => $auction->status,
                'starting_price' => (float) $auction->starting_price,
                'bid_increment' => (float) $auction->bid_increment,
                'bid_cap' => (float) $auction->bid_cap,
                'current_highest_bid' => $highestBid ? (float) $highestBid->bid_amount : null,
                'current_price' => $auction->currentPrice(),
                'min_next_bid' => $auction->minNextBid(),
                'start_time' => $auction->start_time?->toISOString(),
                'end_time' => $auction->end_time?->toISOString(),
                'is_expired' => $auction->isExpired(),
                'time_remaining_seconds' => $auction->timeRemainingSeconds(),
                'listing' => [
                    'id' => $auction->listing->id,
                    'title' => $auction->listing->title,
                    'description' => $auction->listing->description,
                    'condition_checklist' => $auction->listing->condition_checklist,
                    'seller' => [
                        'id' => $auction->listing->user->id,
                        'name' => $auction->listing->user->name,
                        'campus' => $auction->listing->user->campus,
                    ],
                    'category' => $auction->listing->category ? [
                        'id' => $auction->listing->category->id,
                        'name' => $auction->listing->category->name,
                    ] : null,
                ],
                'leader' => $highestBid ? [
                    'id' => $highestBid->user->id,
                    'name' => $highestBid->user->name,
                    'campus' => $highestBid->user->campus,
                    'bid_amount' => (float) $highestBid->bid_amount,
                    'created_at' => $highestBid->created_at->toISOString(),
                ] : null,
                'winner' => $auction->winner ? [
                    'id' => $auction->winner->id,
                    'name' => $auction->winner->name,
                    'campus' => $auction->winner->campus,
                ] : null,
                'total_bids' => $auction->bids->count(),
                'bid_history' => $auction->bids->map(function ($bid) {
                    return [
                        'id' => $bid->id,
                        'bid_amount' => (float) $bid->bid_amount,
                        'bidder' => [
                            'id' => $bid->user->id,
                            'name' => $bid->user->name,
                            'campus' => $bid->user->campus,
                        ],
                        'created_at' => $bid->created_at->toISOString(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Mengajukan tawaran (Bid) ke dalam lelang.
     * Algoritma:
     * 1. Verifikasi akun aktif dan bukan penjual.
     * 2. Pessimistic lock lelang.
     * 3. Validasi status aktif & hitung mundur waktu lelang.
     * 4. Validasi bukan penawar tertinggi saat ini (mencegah outbid diri sendiri).
     * 5. Validasi batas maksimal (Bid Cap) -> jika pas bid_cap, auto-win lelang ditutup.
     * 6. Validasi kelipatan (Bid Increment Modulo).
     * 7. Anti-Sniping: jika bid masuk pada sisa waktu <= 5 menit, perpanjang +5 menit.
     * 8. Simpan penawaran dan siarkan event Pusher BidPlaced.
     */
    public function placeBid(Request $request, $id): JsonResponse
    {
        // 1. Identifikasi pengguna yang menawar
        $user = $request->user();
        if (!$user && (app()->environment('local', 'testing')) && $request->hasHeader('X-User-Id')) {
            $user = User::find($request->header('X-User-Id'));
        }

        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan masuk (login) terlebih dahulu untuk dapat mengajukan penawaran.',
            ], 401);
        }

        if ($user->verification_status !== 'active') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Hanya akun mahasiswa yang telah terverifikasi aktif yang dapat mengikuti lelang.',
            ], 403);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Akun administrator tidak diperbolehkan mengajukan penawaran lelang.',
            ], 403);
        }

        // 2. Validasi input nominal penawaran
        $validator = Validator::make($request->all(), [
            'bid_amount' => ['required', 'numeric', 'min:1000'],
        ], [
            'bid_amount.required' => 'Nominal tawaran wajib diisi.',
            'bid_amount.numeric' => 'Nominal tawaran harus berupa angka numerik.',
            'bid_amount.min' => 'Nominal tawaran minimal adalah Rp 1.000.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $bidAmount = (float) $request->input('bid_amount');

        // 3. Eksekusi dalam DB Transaction dengan Pessimistic Locking
        return DB::transaction(function () use ($id, $user, $bidAmount) {
            /** @var Auction $auction */
            $auction = Auction::lockForUpdate()
                ->with(['listing.user', 'highestBid.user'])
                ->find($id);

            if (!$auction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lelang dengan ID tersebut tidak ditemukan.',
                ], 404);
            }

            // A. Penjual tidak boleh menawar barangnya sendiri
            if ($auction->listing->user_id === $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Penjual tidak diperbolehkan mengajukan penawaran pada lelang barang miliknya sendiri.',
                ], 403);
            }

            // B. Validasi status lelang
            if ($auction->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Lelang saat ini tidak sedang dibuka untuk penawaran (Status: {$auction->status}).",
                ], 422);
            }

            // C. Validasi waktu mulai
            if ($auction->start_time && now()->lt($auction->start_time)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lelang belum dimulai. Waktu mulai: ' . $auction->start_time->format('d/m/Y H:i') . ' WIB.',
                ], 422);
            }

            // D. Validasi batas waktu hitung mundur (Countdown)
            if ($auction->end_time && now()->gte($auction->end_time)) {
                // Tutup otomatis lelang
                $highestBid = $auction->highestBid;
                $auction->update([
                    'status' => 'closed',
                    'winner_id' => $highestBid?->user_id,
                ]);

                $this->markListingBookedIfWinner($auction, $highestBid);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Waktu lelang telah berakhir pada ' . $auction->end_time->format('d/m/Y H:i') . ' WIB. Penawaran baru tidak diterima.',
                ], 422);
            }

            $highestBid = $auction->highestBid;

            // E. Validasi penawar tertinggi saat ini (mencegah outbid diri sendiri)
            if ($highestBid && $highestBid->user_id === $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda saat ini sudah memegang penawaran tertinggi senilai Rp ' . number_format($highestBid->bid_amount, 0, ',', '.') . '. Tunggu penawaran dari peserta lain sebelum menawar kembali.',
                ], 422);
            }

            // F. Validasi Batas Maksimal Tawaran (Bid Cap)
            if ($auction->bid_cap > 0 && $bidAmount > $auction->bid_cap) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tawaran Rp ' . number_format($bidAmount, 0, ',', '.') . ' melebihi batas maksimal penawaran / buy-out (Rp ' . number_format($auction->bid_cap, 0, ',', '.') . ').',
                ], 422);
            }

            // G. Algoritma Kelipatan Tawaran (Bid Increment Modulo)
            $increment = (int) round($auction->bid_increment);

            if (!$highestBid) {
                // Belum ada penawaran sama sekali: tawaran awal harus >= starting_price
                if ($bidAmount < $auction->starting_price) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tawaran minimal adalah harga awal lelang: Rp ' . number_format($auction->starting_price, 0, ',', '.') . '.',
                    ], 422);
                }

                $diff = (int) round($bidAmount - $auction->starting_price);
                if ($diff % $increment !== 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tawaran harus sesuai kelipatan bid increment (Rp ' . number_format($auction->bid_increment, 0, ',', '.') . ') dari harga awal.',
                    ], 422);
                }
            } else {
                // Sudah ada penawaran sebelumnya: tawaran baru harus > current_highest_bid
                if ($bidAmount <= $highestBid->bid_amount) {
                    $minRequired = $highestBid->bid_amount + $auction->bid_increment;
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tawaran Anda harus lebih tinggi dari penawaran tertinggi saat ini (Rp ' . number_format($highestBid->bid_amount, 0, ',', '.') . '). Minimal tawaran berikutnya: Rp ' . number_format($minRequired, 0, ',', '.') . '.',
                    ], 422);
                }

                $diff = (int) round($bidAmount - $highestBid->bid_amount);
                if ($diff % $increment !== 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tawaran harus merupakan kelipatan tepat dari bid increment (Rp ' . number_format($auction->bid_increment, 0, ',', '.') . ') di atas penawaran tertinggi saat ini.',
                    ], 422);
                }
            }

            // H. Algoritma Anti-Sniping:
            // Jika penawaran diajukan di 5 menit terakhir sebelum lelang ditutup,
            // perpanjang waktu lelang 5 menit agar penawar lain punya waktu untuk membalas.
            $isExtended = false;
            $secondsRemaining = $auction->end_time ? now()->diffInSeconds($auction->end_time, false) : 0;
            if ($secondsRemaining > 0 && $secondsRemaining <= 300) {
                $auction->end_time = now()->addMinutes(5);
                $isExtended = true;
            }

            // I. Logika Auto-Win jika mencapai Bid Cap
            $isClosed = false;
            if ($auction->bid_cap > 0 && $bidAmount >= $auction->bid_cap) {
                $auction->status = 'closed';
                $auction->winner_id = $user->id;
                $auction->end_time = now();
                $isClosed = true;

                // Update status listing jika ada
                if ($auction->listing) {
                    $auction->listing->update(['status' => 'booked']);
                }
            }

            // J. Simpan Penawaran Baru
            $bid = AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'bid_amount' => $bidAmount,
            ]);

            $auction->save();

            // Load user data untuk payload event
            $bid->load('user:id,name,campus');

            // K. Siarkan Event Pusher Realtime (BidPlaced)
            try {
                broadcast(new BidPlaced($auction, $bid, $isExtended, $isClosed))->toOthers();
            } catch (\Throwable $e) {
                report($e);
            }

            $successMsg = $isClosed
                ? 'Selamat! Tawaran Anda mencapai batas maksimal (Bid Cap). Anda memenangkan lelang ini secara instan!'
                : 'Tawaran sebesar Rp ' . number_format($bidAmount, 0, ',', '.') . ' berhasil diajukan.';

            if ($isExtended) {
                $successMsg .= ' Waktu lelang diperpanjang 5 menit (Anti-Sniping).';
            }

            return response()->json([
                'status' => 'success',
                'message' => $successMsg,
                'data' => [
                    'bid_id' => $bid->id,
                    'auction_id' => $auction->id,
                    'bid_amount' => (float) $bid->bid_amount,
                    'bidder' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'campus' => $user->campus,
                    ],
                    'highest_bid' => (float) $bid->bid_amount,
                    'min_next_bid' => (float) ($bid->bid_amount + $auction->bid_increment),
                    'end_time' => $auction->end_time?->toISOString(),
                    'is_extended' => $isExtended,
                    'is_closed' => $isClosed,
                    'winner_id' => $auction->winner_id,
                    'time_remaining_seconds' => $auction->timeRemainingSeconds(),
                ],
            ], 201);
        });
    }

    /**
     * Menutup lelang secara manual (misal oleh pemilik barang atau saat waktu lelang habis).
     */
    public function close(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user && (app()->environment('local', 'testing')) && $request->hasHeader('X-User-Id')) {
            $user = User::find($request->header('X-User-Id'));
        }

        $auction = Auction::with(['listing', 'highestBid'])->findOrFail($id);

        // Hanya pemilik barang atau admin yang dapat menutup lelang manual
        if ($user && $user->id !== $auction->listing->user_id && $user->role !== 'admin' && !$auction->isExpired()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk menutup lelang ini.',
            ], 403);
        }

        $highestBid = $auction->highestBid;
        $auction->update([
            'status' => 'closed',
            'winner_id' => $highestBid?->user_id,
            'end_time' => now(),
        ]);

        $this->markListingBookedIfWinner($auction, $highestBid);

        return response()->json([
            'status' => 'success',
            'message' => 'Lelang berhasil ditutup.',
            'data' => [
                'auction_id' => $auction->id,
                'status' => 'closed',
                'winner_id' => $auction->winner_id,
                'highest_bid' => $highestBid ? (float) $highestBid->bid_amount : null,
            ],
        ]);
    }
        /**
     * Halaman web daftar lelang.
     */
    
/**
 * Halaman web daftar lelang.
 */
    public function webIndex(Request $request): View
    {
        // Desktop: 20 lelang per halaman. HP: 10 lelang per halaman.
        $perPage = (int) $request->query('per_page', 20);

        if (! in_array($perPage, [10, 20], true)) {
            $perPage = 20;
        }

        // Query dasar untuk daftar lelang.
        $baseQuery = Auction::with([
            'listing.category',
            'listing.images',
            'highestBid.user',
        ])
            ->whereIn('auctions.status', ['active', 'closed'])
            ->whereHas('listing');

        // Hitung jumlah pengguna berbeda yang pernah menawar.
        $uniqueBiddersQuery = AuctionBid::query()
            ->selectRaw('COUNT(DISTINCT user_id)')
            ->whereColumn('auction_id', 'auctions.id');

        // Spotlight: lelang aktif yang sudah memiliki penawar,
        // diurutkan berdasarkan jumlah pengguna yang menawar.
        $spotlight = (clone $baseQuery)
            ->where('auctions.status', 'active')
            ->whereNotNull('auctions.end_time')
            ->where('auctions.end_time', '>', now())
            ->whereHas('bids')
            ->select('auctions.*')
            ->selectSub($uniqueBiddersQuery, 'unique_bidders_count')
            ->orderByDesc('unique_bidders_count')
            ->orderByDesc('auctions.created_at')
            ->first();

        // Daftar lelang lainnya tidak mengulang kartu Spotlight.
        // Urutkan berdasarkan waktu mulai paling awal.
        $auctions = (clone $baseQuery)
            ->when($spotlight, function ($query) use ($spotlight) {
                $query->where('auctions.id', '!=', $spotlight->id);
            })
            ->orderByRaw(
                'CASE WHEN auctions.start_time IS NULL THEN 1 ELSE 0 END'
            )
            ->orderBy('auctions.start_time', 'asc')
            ->orderByDesc('auctions.created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('auctions.index', compact(
            'auctions',
            'spotlight'
        ));
    }

    /**
     * Halaman web detail lelang.
     */
    public function webShow(int $id): View
    {
        $auction = Auction::with([
            'listing.category',
            'listing.images',
            'listing.user',
            'bids.user',
            'highestBid.user',
            'winner',
        ])->findOrFail($id);

        // Tutup lelang jika waktunya sudah habis.
        if (
            $auction->status === 'active' &&
            $auction->isExpired()
        ) {
            $this->finalizeExpiredAuction($auction->id);

            $auction->refresh()->load([
                'listing.category',
                'listing.images',
                'listing.user',
                'bids.user',
                'highestBid.user',
                'winner',
            ]);
        }

        abort_unless($auction->listing, 404);

        return view('auctions.show', compact('auction'));
    }

    /**
     * Sinkronisasi penutupan lelang ketika countdown habis.
     */
    public function expire(int $id): JsonResponse
    {
        $result = DB::transaction(function () use ($id): array {
            $auction = Auction::query()
                ->lockForUpdate()
                ->findOrFail($id);

            // Jangan tutup jika lelang tidak aktif atau waktunya belum habis.
            if ($auction->status !== 'active') {
                return [
                    'status' => $auction->status,
                    'message' => 'Status lelang sudah diperbarui.',
                ];
            }

            if (
                ! $auction->end_time ||
                now()->lt($auction->end_time)
            ) {
                return [
                    'status' => $auction->status,
                    'message' => 'Waktu lelang belum berakhir.',
                ];
            }

            $highestBid = AuctionBid::query()
                ->where('auction_id', $auction->id)
                ->orderByDesc('bid_amount')
                ->orderBy('created_at')
                ->first();

            $auction->update([
                'status' => 'closed',
                'winner_id' => $highestBid?->user_id,
            ]);

            $this->markListingBookedIfWinner($auction, $highestBid);

            return [
                'status' => 'closed',
                'winner_id' => $highestBid?->user_id,
                'message' => 'Lelang berhasil ditutup.',
            ];
        });

        if ($result['status'] !== 'closed') {
            return response()->json($result, 409);
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    /**
     * Menutup lelang kedaluwarsa dan menentukan penawar tertinggi.
     */
    private function finalizeExpiredAuction(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $auction = Auction::query()
                ->lockForUpdate()
                ->find($id);

            if (
                ! $auction ||
                $auction->status !== 'active' ||
                ! $auction->end_time ||
                now()->lt($auction->end_time)
            ) {
                return;
            }

            $highestBid = AuctionBid::query()
                ->where('auction_id', $auction->id)
                ->orderByDesc('bid_amount')
                ->orderBy('created_at')
                ->first();

            $auction->update([
                'status' => 'closed',
                'winner_id' => $highestBid?->user_id,
            ]);
            $this->markListingBookedIfWinner($auction, $highestBid);
        });
    }
        /**
     * Mengubah status barang menjadi booked jika lelang
     * berakhir dan memiliki pemenang.
     */
    private function markListingBookedIfWinner(
        Auction $auction,
        ?AuctionBid $highestBid
    ): void {
        if (!$highestBid) {
            return;
        }

        $listing = $auction->listing;

        if ($listing) {
            $listing->update(['status' => 'booked']);
        }
    }
}
