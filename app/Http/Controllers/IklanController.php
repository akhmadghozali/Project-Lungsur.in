<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IklanController extends Controller
{
    /**
     * Tampilkan form pasang iklan beserta data kategori dari DB.
     * Khusus untuk Mahasiswa dalam Mode Penjual (Seller).
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check() && !Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Fitur pasang iklan barang dagangan hanya tersedia untuk akun dalam Mode Penjual. Silakan beralih ke Mode Penjual terlebih dahulu.'
            );
        }

        $categories = Category::orderBy('name')->get();

        return view('iklan.create', compact('categories'));
    }

    /**
     * Simpan iklan baru beserta foto ke database.
     *
     * Alur:
     *  1. Validasi input via StoreListingRequest (termasuk custom rule kelipatan 1.000 dan maks 5 foto).
     *  2. Upload foto + INSERT listings + INSERT listings_images dalam satu DB::transaction.
     *  3. Redirect ke halaman utama dengan flash sukses.
     */
    public function store(StoreListingRequest $request): RedirectResponse
    {
        if (!Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Anda sedang dalam Mode Pembeli. Silakan beralih ke Mode Penjual untuk dapat mempublikasikan iklan barang dagangan.'
            );
        }

        $validated = $request->validated();
        $uploadedFilePaths = [];

        // ── SIMPAN ATOMIK (upload foto + DB dalam satu transaction) ─────
        try {
            DB::transaction(function () use ($validated, $request, &$uploadedFilePaths): void {
                $listing = Listing::create([
                    'user_id'             => Auth::id(),  // wajib user asli, tidak ada fallback
                    'category_id'         => $validated['category_id'],
                    'title'               => $validated['title'],
                    'description'         => $validated['description'],
                    'price'               => $validated['price'],
                    // Simpan sebagai array murni — Eloquent cast 'array' menangani JSON encoding
                    'condition_checklist' => ['kondisi' => $validated['condition']],
                    'addresses'           => $validated['addresses'],
                    'status'              => 'available',
                ]);

                $images = $request->file('images', []);
                foreach ($images as $index => $image) {
                    $imagePath = $image->store('listings', 'public');
                    $uploadedFilePaths[] = $imagePath;

                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_url'  => $imagePath,
                        'is_primary' => $index === 0,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            // DB gagal → hapus seluruh foto yang sudah terlanjur diupload
            foreach ($uploadedFilePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan iklan. Silakan coba lagi.');
        }

        // ── REDIRECT SUKSES ─────────────────────────────────────────────
        return redirect('/')
            ->with('success', 'Iklan "' . $validated['title'] . '" berhasil dipasang! Terima kasih. 🎉');
    }

    /**
     * Tampilkan form edit iklan milik penjual.
     */
    public function edit(Listing $listing): View|RedirectResponse
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Anda tidak memiliki hak untuk mengedit iklan ini.');

        if (!Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Silakan beralih ke Mode Penjual untuk mengelola atau mengedit barang dagangan Anda.'
            );
        }

        $categories = Category::orderBy('name')->get();
        $listing->load('images');

        return view('iklan.edit', compact('listing', 'categories'));
    }

    /**
     * Perbarui data iklan milik penjual.
     *
     * Alur:
     *  1. Validasi via StoreListingRequest (gabungan foto lama dan baru maks 5, min 1).
     *  2. Otorisasi kepemilikan iklan.
     *  3. Update data iklan, hapus relasi foto yang dibuang, upload foto baru secara atomik.
     *  4. Pastikan ada foto utama (is_primary = true) otomatis jika foto utama lama dihapus.
     *  5. Hapus fisik foto lama hanya setelah transaksi DB berhasil dan tidak dipakai oleh data lain.
     */
    public function update(StoreListingRequest $request, Listing $listing): RedirectResponse
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Anda tidak memiliki hak untuk memperbarui iklan ini.');

        $validated = $request->validated();
        $newUploadedFilePaths = [];
        $oldFilePathsToDelete = [];

        try {
            DB::transaction(function () use ($validated, $request, $listing, &$newUploadedFilePaths, &$oldFilePathsToDelete): void {
                // 1. Hapus record foto lama yang ditandai untuk dihapus (hanya yang benar milik listing ini)
                $deletedImageIds = array_filter((array) $request->input('deleted_image_ids', []));
                if (! empty($deletedImageIds)) {
                    $imagesToDelete = $listing->images()->whereIn('id', $deletedImageIds)->get();
                    foreach ($imagesToDelete as $img) {
                        $oldFilePathsToDelete[] = $img->image_url;
                        $img->delete();
                    }
                }

                // 2. Upload foto-foto baru jika ada
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $file) {
                        $newPath = $file->store('listings', 'public');
                        $newUploadedFilePaths[] = $newPath;

                        ListingImage::create([
                            'listing_id' => $listing->id,
                            'image_url'  => $newPath,
                            'is_primary' => false,
                        ]);
                    }
                }

                // 3. Pastikan tetap ada satu foto primary
                $hasPrimary = $listing->images()->where('is_primary', true)->exists();
                if (! $hasPrimary) {
                    $firstRemaining = $listing->images()->oldest('id')->first();
                    if ($firstRemaining) {
                        $firstRemaining->update(['is_primary' => true]);
                    }
                }

                // 4. Update detail listing
                $listing->update([
                    'category_id'         => $validated['category_id'],
                    'title'               => $validated['title'],
                    'description'         => $validated['description'],
                    'price'               => $validated['price'],
                    'condition_checklist' => ['kondisi' => $validated['condition']],
                    'addresses'           => $validated['addresses'],
                ]);
            });

            // 5. Transaksi berhasil: Hapus foto lama dari storage disk jika tidak dipakai oleh data lain
            foreach ($oldFilePathsToDelete as $oldPath) {
                $isUsedElsewhere = ListingImage::where('image_url', $oldPath)->exists();
                if (! $isUsedElsewhere && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        } catch (\Throwable $e) {
            // Transaksi gagal: Bersihkan foto baru yang sempat diupload, foto lama tetap aman
            foreach ($newUploadedFilePaths as $newPath) {
                if (Storage::disk('public')->exists($newPath)) {
                    Storage::disk('public')->delete($newPath);
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui iklan. Silakan coba lagi.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Iklan "' . $listing->title . '" berhasil diperbarui! 🎉');
        }
    
        /**
     * Tampilkan halaman detail barang.
     */
    public function show($id): View
    {
        // Mengambil data iklan beserta relasi kategori, gambar, dan user penjualnya
        $listing = Listing::with(['category', 'images', 'user'])->findOrFail($id);
        return view('listings.show', compact('listing'));
    }
}
