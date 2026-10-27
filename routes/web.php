<?php

use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\IklanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Autentikasi Pengguna (Login, Register Jalur Ganda & Logout)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/verify-otp', [AuthController::class, 'verifyOtp'])->name('register.verify_otp');
    Route::post('/register/resend-otp', [AuthController::class, 'resendOtp'])->name('register.resend_otp');

    // Google OAuth SSO
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Lupa Kata Sandi (Password Reset via OTP Email)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Mahasiswa (Khusus Pengguna Login yang Berstatus Aktif & Terverifikasi)
Route::middleware(['auth', 'verified_student'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-mode', [DashboardController::class, 'switchMode'])->name('user.switch_mode');
    Route::patch('/dashboard/listings/{listing}/status', [DashboardController::class, 'updateListingStatus'])->name('dashboard.listings.status');
    Route::delete('/dashboard/listings/{listing}', [DashboardController::class, 'destroyListing'])->name('dashboard.listings.destroy');
});
// Dashboard admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/verifications', [AdminVerificationController::class, 'index'])->name('verifications.index');
    Route::post('/verifications/{user}/approve', [AdminVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{user}/reject', [AdminVerificationController::class, 'reject'])->name('verifications.reject');
});

/*
|--------------------------------------------------------------------------
| Iklan (Listing) Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified_student'])->group(function () {
    // Halaman form pasang iklan — hanya user terverifikasi
    Route::get('/katalog/pasang', [IklanController::class, 'create'])->name('katalog.pasang');

    // POST handler — simpan iklan via IklanController
    Route::post('/katalog/pasang', [IklanController::class, 'store'])->name('iklan.store');
});

// Stub halaman detail barang (agar tombol "Lihat Detail" di beranda tidak 404)
Route::get('/katalog/{id}', function ($id) {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.verifications.index');
    }
    return redirect('/')->with('info', 'Halaman detail barang akan segera hadir.');
})->name('listings.show');

// Halaman daftar dan detail lelang
Route::get('/lelang', [AuctionController::class, 'webIndex'])
    ->name('auctions.index');

Route::get('/lelang/{id}', [AuctionController::class, 'webShow'])
    ->whereNumber('id')
    ->name('auctions.show');

// Menutup lelang ketika countdown habis
Route::post('/lelang/{id}/expire', [AuctionController::class, 'expire'])
    ->whereNumber('id')
    ->name('auctions.expire');

// Mengajukan penawaran lelang
Route::post('/lelang/{id}/tawar', [AuctionController::class, 'placeBid'])
    ->middleware(['auth', 'verified_student'])
    ->whereNumber('id')
    ->name('auctions.bids.store');
