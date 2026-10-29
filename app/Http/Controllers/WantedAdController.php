<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\WantedAd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WantedAdController extends Controller
{
    //Menampilkan papan informasi barang yang sedang dicari mahasiswa (Wanted Ads).
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categoryParam = trim((string) $request->query('category', $request->query('kategori', '')));

        $query = WantedAd::active()->with(['user', 'category'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('addresses', 'ilike', "%{$search}%");
            });
        }

        if ($categoryParam !== '') {
            $query->whereHas('category', function ($catQuery) use ($categoryParam) {
                $catQuery->where('slug', 'ilike', $categoryParam)
                         ->orWhere('name', 'ilike', "%{$categoryParam}%");
            });
        }

        $wantedAds = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Ambil daftar barang dagangan aktif milik user (jika sedang login sebagai penjual)
        $myActiveListings = Auth::check()
            ? Auth::user()->listings()->where('status', 'available')->with(['category', 'images'])->latest()->get()
            : collect();

        return view('wanted_ads.index', compact(
            'wantedAds',
            'categories',
            'search',
            'categoryParam',
            'myActiveListings'
        ));
    }

    /**
     * Mempublikasikan permintaan barang kebutuhan kos baru.
     * Khusus untuk Mahasiswa dalam Mode Pembeli (Buyer).
     */
    public function store(Request $request): RedirectResponse
    {
        // ── 1. VALIDASI PERAN: Penjual dilarang memasang iklan kebutuhan ──
        if (Auth::user()->isSeller()) {
            return back()->withInput()->with(
                'warning',
                'Papan Wanted Ads khusus diperuntukkan bagi pembeli yang mencari barang. Akun dalam Mode Penjual tidak dapat memasang kebutuhan. Silakan beralih ke Mode Pembeli di menu profil atas jika Anda membutuhkan barang kos.'
            );
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'budget_max' => [
                'required',
                'numeric',
                'min:1000',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ((int) $value % 1000 !== 0) {
                        $fail('Batas anggaran harus dalam kelipatan bulat Rp1.000 (contoh: Rp50.000, Rp100.000).');
                    }
                },
            ],
            'addresses' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
        ], [
            'title.required' => 'Nama barang yang dicari wajib diisi.',
            'title.max' => 'Nama barang maksimal 200 karakter.',
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'budget_max.required' => 'Budget maksimal wajib diisi.',
            'budget_max.numeric' => 'Budget harus berupa angka.',
            'budget_max.min' => 'Budget minimal adalah Rp1.000.',
            'addresses.required' => 'Area / lokasi kos tempat serah terima wajib diisi.',
            'description.required' => 'Deskripsi kriteria atau spesifikasi barang wajib diisi.',
            'description.min' => 'Deskripsi minimal 10 karakter agar penjual mudah memahami.',
        ]);

        WantedAd::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget_max' => $validated['budget_max'],
            'addresses' => $validated['addresses'],
            'status' => 'active',
        ]);

        return redirect()->route('wanted-ads.index')
            ->with('success', 'Permintaan kebutuhan "' . $validated['title'] . '" berhasil dipublikasikan ke papan Wanted Ads! 🎉');
    }

    /**
     * Menghapus postingan kebutuhan barang milik sendiri.
     */
    public function destroy(WantedAd $wantedAd): RedirectResponse
    {
        if (Auth::id() !== (int) $wantedAd->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak untuk menghapus postingan ini.');
        }

        $wantedAd->delete();

        return redirect()->route('wanted-ads.index')
            ->with('success', 'Postingan kebutuhan berhasil dihapus.');
    }

    /**
     * Mengajukan penawaran barang dagangan penjual ke pembeli Wanted Ad.
     * Otomatis mendeteksi postingan barang penjual dan menyambungkannya ke ruang chat internal (Conversation).
     */
    public function offerItem(Request $request, WantedAd $wantedAd): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isSeller()) {
            return back()->with('error', 'Hanya akun dalam Mode Penjual yang dapat menawarkan barang dagangan.');
        }

        if ((int) $wantedAd->user_id === (int) $user->id) {
            return back()->with('error', 'Anda tidak dapat menawarkan barang pada postingan kebutuhan Anda sendiri.');
        }

        $validated = $request->validate([
            'listing_id' => ['required', 'integer', 'exists:listings,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'listing_id.required' => 'Silakan pilih salah satu barang dagangan Anda yang ingin ditawarkan.',
            'listing_id.exists' => 'Barang dagangan yang dipilih tidak valid atau tidak ditemukan.',
            'note.max' => 'Catatan penawaran maksimal 1000 karakter.',
        ]);

        // Pastikan listing berstatus available dan dimiliki oleh penjual yang sedang login
        $listing = $user->listings()
            ->where('status', 'available')
            ->findOrFail($validated['listing_id']);

        // Sambungkan ke percakapan chat internal (Conversation) antara pembeli wanted ad dan penjual
        $conversation = Conversation::firstOrCreate([
            'listing_id' => $listing->id,
            'buyer_id'   => $wantedAd->user_id,
            'seller_id'  => $user->id,
        ], [
            'last_message_at' => now(),
        ]);

        // Susun pesan pembuka penawaran barang otomatis
        $buyerName = $wantedAd->user->name ?? 'Kak';
        $messageText = "Halo kak {$buyerName}! Saya melihat kebutuhan Anda di papan Wanted Ads untuk '{$wantedAd->title}'.\n"
                     . "Saya menawarkan produk dagangan saya yang cocok:\n"
                     . "📦 {$listing->title}\n"
                     . "💰 Harga: Rp " . number_format($listing->price, 0, ',', '.') . "\n"
                     . "🔗 Tautan Produk: " . route('listings.show', $listing->id);

        if (!empty($validated['note'])) {
            $messageText .= "\n\nCatatan Tambahan: " . trim($validated['note']);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message_text'    => $messageText,
            'is_read'         => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('wanted-ads.index')->with(
            'success',
            "Tawaran postingan '{$listing->title}' berhasil disambungkan ke ruang chat internal pembeli ({$buyerName})!"
        );
    }
}

