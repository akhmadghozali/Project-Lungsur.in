<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WantedAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'budget_max',
        'addresses',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'budget_max' => 'float',
        ];
    }

    /**
     * Scope untuk mengambil hanya iklan kebutuhan yang berstatus aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Relasi ke mahasiswa pencari barang.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke kategori perlengkapan kos.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Format rupiah untuk pagu anggaran maksimal.
     */
    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp ' . number_format($this->budget_max, 0, ',', '.');
    }
}
