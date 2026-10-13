<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusDomain extends Model
{
    use HasFactory;

    protected $table = 'campus_domains';

    protected $fillable = [
        'domain_name',
        'campus_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
