<?php

namespace Tests\Unit;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_price_uses_starting_price_without_bids(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
        ]);

        $this->assertEquals(100000, $auction->currentPrice());
    }
    public function test_current_price_uses_highest_bid_when_bids_exist(): void
    {
        $seller = User::factory()->create();
        $bidder = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
        ]);

        $auction->bids()->create([
            'user_id' => $bidder->id,
            'bid_amount' => 150000,
        ]);

        $this->assertEquals(150000, $auction->currentPrice());
    }
    public function test_min_next_bid_uses_starting_price_without_bids(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
        ]);

        $this->assertEquals(100000, $auction->minNextBid());
    }
    public function test_min_next_bid_adds_increment_to_highest_bid(): void
    {
        $seller = User::factory()->create();
        $bidder = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
        ]);

        $auction->bids()->create([
            'user_id' => $bidder->id,
            'bid_amount' => 150000,
        ]);

        $this->assertEquals(160000, $auction->minNextBid());
    }
    public function test_auction_is_expired_when_end_time_has_passed(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
            'end_time' => now()->subMinute(),
        ]);

        $this->assertTrue($auction->isExpired());
    }
    public function test_auction_is_not_expired_when_end_time_is_in_future(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
            'end_time' => now()->addHour(),
        ]);

        $this->assertFalse($auction->isExpired());
    }
    public function test_auction_is_not_expired_when_end_time_is_null(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
            'end_time' => null,
        ]);

        $this->assertFalse($auction->isExpired());
    }
    public function test_time_remaining_returns_positive_seconds_for_active_auction(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
            'end_time' => now()->addMinutes(10),
        ]);

        $this->assertGreaterThan(0, $auction->timeRemainingSeconds());
    }
    public function test_time_remaining_returns_zero_for_expired_auction(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'active',
            'end_time' => now()->subMinute(),
        ]);

        $this->assertEquals(0, $auction->timeRemainingSeconds());
    }
    public function test_time_remaining_returns_zero_for_inactive_auction(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Laptop Bekas',
            'description' => 'Laptop untuk pengujian.',
            'price' => 1000000,
            'addresses' => 'Malang',
            'status' => 'available',
        ]);

        $auction = Auction::create([
            'listing_id' => $listing->id,
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'bid_cap' => 500000,
            'status' => 'closed',
            'end_time' => now()->addHour(),
        ]);

        $this->assertEquals(0, $auction->timeRemainingSeconds());
    }
    public function test_active_scope_returns_only_active_auctions(): void
{
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'Elektronik',
        'slug' => 'elektronik',
    ]);

    $listing1 = Listing::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Laptop Aktif',
        'description' => 'Pengujian lelang aktif.',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    $listing2 = Listing::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Laptop Closed',
        'description' => 'Pengujian lelang tertutup.',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    Auction::create([
        'listing_id' => $listing1->id,
        'starting_price' => 100000,
        'bid_increment' => 10000,
        'bid_cap' => 500000,
        'status' => 'active',
    ]);

    Auction::create([
        'listing_id' => $listing2->id,
        'starting_price' => 100000,
        'bid_increment' => 10000,
        'bid_cap' => 500000,
        'status' => 'closed',
    ]);

    $activeAuctions = Auction::active()->get();

    $this->assertCount(1, $activeAuctions);
    $this->assertEquals('active', $activeAuctions->first()->status);
}
public function test_closed_scope_returns_only_closed_auctions(): void
{
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'Elektronik',
        'slug' => 'elektronik',
    ]);

    $listing1 = Listing::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Laptop Aktif',
        'description' => 'Pengujian lelang aktif.',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    $listing2 = Listing::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Laptop Closed',
        'description' => 'Pengujian lelang tertutup.',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    Auction::create([
        'listing_id' => $listing1->id,
        'starting_price' => 100000,
        'bid_increment' => 10000,
        'bid_cap' => 500000,
        'status' => 'active',
    ]);

    Auction::create([
        'listing_id' => $listing2->id,
        'starting_price' => 100000,
        'bid_increment' => 10000,
        'bid_cap' => 500000,
        'status' => 'closed',
    ]);

    $closedAuctions = Auction::closed()->get();

    $this->assertCount(1, $closedAuctions);
    $this->assertEquals('closed', $closedAuctions->first()->status);
}
public function test_ongoing_scope_returns_active_auctions_not_expired(): void
{
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'Elektronik',
        'slug' => 'elektronik',
    ]);

    $listing1 = Listing::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Laptop Ongoing',
        'description' => 'Pengujian lelang berlangsung.',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    $listing2 = Listing::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Laptop Expired',
        'description' => 'Pengujian lelang berakhir.',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    $listing3 = Listing::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Laptop Closed',
        'description' => 'Pengujian lelang tertutup.',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    Auction::create([
        'listing_id' => $listing1->id,
        'starting_price' => 100000,
        'bid_increment' => 10000,
        'bid_cap' => 500000,
        'status' => 'active',
        'end_time' => now()->addHour(),
    ]);

    Auction::create([
        'listing_id' => $listing2->id,
        'starting_price' => 100000,
        'bid_increment' => 10000,
        'bid_cap' => 500000,
        'status' => 'active',
        'end_time' => now()->subMinute(),
    ]);

    Auction::create([
        'listing_id' => $listing3->id,
        'starting_price' => 100000,
        'bid_increment' => 10000,
        'bid_cap' => 500000,
        'status' => 'closed',
        'end_time' => now()->addHour(),
    ]);

    $ongoingAuctions = Auction::ongoing()->get();

    $this->assertCount(1, $ongoingAuctions);
    $this->assertEquals('Laptop Ongoing', $ongoingAuctions->first()->listing->title);
}
}
