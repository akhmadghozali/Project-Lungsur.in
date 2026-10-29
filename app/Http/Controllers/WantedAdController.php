<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\WantedAd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WantedAdController extends Controller
{
    /**
     * Menampilkan papan informasi barang yang sedang dicari mahasiswa (Wanted Ads).
     */
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

        return view('wanted_ads.index', compact(
            'wantedAds',
            'categories',
            'search',
            'categoryParam'
        ));
    }

    /**
     * Mempublikasikan permintaan barang kebutuhan kos baru.
     */
    public function store(Request $request): RedirectResponse
    {
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
            'budget_max.required' => 'Pagu anggaran maksimal wajib diisi.',
            'budget_max.numeric' => 'Anggaran harus berupa angka.',
            'budget_max.min' => 'Anggaran minimal adalah Rp1.000.',
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
}
