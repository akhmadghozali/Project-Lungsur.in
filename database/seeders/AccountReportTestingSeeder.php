<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountReportTestingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat akun testing
        $buyer = User::updateOrCreate(
            ['email' => 'tester.buyer@polinema.ac.id'],
            [
                'name' => 'Tester Buyer',
                'password' => Hash::make('Testing123!'),
                'role' => 'buyer',
                'email_type' => 'campus',
                'verification_status' => 'active',
                'status' => 'active',
                'campus' => 'Politeknik Negeri Malang',
                'email_verified_at' => now(),
            ]
        );

        $seller1 = User::updateOrCreate(
            ['email' => 'tester.seller1@polinema.ac.id'],
            [
                'name' => 'Tester Seller 1',
                'password' => Hash::make('Testing123!'),
                'role' => 'seller',
                'email_type' => 'campus',
                'verification_status' => 'active',
                'status' => 'active',
                'campus' => 'Politeknik Negeri Malang',
                'email_verified_at' => now(),
            ]
        );

        $seller2 = User::updateOrCreate(
            ['email' => 'tester.seller2@polinema.ac.id'],
            [
                'name' => 'Tester Seller 2',
                'password' => Hash::make('Testing123!'),
                'role' => 'seller',
                'email_type' => 'campus',
                'verification_status' => 'active',
                'status' => 'active',
                'campus' => 'Politeknik Negeri Malang',
                'email_verified_at' => now(),
            ]
        );

        // 2. Membuat iklan testing
        $items = [
            [
                'seller' => $seller1,
                'title' => 'Kasur Busa Single Tester',
                'category' => 'kasur-dan-perlengkapan-tidur',
                'price' => 150000,
                'description' => 'Kasur busa single kondisi masih bagus dan bersih.',
                'image' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600',
            ],
            [
                'seller' => $seller1,
                'title' => 'Meja Belajar Minimalis Tester',
                'category' => 'meja-dan-kursi',
                'price' => 125000,
                'description' => 'Meja belajar minimalis cocok untuk kamar kos.',
                'image' => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=600',
            ],
            [
                'seller' => $seller2,
                'title' => 'Kipas Angin Berdiri Tester',
                'category' => 'elektronik',
                'price' => 100000,
                'description' => 'Kipas angin masih berfungsi normal.',
                'image' => 'https://images.unsplash.com/photo-1618941716939-553df3c6c278?w=600',
            ],
            [
                'seller' => $seller2,
                'title' => 'Rice Cooker Mini Tester',
                'category' => 'peralatan-dapur',
                'price' => 85000,
                'description' => 'Rice cooker mini cocok untuk kebutuhan anak kos.',
                'image' => 'https://images.unsplash.com/photo-1544233726-9f1d2b27be8b?w=600',
            ],
        ];

        $listings = [];

        foreach ($items as $item) {
            $category = Category::where('slug', $item['category'])->firstOrFail();

            $listing = Listing::updateOrCreate(
                [
                    'user_id' => $item['seller']->id,
                    'title' => $item['title'],
                ],
                [
                    'category_id' => $category->id,
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'condition_checklist' => ['kondisi' => 'bekas_baik'],
                    'addresses' => 'Sekitar Kampus Politeknik Negeri Malang',
                    'status' => 'available',
                    'is_boosted' => false,
                    'boosted_until' => null,
                ]
            );

            ListingImage::updateOrCreate(
                [
                    'listing_id' => $listing->id,
                    'image_url' => $item['image'],
                ],
                [
                    'is_primary' => true,
                ]
            );

            $listings[] = $listing;
        }

        // 3. Membuat percakapan buyer dengan seller 1
        $conversation1 = Conversation::updateOrCreate(
            [
                'listing_id' => $listings[0]->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller1->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        Message::firstOrCreate(
            [
                'conversation_id' => $conversation1->id,
                'sender_id' => $buyer->id,
                'message_text' => 'Halo, apakah kasurnya masih tersedia?',
            ],
            [
                'is_read' => true,
                'created_at' => now()->subMinutes(10),
            ]
        );

        Message::firstOrCreate(
            [
                'conversation_id' => $conversation1->id,
                'sender_id' => $seller1->id,
                'message_text' => 'Halo, masih tersedia.',
            ],
            [
                'is_read' => true,
                'created_at' => now()->subMinutes(8),
            ]
        );

        Message::firstOrCreate(
            [
                'conversation_id' => $conversation1->id,
                'sender_id' => $buyer->id,
                'message_text' => 'Boleh tahu kondisi barangnya?',
            ],
            [
                'is_read' => true,
                'created_at' => now()->subMinutes(5),
            ]
        );

        Message::firstOrCreate(
            [
                'conversation_id' => $conversation1->id,
                'sender_id' => $seller1->id,
                'message_text' => 'Kondisinya masih bagus dan layak digunakan.',
            ],
            [
                'is_read' => false,
                'created_at' => now(),
            ]
        );

        // 4. Membuat percakapan buyer dengan seller 2
        $conversation2 = Conversation::updateOrCreate(
            [
                'listing_id' => $listings[2]->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller2->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        Message::firstOrCreate(
            [
                'conversation_id' => $conversation2->id,
                'sender_id' => $buyer->id,
                'message_text' => 'Halo, kipas anginnya masih ada?',
            ],
            [
                'is_read' => true,
                'created_at' => now()->subMinutes(6),
            ]
        );

        Message::firstOrCreate(
            [
                'conversation_id' => $conversation2->id,
                'sender_id' => $seller2->id,
                'message_text' => 'Masih ada, silakan kalau berminat.',
            ],
            [
                'is_read' => false,
                'created_at' => now(),
            ]
        );

        $this->command->info('Data testing berhasil dibuat: 3 akun, 4 iklan, dan 2 percakapan.');
    }
}