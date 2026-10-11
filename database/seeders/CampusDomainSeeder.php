<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusDomainSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            [
                'domain_name' => 'polinema.ac.id',
                'campus_name' => 'Politeknik Negeri Malang',
            ],
            [
                'domain_name' => 'ub.ac.id',
                'campus_name' => 'Universitas Brawijaya',
            ],
            [
                'domain_name' => 'um.ac.id',
                'campus_name' => 'Universitas Negeri Malang',
            ],
            [
                'domain_name' => 'uin-malang.ac.id',
                'campus_name' => 'UIN Maulana Malik Ibrahim Malang',
            ],
        ];

        foreach ($domains as $domain) {
            DB::table('campus_domains')->updateOrInsert(
                ['domain_name' => $domain['domain_name']],
                [
                    'campus_name' => $domain['campus_name'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
