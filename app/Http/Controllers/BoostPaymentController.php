<?php

namespace App\Http\Controllers;

use App\Models\BoostPayment;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BoostPaymentController extends Controller
{
    /**
     * Tampilkan formulir pembayaran & upload bukti transfer boost iklan.
     */
    public function create(Listing $listing): View|RedirectResponse
    {
        // 1. Otorisasi kepemilikan iklan
        abort_if($listing->user_id !== Auth::id(), 403, 'Anda tidak berhak melakukan boost pada iklan pengguna lain.');

        // 2. Wajib dalam Mode Penjual
        if (!Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Silakan beralih ke Mode Penjual terlebih dahulu untuk mengelola pembayaran promosi boost.'
            );
        }

        // 3. Cek apakah iklan sedang aktif di-boost
        if ($listing->isCurrentlyBoosted()) {
            $formattedDate = $listing->boosted_until
                ? $listing->boosted_until->translatedFormat('d F Y - H:i') . ' WIB'
                : 'waktu yang belum ditentukan';

            return redirect()->route('dashboard')->with(
                'info',
                "Iklan \"{$listing->title}\" saat ini sudah aktif di-boost hingga {$formattedDate}."
            );
        }

        // 4. Cek apakah ada pembayaran boost yang masih berstatus pending verifikasi
        $pendingPayment = $listing->boostPayments()->where('status', 'pending')->latest()->first();
        if ($pendingPayment) {
            return redirect()->route('dashboard')->with(
                'warning',
                "Pembayaran boost untuk iklan \"{$listing->title}\" (Invoice: {$pendingPayment->invoice_code}) sedang dalam proses verifikasi oleh Admin."
            );
        }

        $listing->load(['category', 'images']);

        return view('boost.create', compact('listing'));
    }

    /**
     * Simpan bukti transfer pembayaran boost (5K - 30 Hari).
     */
    public function store(Request $request, Listing $listing): RedirectResponse
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Akses ditolak.');

        if (!Auth::user()->isSeller()) {
            return redirect()->route('dashboard')->with(
                'warning',
                'Anda harus berada dalam Mode Penjual untuk mengirim bukti pembayaran boost.'
            );
        }

        // Cegah duplikasi submit jika sudah aktif atau sedang pending
        if ($listing->isCurrentlyBoosted()) {
            return redirect()->route('dashboard')->with('info', 'Iklan ini sedang aktif di-boost.');
        }

        if ($listing->boostPayments()->where('status', 'pending')->exists()) {
            return redirect()->route('dashboard')->with('warning', 'Pengajuan boost sedang menunggu verifikasi admin.');
        }

        $validated = $request->validate([
            'payment_channel' => ['required', 'string', 'in:qris,bca,mandiri,gopay,shopeepay,dana'],
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maksimal 2MB
        ], [
            'payment_channel.required' => 'Silakan pilih metode/saluran pembayaran yang digunakan.',
            'payment_channel.in' => 'Metode pembayaran tidak valid.',
            'proof_image.required' => 'Unggah foto struk atau tangkapan layar bukti transfer pembayaran Anda.',
            'proof_image.image' => 'Berkas bukti transfer harus berupa format gambar valid.',
            'proof_image.mimes' => 'Format gambar yang diperbolehkan: JPG, JPEG, PNG, atau WEBP.',
            'proof_image.max' => 'Ukuran berkas gambar maksimal 2MB.',
        ]);

        // Simpan berkas bukti ke storage publik
        $proofPath = $request->file('proof_image')->store('boost_proofs', 'public');

        // Generate nomor invoice unik: BST-YYYYMMDD-XXXX
        $invoiceCode = 'BST-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Buat record pembayaran boost baru (Rp 5.000 untuk 30 Hari)
        BoostPayment::create([
            'user_id' => Auth::id(),
            'listing_id' => $listing->id,
            'invoice_code' => $invoiceCode,
            'amount' => 5000.00,
            'payment_channel' => $validated['payment_channel'],
            'proof_image' => $proofPath,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            "Bukti pembayaran boost untuk \"{$listing->title}\" (Invoice: {$invoiceCode}) berhasil dikirim! Iklan akan otomatis diprioritaskan di atas selama 30 hari begitu disetujui Admin."
        );
    }
}

