<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminVerificationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Autentikasi Pengguna (Login, Register Jalur Ganda & Logout)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
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
