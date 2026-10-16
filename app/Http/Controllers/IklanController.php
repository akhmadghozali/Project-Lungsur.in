<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     *  1. Validasi input (termasuk custom rule kelipatan 1.000).
     *  2. Upload foto + INSERT listings + INSERT listings_images dalam satu DB::transaction.
     *  3. Redirect ke halaman utama dengan flash sukses.
     */
    public function store(Request $request): RedirectResponse
    {
        // ── 1. VALIDASI ────────────────────────────────────────────────────
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],  // sesuai VARCHAR(200) di DB

            'price' => [
                'required',
                'numeric',
                'min:1000',
                // Kustom closure: harga wajib kelipatan 1.000
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ((int) $value % 1000 !== 0) {
                        $fail('Harga harus dalam kelipatan Rp1.000 (contoh: Rp15.000, Rp20.000).');
                    }
                },
            ],

            'condition' => [
                'required',
                'string',
                'in:baru,bekas_mulus,bekas_baik,bekas_normal,bekas_perlu_perbaikan',
            ],

            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'addresses' => ['required', 'string', 'max:255'],

            'description' => ['required', 'string'],

            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',  // 2 MB
            ],
        ], [
            // ── Pesan error kustom (Bahasa Indonesia) ──
            'title.required'        => 'Judul barang wajib diisi.',
            'title.max'             => 'Judul barang maksimal 200 karakter.',
            'price.required'        => 'Harga barang wajib diisi.',
            'price.numeric'         => 'Harga harus berupa angka.',
            'price.min'             => 'Harga minimal adalah Rp1.000.',
            'condition.required'    => 'Kondisi barang wajib dipilih.',
            'condition.in'          => 'Kondisi barang yang dipilih tidak valid.',
            'category_id.required'  => 'Kategori barang wajib dipilih.',
            'category_id.exists'    => 'Kategori yang dipilih tidak valid.',
            'addresses.required'    => 'Lokasi/area kampus wajib diisi.',
            'description.required'  => 'Deskripsi barang wajib diisi.',
            'image.required'        => 'Foto barang wajib diunggah.',
            'image.image'           => 'File yang diunggah harus berupa gambar.',
            'image.mimes'           => 'Format foto harus JPEG, PNG, JPG, atau WEBP.',
            'image.max'             => 'Ukuran foto maksimal 2 MB.',
        ]);

        // ── 2. SIMPAN ATOMIK (upload foto + DB dalam satu transaction) ─────
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

        // ── 3. REDIRECT SUKSES ─────────────────────────────────────────────
        return redirect('/')
            ->with('success', 'Iklan "' . $validated['title'] . '" berhasil dipasang! Terima kasih. 🎉');
    }
}
