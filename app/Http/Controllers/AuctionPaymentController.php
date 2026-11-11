<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionPayment;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuctionPaymentController extends Controller
{
    /**
     * Biaya tetap pembukaan sesi lelang (Rp 5.000).
     */
    public const OPENING_FEE = 5000.00;

    /**
     * Durasi tetap sesi lelang yang ditentukan oleh sistem (7 hari).
     */
    public const DURATION_DAYS = 7;

    /**
     * Halaman pilih produk milik penjual yang sudah diupload untuk dibuka sesi lelangnya (/lelang/buka).
     */
    public function selectListing(): View|RedirectResponse
    {
        if (! Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Silakan beralih ke Mode Penjual terlebih dahulu untuk membuka sesi lelang.'
            );
        }

        $myListings = Listing::query()
            ->where('user_id', Auth::id())
            ->with(['category', 'images', 'auction.latestPayment'])
            ->orderByRaw("CASE WHEN status = 'available' THEN 0 ELSE 1 END")
            ->latest()
            ->get();

        $eligibleCount = $myListings->filter(function (Listing $item): bool {
            if ($item->status !== 'available') {
                return false;
            }
            if (! $item->auction) {
                return true;
            }
            return ! in_array($item->auction->status, ['active', 'pending_approval', 'closed'], true)
                && ! ($item->auction->latestPayment && $item->auction->latestPayment->isPending());
        })->count();

        $durationDays = self::DURATION_DAYS;
        $openingFee = self::OPENING_FEE;

        return view('auctions.select-listing', compact(
            'myListings',
            'eligibleCount',
            'durationDays',
            'openingFee'
        ));
    }

    /**
     * Tampilkan formulir pembukaan lelang & pembayaran QRIS biaya buka lelang (Rp 5.000).
     */
    public function create(Listing $listing): View|RedirectResponse
    {
        abort_if((int) $listing->user_id !== (int) Auth::id(), 403, 'Anda tidak berhak membuka lelang untuk iklan pengguna lain.');

        if (! Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Silakan beralih ke Mode Penjual terlebih dahulu untuk membuka sesi lelang.'
            );
        }

        if ($blockedResponse = $this->guardListingEligibility($listing)) {
            return $blockedResponse;
        }

        $listing->load(['category', 'images', 'auction.latestPayment']);

        $durationDays = self::DURATION_DAYS;
        $openingFee = self::OPENING_FEE;

        return view('auctions.create', compact(
            'listing',
            'durationDays',
            'openingFee'
        ));
    }

    /**
     * Simpan pengajuan pembukaan lelang beserta bukti pembayaran QRIS (Rp 5.000).
     */
    public function store(Request $request, Listing $listing): RedirectResponse
    {
        abort_if((int) $listing->user_id !== (int) Auth::id(), 403, 'Akses ditolak.');

        if (! Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Anda harus berada dalam Mode Penjual untuk membuka lelang dan mengirim bukti pembayaran.'
            );
        }

        if ($blockedResponse = $this->guardListingEligibility($listing)) {
            return $blockedResponse;
        }

        $moduloThousandRule = function (string $label): \Closure {
            return function (string $attribute, mixed $value, \Closure $fail) use ($label): void {
                $num = (float) $value;
                if ($num <= 0 || fmod($num, 1000.0) !== 0.0) {
                    $fail("{$label} harus berupa kelipatan Rp 1.000 (contoh: Rp 5.000, Rp 50.000).");
                }
            };
        };

        $validated = $request->validate([
            'starting_price' => [
                'required',
                'numeric',
                'min:1000',
                'max:100000000',
                $moduloThousandRule('Harga awal lelang'),
            ],
            'bid_increment' => [
                'required',
                'numeric',
                'min:1000',
                'max:50000000',
                $moduloThousandRule('Kelipatan tawaran'),
            ],
            'bid_cap' => [
                'required',
                'numeric',
                'min:2000',
                'max:500000000',
                $moduloThousandRule('Harga Buy-Out'),
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $cap = (float) $value;
                    $start = (float) $request->input('starting_price', 0);
                    if ($cap <= $start) {
                        $fail('Harga Buy-Out harus lebih besar dari harga awal lelang.');
                    }
                },
            ],
            'payment_channel' => ['required', 'string', 'in:qris'],
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'starting_price.required' => 'Harga awal lelang wajib diisi.',
            'starting_price.numeric' => 'Harga awal lelang harus berupa angka.',
            'starting_price.min' => 'Harga awal lelang minimal Rp 1.000.',
            'bid_increment.required' => 'Kelipatan tawaran wajib diisi.',
            'bid_increment.numeric' => 'Kelipatan tawaran harus berupa angka.',
            'bid_increment.min' => 'Kelipatan tawaran minimal Rp 1.000.',
            'bid_cap.required' => 'Harga Buy-Out (batas maksimal tawaran) wajib diisi.',
            'bid_cap.numeric' => 'Harga Buy-Out harus berupa angka.',
            'bid_cap.min' => 'Harga Buy-Out minimal Rp 2.000.',
            'payment_channel.required' => 'Metode pembayaran wajib diisi.',
            'payment_channel.in' => 'Pembayaran hanya mendukung QRIS Statis.',
            'proof_image.required' => 'Unggah foto struk atau tangkapan layar bukti pembayaran QRIS Anda.',
            'proof_image.image' => 'Berkas bukti pembayaran harus berupa gambar valid.',
            'proof_image.mimes' => 'Format gambar yang diperbolehkan: JPG, JPEG, PNG, atau WEBP.',
            'proof_image.max' => 'Ukuran berkas gambar maksimal 2MB.',
        ]);

        $proofPath = $request->file('proof_image')->store('auction_proofs', 'public');
        $invoiceCode = 'AUC-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        try {
            DB::transaction(function () use ($listing, $validated, $proofPath, $invoiceCode): void {
                $auction = Auction::updateOrCreate(
                    ['listing_id' => $listing->id],
                    [
                        'starting_price' => (float) $validated['starting_price'],
                        'bid_increment' => (float) $validated['bid_increment'],
                        'bid_cap' => (float) $validated['bid_cap'],
                        'start_time' => now(),
                        'end_time' => now()->addDays(self::DURATION_DAYS),
                        'status' => 'pending_approval',
                        'winner_id' => null,
                    ]
                );

                AuctionPayment::create([
                    'auction_id' => $auction->id,
                    'user_id' => Auth::id(),
                    'invoice_code' => $invoiceCode,
                    'amount' => self::OPENING_FEE,
                    'payment_channel' => $validated['payment_channel'],
                    'proof_image' => $proofPath,
                    'status' => 'pending',
                ]);
            });
        } catch (\Throwable $e) {
            if (Storage::disk('public')->exists($proofPath)) {
                Storage::disk('public')->delete($proofPath);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memproses pengajuan lelang. Silakan coba lagi.');
        }

        return redirect()->route('dashboard')->with(
            'success',
            "Pengajuan buka lelang 7 hari untuk \"{$listing->title}\" (Invoice: {$invoiceCode}) berhasil dikirim! Admin akan segera memverifikasi pembayaran QRIS Rp5.000 Anda."
        );
    }

    /**
     * Pastikan barang memenuhi syarat untuk diajukan pembukaan lelang.
     */
    private function guardListingEligibility(Listing $listing): ?RedirectResponse
    {
        if ($listing->status !== 'available') {
            return redirect()->route('dashboard')->with(
                'warning',
                "Barang \"{$listing->title}\" saat ini berstatus \"{$listing->status}\" sehingga tidak dapat diajukan sebagai lelang baru."
            );
        }

        $existingAuction = $listing->auction()->with('latestPayment')->first();
        if (! $existingAuction) {
            return null;
        }

        if ($existingAuction->status === 'active') {
            return redirect()->route('auctions.show', $existingAuction->id)->with(
                'info',
                "Sesi lelang untuk \"{$listing->title}\" saat ini sedang berlangsung."
            );
        }

        if ($existingAuction->status === 'pending_approval' || ($existingAuction->latestPayment && $existingAuction->latestPayment->isPending())) {
            $invoice = $existingAuction->latestPayment?->invoice_code ?? '-';
            return redirect()->route('dashboard')->with(
                'warning',
                "Pembukaan lelang untuk \"{$listing->title}\" (Invoice: {$invoice}) sedang menunggu verifikasi Admin."
            );
        }

        if ($existingAuction->status === 'closed') {
            return redirect()->route('auctions.show', $existingAuction->id)->with(
                'info',
                "Sesi lelang untuk \"{$listing->title}\" telah selesai."
            );
        }

        return null;
    }
}
