<?php

use App\Http\Controllers\IklanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Iklan (Listing) Routes
|--------------------------------------------------------------------------
*/

// Halaman form pasang iklan
Route::get('/katalog/pasang', function () {
    return view('iklan.create');
})->name('katalog.pasang');

// POST handler — simpan iklan via IklanController
Route::post('/katalog/pasang', [IklanController::class, 'store'])->name('iklan.store');
