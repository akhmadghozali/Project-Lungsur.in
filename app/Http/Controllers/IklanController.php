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
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('iklan.create', compact('categories'));
    }

    /**
     * Simpan iklan baru beserta foto ke database.
     *
     * Alur:
     *  1. Validasi input via StoreListingRequest (termasuk custom rule kelipatan 1.000).
     *  2. Upload foto + INSERT listings + INSERT listings_images dalam satu DB::transaction.
     *  3. Redirect ke halaman utama dengan flash sukses.
     */
    public function store(StoreListingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // ── SIMPAN ATOMIK (upload foto + DB dalam satu transaction) ─────
        try {
            DB::transaction(function () use ($validated, $request): void {
                // Upload foto di dalam transaction; jika DB gagal → rollback + hapus file
                $imagePath = $request->file('image')->store('listings', 'public');

                try {
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

                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_url'  => $imagePath,
                        'is_primary' => true,
                    ]);
                } catch (\Throwable $dbError) {
                    // DB gagal → hapus foto yang sudah terlanjur diupload
                    Storage::disk('public')->delete($imagePath);
                    throw $dbError;  // re-throw agar transaction rollback
                }
            });
        } catch (\Throwable $e) {
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
    public function edit(Listing $listing): View
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Anda tidak memiliki hak untuk mengedit iklan ini.');

        $categories = Category::orderBy('name')->get();
        $listing->load('images');

        return view('iklan.edit', compact('listing', 'categories'));
    }

    /**
     * Perbarui data iklan milik penjual.
     *
     * Alur:
     *  1. Validasi via StoreListingRequest (foto opsional jika tidak diganti).
     *  2. Otorisasi kepemilikan iklan.
     *  3. Update data iklan dan foto baru (jika diunggah) secara atomik.
     *  4. Hapus foto lama hanya setelah transaksi berhasil dan tidak dipakai oleh iklan lain.
     */
    public function update(StoreListingRequest $request, Listing $listing): RedirectResponse
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Anda tidak memiliki hak untuk memperbarui iklan ini.');

        $validated = $request->validated();
        $newImagePath = null;
        $oldImagePathToDelete = null;

        try {
            DB::transaction(function () use ($validated, $request, $listing, &$newImagePath, &$oldImagePathToDelete): void {
                if ($request->hasFile('image')) {
                    $newImagePath = $request->file('image')->store('listings', 'public');

                    try {
                        $primaryImage = $listing->images()->where('is_primary', true)->first() ?? $listing->images()->first();

                        if ($primaryImage) {
                            $oldImagePathToDelete = $primaryImage->image_url;
                            $primaryImage->update([
                                'image_url'  => $newImagePath,
                                'is_primary' => true,
                            ]);
                        } else {
                            ListingImage::create([
                                'listing_id' => $listing->id,
                                'image_url'  => $newImagePath,
                                'is_primary' => true,
                            ]);
                        }
                    } catch (\Throwable $dbError) {
                        if ($newImagePath) {
                            Storage::disk('public')->delete($newImagePath);
                        }
                        throw $dbError;
                    }
                }

                $listing->update([
                    'category_id'         => $validated['category_id'],
                    'title'               => $validated['title'],
                    'description'         => $validated['description'],
                    'price'               => $validated['price'],
                    'condition_checklist' => ['kondisi' => $validated['condition']],
                    'addresses'           => $validated['addresses'],
                ]);
            });

            // Hapus foto lama hanya jika transaksi sukses dan tidak dipakai oleh data lain
            if ($oldImagePathToDelete && $oldImagePathToDelete !== $newImagePath) {
                $isUsedElsewhere = ListingImage::where('image_url', $oldImagePathToDelete)->exists();
                if (! $isUsedElsewhere && Storage::disk('public')->exists($oldImagePathToDelete)) {
                    Storage::disk('public')->delete($oldImagePathToDelete);
                }
            }
        } catch (\Throwable $e) {
            if ($newImagePath && Storage::disk('public')->exists($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
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
