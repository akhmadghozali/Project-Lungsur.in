<?php

use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\Admin\AdminBoostPaymentController;
use App\Http\Controllers\Admin\AdminAuctionPaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuctionPaymentController;
use App\Http\Controllers\BoostPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\IklanController;
use App\Http\Controllers\WantedAdController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use App\Models\Listing; 

Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman katalog barang publik (listings.index)
Route::get('/katalog', [HomeController::class, 'index'])->name('listings.index');

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
    Route::get('/auth/google/ktm', [AuthController::class, 'showGoogleKtmForm'])->name('auth.google.ktm');
    Route::post('/auth/google/ktm', [AuthController::class, 'submitGoogleKtm'])->name('auth.google.ktm.submit');

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
    Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('dashboard.history');
    Route::post('/switch-mode', [DashboardController::class, 'switchMode'])->name('user.switch_mode');

    // Melaporkan iklan (Bisa penjual atau pembeli)
    Route::post('/katalog/{listing}/report', [ReportController::class, 'store'])->name('reports.store');
});

// Aksi Khusus MODE PENJUAL
Route::middleware(['auth', 'verified_student', 'active_mode:seller'])->group(function () {
    // Pengelolaan listing
    Route::get('/katalog/pasang', [IklanController::class, 'create'])->name('katalog.pasang');
    Route::post('/katalog/pasang', [IklanController::class, 'store'])->name('iklan.store');
    Route::get('/katalog/{listing}/edit', [IklanController::class, 'edit'])->name('iklan.edit');
    Route::match(['put', 'patch'], '/katalog/{listing}', [IklanController::class, 'update'])->name('iklan.update');
    Route::patch('/dashboard/listings/{listing}/status', [DashboardController::class, 'updateListingStatus'])->name('dashboard.listings.status');
    Route::delete('/dashboard/listings/{listing}', [DashboardController::class, 'destroyListing'])->name('dashboard.listings.destroy');

    // Monetisasi & Lelang Penjual
    Route::get('/katalog/{listing}/boost', [BoostPaymentController::class, 'create'])->name('boost.create');
    Route::post('/katalog/{listing}/boost', [BoostPaymentController::class, 'store'])->name('boost.store');
    Route::get('/lelang/buka', [AuctionPaymentController::class, 'selectListing'])->name('auctions.create');
    Route::get('/katalog/{listing}/lelang', [AuctionPaymentController::class, 'create'])->name('auctions.payment.create');
    Route::post('/katalog/{listing}/lelang', [AuctionPaymentController::class, 'store'])->name('auctions.payment.store');

    // Tawarkan barang ke pembeli Wanted Ad
    Route::post('/cari-barang/{wantedAd}/tawari', [WantedAdController::class, 'offerItem'])->name('wanted-ads.offer');
});

// Aksi Khusus MODE PEMBELI
Route::middleware(['auth', 'verified_student', 'active_mode:buyer'])->group(function () {
    // Wanted Ads (Cari Kebutuhan)
    Route::post('/cari-barang', [WantedAdController::class, 'store'])->name('wanted-ads.store');
    Route::delete('/cari-barang/{wantedAd}', [WantedAdController::class, 'destroy'])->name('wanted-ads.destroy');

    // Bidding Lelang
    Route::post('/lelang/{id}/tawar', [AuctionController::class, 'placeBid'])
        ->whereNumber('id')
        ->name('auctions.bids.store');
});

// Stub halaman detail barang (agar tombol "Lihat Detail" di beranda tidak 404)
Route::get('/katalog/{id}', [IklanController::class, 'show'])
    ->name('listings.show');

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

/*
|--------------------------------------------------------------------------
| Papan Kebutuhan Mahasiswa (Wanted Ads / Cari Barang)
|--------------------------------------------------------------------------
*/
// Papan Wanted Ads khusus Mode Pembeli (atau Guest); Penjual diarahkan ke Dasbor Peluang
Route::get('/cari-barang', [WantedAdController::class, 'index'])
    ->middleware(['active_mode:buyer'])
    ->name('wanted-ads.index');

Route::get('/wanted-ads', fn () => redirect()->route('wanted-ads.index'));
    
// Alias rute kompatibilitas untuk detail iklan & lelang
Route::get('/listings/{id}', [IklanController::class, 'show']);
Route::get('/auctions/{id}', fn ($id) => redirect()->route('auctions.show', $id));

// Modul Chat Komunitas Mahasiswa
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/start/{listing}', function ($listing) {
        return redirect()->route('chat.index', ['listing_id' => $listing]);
    })->name('chat.start');

    // Chat Web API (Session-authenticated endpoints)
    Route::prefix('chat/api')->group(function () {
        Route::get('/conversations', [ChatController::class, 'index'])->name('chat.web.conversations.index');
        Route::post('/conversations', [ChatController::class, 'startConversation'])->name('chat.web.conversations.store');
        Route::get('/conversations/{id}', [ChatController::class, 'show'])->name('chat.web.conversations.show');
        Route::post('/conversations/{id}/messages', [ChatController::class, 'sendMessage'])->name('chat.web.conversations.messages.store');
        Route::patch('/conversations/{id}/read', [ChatController::class, 'markAsRead'])->name('chat.web.conversations.read');
    });
});
