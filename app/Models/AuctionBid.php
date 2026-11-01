<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBid extends Model
{
    use HasFactory;

    protected $table = 'auctions_bids';

    protected $fillable = [
        'auction_id',
        'user_id',
        'bid_amount',
    ];

    protected function casts(): array
    {
        return [
            'bid_amount' => 'float',
        ];
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk mengurutkan tawaran dari nominal tertinggi ke terendah.
     */
    public function scopeHighestFirst($query)
    {
        return $query->orderByDesc('bid_amount');
    }

    /**
     * Scope untuk memfilter tawaran berdasarkan ID pengguna tertentu.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
