<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class IklanController extends Controller
{
    /**
     * Simpan iklan baru beserta foto ke database.
     *
     * Alur:
     *  1. Validasi input (termasuk custom rule kelipatan 1.000).
     *  2. Upload foto ke storage/app/public/listings.
     *  3. Bungkus INSERT listings + INSERT listings_images dalam DB::transaction.
     *  4. Redirect ke halaman utama dengan flash sukses.
     */
    public function store(Request $request): RedirectResponse
    {
        // ── 1. VALIDASI ────────────────────────────────────────────────────
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],

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

            'description' => ['required', 'string'],

            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',  // 2 MB
            ],
        ], [
            // ── Pesan error kustom (Bahasa Indonesia) ──
            'title.required'       => 'Judul barang wajib diisi.',
            'title.max'            => 'Judul barang maksimal 255 karakter.',
            'price.required'       => 'Harga barang wajib diisi.',
            'price.numeric'        => 'Harga harus berupa angka.',
            'price.min'            => 'Harga minimal adalah Rp1.000.',
            'condition.required'   => 'Kondisi barang wajib dipilih.',
            'condition.in'         => 'Kondisi barang yang dipilih tidak valid.',
            'description.required' => 'Deskripsi barang wajib diisi.',
            'image.required'       => 'Foto barang wajib diunggah.',
            'image.image'          => 'File yang diunggah harus berupa gambar.',
            'image.mimes'          => 'Format foto harus JPEG, PNG, JPG, atau WEBP.',
            'image.max'            => 'Ukuran foto maksimal 2 MB.',
        ]);

        // ── 2. UPLOAD FOTO ─────────────────────────────────────────────────
        //    Simpan ke: storage/app/public/listings/<filename>
        //    URL publik: /storage/listings/<filename>  (setelah php artisan storage:link)
        $imagePath = $request->file('image')->store('listings', 'public');

        // ── 3. SIMPAN KE DATABASE (atomic transaction) ─────────────────────
        try {
            DB::transaction(function () use ($validated, $imagePath): void {
                // Mapping nama field form → kolom database
                $listing = Listing::create([
                    'user_id'     => Auth::id() ?? 1,   // fallback 1 jika belum ada auth
                    'category_id' => 1,                  // default "Lainnya"; nanti disesuaikan via select kategori
                    'title'       => $validated['title'],
                    'description' => $validated['description'],
                    'price'       => $validated['price'],
                    'condition_checklist' => json_encode(['kondisi' => $validated['condition']]),
                    'addresses'   => '',                 // TODO: isi dari form alamat/lokasi
                    'status'      => 'available',
                ]);

                // Simpan relasi foto ke listings_images
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_url'  => $imagePath,
                    'is_primary' => true,
                ]);
            });
        } catch (\Throwable $e) {
            // Rollback otomatis oleh DB::transaction; hapus foto yang sudah terlanjur diupload
            Storage::disk('public')->delete($imagePath);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan iklan. Silakan coba lagi.');
        }

        // ── 4. REDIRECT SUKSES ─────────────────────────────────────────────
        return redirect('/')
            ->with('success', 'Iklan "' . $validated['title'] . '" berhasil dipasang! Terima kasih. 🎉');
    }
}
