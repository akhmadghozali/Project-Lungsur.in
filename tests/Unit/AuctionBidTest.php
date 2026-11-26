<?php

namespace Tests\Unit;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionBidTest extends TestCase
{
    use RefreshDatabase;

    public function test_highest_first_scope_orders_bids_from_highest_to_lowest(): void
    {
        $seller = User::factory()->create();
        $bidder1 = User::factory()->create();
        $bidder2 = User::factory()->create();

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
            'user_id' => $bidder1->id,
            'bid_amount' => 150000,
        ]);

        $auction->bids()->create([
            'user_id' => $bidder2->id,
            'bid_amount' => 200000,
        ]);

        $bids = AuctionBid::highestFirst()->get();

        $this->assertEquals(200000, (float) $bids->first()->bid_amount);
        $this->assertEquals(150000, (float) $bids->last()->bid_amount);
    }
}