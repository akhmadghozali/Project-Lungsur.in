<?php

use App\Http\Controllers\IklanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminVerificationController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Autentikasi Pengguna (Login, Register Jalur Ganda & Logout)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/verify-otp', [AuthController::class, 'verifyOtp'])->name('register.verify_otp');
    Route::post('/register/resend-otp', [AuthController::class, 'resendOtp'])->name('register.resend_otp');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Mahasiswa (Khusus Pengguna Login yang Berstatus Aktif & Terverifikasi)
Route::middleware(['auth', 'verified_student'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
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
    return redirect('/')->with('info', 'Halaman detail barang akan segera hadir.');
})->name('listings.show');
