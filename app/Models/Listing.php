<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'description', 'price',
        'condition_checklist', 'addresses', 'status',
        'is_boosted', 'boosted_until', 'view_count',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'condition_checklist' => 'array',
        'is_boosted'          => 'boolean',
        'boosted_until'       => 'datetime',
    ];

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class);
    }

    /**
     * URL foto utama: pakai foto yang is_primary, kalau tidak ada ambil foto pertama.
     * image_url boleh berupa link penuh (http...) atau path di storage.
     */
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
}