<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat 5 akun mahasiswa
        $users = collect();

        for ($i = 1; $i <= 5; $i++) {
            $users->push(User::updateOrCreate(
                ['email' => "demo{$i}@lungsurin.test"],
                [
                    'name' => "Mahasiswa Demo {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'email_type' => 'campus',
                    'verification_status' => 'active',
                    'status' => 'active',
                    'campus' => 'Politeknik Negeri Malang',
                    'email_verified_at' => now(),
                ]
            ));
        }

        $categories = Category::orderBy('id')->get();

        if ($categories->isEmpty()) {
            throw new \RuntimeException('Kategori belum tersedia.');
        }

        // 2. Membuat 20 iklan
        $listings = collect();

        for ($i = 1; $i <= 20; $i++) {
            $seller = $users[($i - 1) % 5];

            $listings->push(Listing::updateOrCreate(
                [
                    'user_id' => $seller->id,
                    'title' => "Barang Demo {$i}",
                ],
                [
                    'category_id' => $categories[($i - 1) % $categories->count()]->id,
                    'description' => "Deskripsi barang demo {$i}.",
                    'price' => 100000 + ($i * 10000),
                    'condition_checklist' => ['kondisi' => 'bekas_baik'],
                    'addresses' => 'Politeknik Negeri Malang',
                    'status' => $i <= 5 ? 'sold' : ($i <= 10 ? 'booked' : 'available'),
                ]
            ));

            // Lima iklan pertama memiliki riwayat pembelian
            if ($i <= 5) {
                $buyer = $users[($i) % 5];
                $listings[$i - 1]->buyer_id = $buyer->id;
                $listings[$i - 1]->save();
            }
        }

        // 3. Membuat 5 lelang beserta bid dan pemenang
        for ($i = 0; $i < 5; $i++) {
            $listing = $listings[$i + 5];

            $winner = $users[($i + 1) % 5];
            $firstBidder = $users[($i + 2) % 5];

            $auction = Auction::updateOrCreate(
                ['listing_id' => $listing->id],
                [
                    'starting_price' => 100000,
                    'bid_increment' => 10000,
                    'bid_cap' => 300000,
                    'start_time' => now()->subDays(3),
                    'end_time' => now()->subDay(),
                    'status' => 'closed',
                    'winner_id' => $winner->id,
                ]
            );

            AuctionBid::where('auction_id', $auction->id)->delete();

            AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id' => $firstBidder->id,
                'bid_amount' => 100000,
            ]);

            AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id' => $winner->id,
                'bid_amount' => 120000,
            ]);
        }

        $this->command->info('5 akun, 20 iklan, dan 5 lelang berhasil dibuat.');
    }
}
