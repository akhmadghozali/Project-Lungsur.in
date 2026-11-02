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
    'phone',
    'status',
    'google_id',
    'avatar'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function listings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function bids(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function wonAuctions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Auction::class, 'winner_id');
    }

    public function buyerConversations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function sellerConversations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function wantedAds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WantedAd::class);
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
}