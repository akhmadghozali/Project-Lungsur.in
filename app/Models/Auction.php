<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'starting_price',
        'bid_increment',
        'bid_cap',
        'start_time',
        'end_time',
        'status',
        'winner_id',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'float',
            'bid_increment' => 'float',
            'bid_cap' => 'float',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class)->orderByDesc('bid_amount')->orderBy('created_at');
    }

    public function highestBid(): HasOne
    {
        return $this->hasOne(AuctionBid::class)->ofMany('bid_amount', 'max');
    }

    /**
     * Harga tawaran tertinggi saat ini atau harga awal jika belum ada tawaran.
     */
    public function currentPrice(): float
    {
        return (float) ($this->highestBid?->bid_amount ?? $this->starting_price);
    }

    /**
     * Batas minimal tawaran berikutnya yang sah.
     */
    public function minNextBid(): float
    {
        if (!$this->highestBid) {
            return (float) $this->starting_price;
        }

        return (float) ($this->highestBid->bid_amount + $this->bid_increment);
    }

    /**
     * Apakah waktu lelang telah melewati batas selesai.
     */
    public function isExpired(): bool
    {
        if (!$this->end_time) {
            return false;
        }

        return now()->gte($this->end_time);
    }

    /**
     * Sisa waktu lelang dalam satuan detik.
     */
    public function timeRemainingSeconds(): int
    {
        if ($this->status !== 'active' || !$this->end_time || $this->isExpired()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->end_time, false));
    }
}
