<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'condition_checklist',
        'addresses',
        'status',
        'is_boosted',
        'boosted_until',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'condition_checklist' => 'array',
            'is_boosted' => 'boolean',
            'boosted_until' => 'datetime',
            'view_count' => 'integer',
        ];
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // ── Relationships ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class);
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ListingImage::class)->where('is_primary', true);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $image = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        if (! $image) {
            return null;
        }

        $url = $image->image_url;

        return Str::startsWith($url, ['http://', 'https://'])
            ? $url
            : asset('storage/' . ltrim($url, '/'));
    }

    public function auction(): HasOne
    {
        return $this->hasOne(Auction::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
