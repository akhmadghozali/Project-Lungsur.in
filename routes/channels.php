<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Otorisasi Channel Percakapan (Private Chat)
|--------------------------------------------------------------------------
| Hanya pembeli (buyer) atau penjual (seller) pemilik percakapan yang diizinkan
| mendengarkan broadcast WebSocket pada channel percakapan ini.
*/
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }

    return (int) $user->id === (int) $conversation->buyer_id
        || (int) $user->id === (int) $conversation->seller_id;
});
