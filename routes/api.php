<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Lungsur.in
|--------------------------------------------------------------------------
|
| Rute API untuk Mesin Lelang dan Layanan Realtime Chat Pusher.
|
*/

// 1. Mesin Lelang (Auction Engine)
Route::prefix('auctions')->group(function () {
    Route::get('/', [AuctionController::class, 'index'])->name('api.auctions.index');
    Route::get('/{id}', [AuctionController::class, 'show'])->name('api.auctions.show');
    Route::post('/{id}/bids', [AuctionController::class, 'placeBid'])->name('api.auctions.bids.store');
    Route::post('/{id}/close', [AuctionController::class, 'close'])->name('api.auctions.close');
});

// 2. Chat Backend Realtime (Pusher WebSockets)
Route::prefix('conversations')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('api.conversations.index');
    Route::post('/', [ChatController::class, 'startConversation'])->name('api.conversations.store');
    Route::get('/{id}', [ChatController::class, 'show'])->name('api.conversations.show');
    Route::post('/{id}/messages', [ChatController::class, 'sendMessage'])->name('api.conversations.messages.store');
    Route::patch('/{id}/read', [ChatController::class, 'markAsRead'])->name('api.conversations.read');
});
