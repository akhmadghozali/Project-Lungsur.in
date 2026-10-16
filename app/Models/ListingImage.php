<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingImage extends Model
{
    protected $table = 'listings_images';

    protected $fillable = [
        'listing_id',
        'image_url',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
