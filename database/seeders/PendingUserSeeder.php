<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PendingUserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin untuk login & tes panel
        User::updateOrCreate(
            ['email' => 'admin@lungsurin.test'],
            [
                'name' => 'Admin Lungsur.in',
                'email_type' => 'public',
                'verification_status' => 'active',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Pendaftar email non-kampus yang menunggu verifikasi KTM
        $pending = [
            ['name' => 'Budi Santoso', 'nim' => '2441720001', 'email' => 'budi.santoso@gmail.com'],
            ['name' => 'Siti Aminah', 'nim' => '2441720002', 'email' => 'siti.aminah@yahoo.com'],
            ['name' => 'Rizky Pratama', 'nim' => '2441720003', 'email' => 'rizky.pratama@outlook.com'],
        ];

        foreach ($pending as $row) {
            User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'nim' => $row['nim'],
                    'email_type' => 'public',
                    'ktm_image_path' => 'ktm/dummy.jpg',
                    'verification_status' => 'pending',
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'campus' => 'Polinema',
                    'phone' => '08123456789',
                    'status' => 'active',
                ]
            );
        }

        // Pembanding: email kampus langsung lolos, tidak boleh muncul di antrean
        User::updateOrCreate(
            ['email' => 'dina@student.polinema.ac.id'],
            [
                'name' => 'Dina Kusuma',
                'nim' => '2441720004',
                'email_type' => 'campus',
                'verification_status' => 'active',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'campus' => 'Polinema',
                'phone' => '08123456780',
                'status' => 'active',
            ]
        );
    }
}