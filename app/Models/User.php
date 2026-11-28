<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Listing;

#[Fillable([
    'name',
    'nim',
    'email',
    'email_type',
    'ktm_image_path',
    'verification_status',
    'rejection_reason',
    'verified_by',
    'verified_at',
    'password',
    'role',
    'campus',
    'status',
    'google_id',
    'avatar',
    'ban_reason',
    'banned_at',
    'ban_expires_at',
    'banned_by',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'ban_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Memeriksa apakah masa ban sementara sudah berakhir.
     * Jika sudah berakhir, status akun dikembalikan menjadi active.
     */
    public function refreshBanStatus(): void
    {
        if (
            $this->status === 'suspended' &&
            $this->ban_expires_at !== null &&
            $this->ban_expires_at->isPast()
        ) {
            $this->update([
                'status' => 'active',
                'ban_reason' => null,
                'banned_at' => null,
                'ban_expires_at' => null,
                'banned_by' => null,
            ]);
        }
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function purchasedListings(): HasMany
    {
        return $this->hasMany(Listing::class, 'buyer_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function wonAuctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'winner_id');
    }

    public function buyerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function sellerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function wantedAds(): HasMany
    {
        return $this->hasMany(WantedAd::class);
    }

    public function boostPayments(): HasMany
    {
        return $this->hasMany(BoostPayment::class);
    }

    public function auctionPayments(): HasMany
    {
        return $this->hasMany(AuctionPayment::class);
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function getActiveMode(): string
    {
        return session('user_mode', $this->role === 'seller' ? 'seller' : 'buyer');
    }

    public function isSeller(): bool
    {
        return $this->getActiveMode() === 'seller';
    }

    public function isBuyer(): bool
    {
        return $this->getActiveMode() === 'buyer';
    }

    public function unreadMessagesCount(): int
    {
        return Message::where('sender_id', '!=', $this->id)
            ->where('is_read', false)
            ->whereHas('conversation', function ($q) {
                $q->where('buyer_id', $this->id)
                  ->orWhere('seller_id', $this->id);
            })
            ->count();
    }
}
