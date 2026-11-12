<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\User;
use App\Models\WantedAd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat 5 akun mahasiswa aktif
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

        $categories = Category::all()->keyBy('slug');
        if ($categories->isEmpty()) {
            throw new \RuntimeException('Kategori belum tersedia.');
        }

        // Daftar 20 perlengkapan kos realistis beserta foto & kategori
        $dummyItems = [
            [
                'title' => 'Kasur Busa Inoac Single 90x200 Tebal 15cm',
                'category_slug' => 'kasur-dan-perlengkapan-tidur',
                'price' => 250000,
                'desc' => 'Kondisi busa masih padat dan tidak kempes. Dipakai 1 semester saja, sudah termasuk sprei motif.',
                'image' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Meja Belajar Minimalis Kayu Pinus + Rak Buku',
                'category_slug' => 'meja-dan-kursi',
                'price' => 180000,
                'desc' => 'Kokoh, tidak goyang. Ada rak kecil di atas untuk taruh buku atau lampu belajar.',
                'image' => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Kipas Angin Berdiri Miyako 16 Inch 3 Kecepatan',
                'category_slug' => 'elektronik',
                'price' => 120000,
                'desc' => 'Angin kencang, putaran kanan-kiri normal, tidak berisik. Kabel masih utuh.',
                'image' => 'https://images.unsplash.com/photo-1618941716939-553df3c6c278?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Rice Cooker Mini Cosmos 0.8 Liter Hemat Listrik',
                'category_slug' => 'peralatan-dapur',
                'price' => 95000,
                'desc' => 'Cocok untuk anak kos masak 1-2 porsi. Panci teflon masih mulus, dapat centong & cup takar.',
                'image' => 'https://images.unsplash.com/photo-1544233726-9f1d2b27be8b?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Lemari Pakaian Plastik 4 Susun Napolly Kunci',
                'category_slug' => 'lemari-dan-penyimpanan',
                'price' => 160000,
                'desc' => 'Tingkat paling atas ada kunci. Bersih, tidak ada retak atau patah.',
                'image' => 'https://images.unsplash.com/photo-1595428774223-ef52624120d2?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Dispenser Air Portable Miyako + Galon Aqua Asli',
                'category_slug' => 'peralatan-dapur',
                'price' => 85000,
                'desc' => 'Dispenser normal fungsi panas & normal. Galon Aqua resmi bukan isi ulang.',
                'image' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Lampu Belajar LED Meja Fleksibel Eye-Protection',
                'category_slug' => 'dekorasi-kamar',
                'price' => 45000,
                'desc' => '3 tingkat kecerahan sentuh. Bisa dicharge pakai kabel USB type-C.',
                'image' => 'https://images.unsplash.com/photo-1534349762230-e0cadf78f5da?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Rak Sepatu Kayu Susun 4 Tingkat Muat 12 Pasang',
                'category_slug' => 'lemari-dan-penyimpanan',
                'price' => 60000,
                'desc' => 'Bahan kayu kokoh, rapi, cocok ditaruh di depan pintu kamar kos.',
                'image' => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Setrika Philips HD1172 Klasik Anti Lengket',
                'category_slug' => 'elektronik',
                'price' => 110000,
                'desc' => 'Cepat panas, pengatur suhu berfungsi normal, kabel original.',
                'image' => 'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Kursi Kerja Ergonomis Jaring Hidrolik Putar',
                'category_slug' => 'meja-dan-kursi',
                'price' => 220000,
                'desc' => 'Busa dudukan tebal, sandaran jaring tidak panas, hidrolik naik-turun lancar.',
                'image' => 'https://images.unsplash.com/photo-1580481077195-c3f208c02c6d?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Jemuran Baju Lipat Aluminium Gantung Kos',
                'category_slug' => 'peralatan-kebersihan',
                'price' => 75000,
                'desc' => 'Bahan aluminium anti karat, bisa dilipat rapat jika tidak dipakai.',
                'image' => 'https://images.unsplash.com/photo-1582735689369-4fe89db7114c?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Cermin Dinding Gantung Aesthetic Frame Kayu 100x40',
                'category_slug' => 'dekorasi-kamar',
                'price' => 90000,
                'desc' => 'Kaca jernih tidak cembung, frame kayu rapi, ada tali gantungan kokoh.',
                'image' => 'https://images.unsplash.com/photo-1618220179428-22790b461013?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Teko Listrik Pemanas Air Stainless 1.5L Scarlett',
                'category_slug' => 'peralatan-dapur',
                'price' => 50000,
                'desc' => 'Masak air panas untuk mie / kopi cuma butuh 3 menit, auto off saat mendidih.',
                'image' => 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Bantal & Guling Dacron Silicon Empuk (1 Pasang)',
                'category_slug' => 'kasur-dan-perlengkapan-tidur',
                'price' => 55000,
                'desc' => 'Sudah dicuci bersih dan di-laundry, include sarung bantal warna navy.',
                'image' => 'https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Rak Piring Gantung Dapur Kos 2 Tingkat Stainless',
                'category_slug' => 'peralatan-dapur',
                'price' => 65000,
                'desc' => 'Ada nampan penampung tetesan air di bawahnya, lengkap tempat sendok garpu.',
                'image' => 'https://images.unsplash.com/photo-1584990347449-3a3c26bcf641?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Karpet Bulu Rasfur Halus Kamar Kos 150x100 Abu-abu',
                'category_slug' => 'dekorasi-kamar',
                'price' => 70000,
                'desc' => 'Bulu lebat empuk, alas bintik anti slip tidak licin di lantai keramik.',
                'image' => 'https://images.unsplash.com/photo-1600121848594-d8644e57abab?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Blender Mini Portable Capsule Cutter 4 Pisau',
                'category_slug' => 'peralatan-dapur',
                'price' => 45000,
                'desc' => 'Bisa untuk buat jus buah atau bumbu masak anak kos praktis.',
                'image' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Keranjang Laundry Baju Kotor Lipat Jaring',
                'category_slug' => 'peralatan-kebersihan',
                'price' => 20000,
                'desc' => 'Kapasitas muat 1 minggu baju kotor, ada pegangan samping.',
                'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Meja Lipat Laptop Lesehan Ada Slot Gelas & Tablet',
                'category_slug' => 'meja-dan-kursi',
                'price' => 40000,
                'desc' => 'Kaki besi anti selip, permukaan halus, praktis belajar di atas kasur.',
                'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=600&auto=format&fit=crop&q=80',
            ],
            [
                'title' => 'Hair Dryer Lipat Philips EssentialCare 400 Watt',
                'category_slug' => 'elektronik',
                'price' => 85000,
                'desc' => 'Daya ramah listrik kos (400W), 2 setelan angin hangat/dingin, bisa dilipat.',
                'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=600&auto=format&fit=crop&q=80',
            ],
        ];

        // 2. Seeding 20 Iklan Barang Kos Lengkap
        $listings = collect();

        foreach ($dummyItems as $idx => $item) {
            $i = $idx + 1;
            $seller = $users[($i - 1) % 5];
            $category = $categories->get($item['category_slug']) ?? $categories->first();

            // Status: 1-5 terjual (ada buyer), 6-8 lelang aktif, 9-10 lelang closed, 11-12 boosted unggulan, sisanya available
            $status = 'available';
            if ($i <= 5) {
                $status = 'sold';
            } elseif ($i >= 6 && $i <= 10) {
                $status = 'lelang';
            }

            $isBoosted = ($i === 11 || $i === 12);

            $listing = Listing::updateOrCreate(
                [
                    'user_id' => $seller->id,
                    'title' => $item['title'],
                ],
                [
                    'category_id' => $category->id,
                    'description' => $item['desc'],
                    'price' => $item['price'],
                    'condition_checklist' => ['kondisi' => 'bekas_baik'],
                    'addresses' => 'Sekitar Kampus Politeknik Negeri Malang (Jl. Soekarno-Hatta)',
                    'status' => $status,
                    'is_boosted' => $isBoosted,
                    'boosted_until' => $isBoosted ? now()->addDays(28) : null,
                ]
            );

            // Simpan foto barang di listings_images
            ListingImage::updateOrCreate(
                [
                    'listing_id' => $listing->id,
                    'image_url' => $item['image'],
                ],
                [
                    'is_primary' => true,
                ]
            );

            // Pasang pembeli untuk 5 barang terjual
            if ($i <= 5) {
                $buyer = $users[$i % 5];
                $listing->buyer_id = $buyer->id;
                $listing->save();
            }

            $listings->push($listing);
        }

        // 3. Seeding Sesi Lelang (3 Lelang AKTIF & 2 Lelang SELESAI)
        for ($k = 0; $k < 5; $k++) {
            $listing = $listings[$k + 5]; // Menggunakan listing indeks 5 s/d 9 (Barang 6-10)

            $winner = $users[($k + 1) % 5];
            $firstBidder = $users[($k + 2) % 5];

            // 3 lelang pertama (k = 0, 1, 2) adalah AKTIF agar user bisa tes hitung mundur & bid
            // 2 lelang berikutnya (k = 3, 4) adalah CLOSED (selesai)
            $isActive = ($k < 3);

            $auction = Auction::updateOrCreate(
                ['listing_id' => $listing->id],
                [
                    'starting_price' => $listing->price,
                    'bid_increment' => 10000,
                    'bid_cap' => $listing->price * 2,
                    'start_time' => $isActive ? now()->subHours(4) : now()->subDays(3),
                    'end_time' => $isActive ? now()->addDays(2) : now()->subDay(),
                    'status' => $isActive ? 'active' : 'closed',
                    'winner_id' => $isActive ? null : $winner->id,
                ]
            );

            AuctionBid::where('auction_id', $auction->id)->delete();

            AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id' => $firstBidder->id,
                'bid_amount' => $listing->price,
                'created_at' => now()->subHours(2),
            ]);

            AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id' => $winner->id,
                'bid_amount' => $listing->price + 10000,
                'created_at' => now()->subHour(),
            ]);
        }

        // 4. Seeding 3 Papan Kebutuhan Mahasiswa (Wanted Ads)
        $wantedSamples = [
            [
                'title' => 'Dicari Kulkas Mini Portable Bekas untuk Kamar Kos',
                'category_slug' => 'elektronik',
                'budget_max' => 600000,
                'desc' => 'Cari kulkas 1 pintu mini yang masih dingin dan freezer normal. Siap angkut sendiri area Suhat / Dinoyo.',
                'address' => 'Jl. Kembang Turi, Jatimulyo (Dekat Polinema)',
            ],
            [
                'title' => 'Butuh Lemari Pakaian Kayu 2 Pintu Masih Mulus',
                'category_slug' => 'lemari-dan-penyimpanan',
                'budget_max' => 250000,
                'desc' => 'Ada gantungan baju dan rak lipat. Budget max 250k, kalau bisa include ongkir / bantu angkut.',
                'address' => 'Jl. MT Haryono Gg 4 (Dekat UB)',
            ],
            [
                'title' => 'Cari Kasur Busa Lipat / Kasur Lantai Tebal 10cm',
                'category_slug' => 'kasur-dan-perlengkapan-tidur',
                'budget_max' => 150000,
                'desc' => 'Untuk tamu teman kos menginap. Yang penting bersih dan tidak sobek.',
                'address' => 'Jl. Sigura-gura Barat (Dekat ITN)',
            ],
        ];

        foreach ($wantedSamples as $wIdx => $w) {
            $creator = $users[$wIdx % 5];
            $cat = $categories->get($w['category_slug']) ?? $categories->first();

            WantedAd::updateOrCreate(
                [
                    'user_id' => $creator->id,
                    'title' => $w['title'],
                ],
                [
                    'category_id' => $cat->id,
                    'description' => $w['desc'],
                    'budget_max' => $w['budget_max'],
                    'addresses' => $w['address'],
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Database Lungsur.in berhasil di-seed: 5 Akun, 20 Iklan Bergambar, 3 Lelang Aktif, 2 Lelang Selesai, dan 3 Papan Kebutuhan!');
    }
}
