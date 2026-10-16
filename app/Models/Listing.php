<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use SoftDeletes;

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
            'price'             => 'decimal:2',
            'condition_checklist' => 'array',
            'is_boosted'        => 'boolean',
            'boosted_until'     => 'datetime',
        ];
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
}
