<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kasur dan Perlengkapan Tidur', 'icon' => 'bed'],
            ['name' => 'Meja dan Kursi', 'icon' => 'armchair'],
            ['name' => 'Lemari dan Penyimpanan', 'icon' => 'archive'],
            ['name' => 'Elektronik', 'icon' => 'monitor'],
            ['name' => 'Peralatan Dapur', 'icon' => 'cooking-pot'],
            ['name' => 'Peralatan Kebersihan', 'icon' => 'brush'],
            ['name' => 'Dekorasi Kamar', 'icon' => 'lamp'],
            ['name' => 'Perlengkapan Mandi', 'icon' => 'bath'],
            ['name' => 'Perlengkapan Belajar', 'icon' => 'book-open'],
            ['name' => 'Lain-lain', 'icon' => 'package'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}