<?php

namespace Tests\Unit;

use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;
    public function test_listing_boost_status_is_checked_correctly(): void
    {
        $activeBoost = new Listing([
            'is_boosted' => true,
            'boosted_until' => now()->addDay(),
        ]);

        $permanentBoost = new Listing([
            'is_boosted' => true,
            'boosted_until' => null,
        ]);

        $expiredBoost = new Listing([
            'is_boosted' => true,
            'boosted_until' => now()->subDay(),
        ]);

        $notBoosted = new Listing([
            'is_boosted' => false,
        ]);

        $this->assertTrue($activeBoost->isCurrentlyBoosted());
        $this->assertTrue($permanentBoost->isCurrentlyBoosted());
        $this->assertFalse($expiredBoost->isCurrentlyBoosted());
        $this->assertFalse($notBoosted->isCurrentlyBoosted());
    }
    public function test_listing_scopes_filter_records_correctly(): void
{
    $user = \App\Models\User::factory()->create();

    $category = \App\Models\Category::create([
        'name' => 'Elektronik',
        'slug' => 'elektronik',
    ]);

    $createListing = function (string $status, bool $boosted, $until = null) use ($user, $category) {
        return \App\Models\Listing::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Produk Test',
            'description' => 'Deskripsi pengujian.',
            'price' => 100000,
            'addresses' => 'Malang',
            'status' => $status,
            'is_boosted' => $boosted,
            'boosted_until' => $until,
        ]);
    };

    $activeBoost = $createListing('available', true, now()->addDay());
    $permanentBoost = $createListing('available', true, null);
    $normalListing = $createListing('available', false);
    $expiredBoost = $createListing('available', true, now()->subDay());
    $soldListing = $createListing('sold', false);

    $this->assertEqualsCanonicalizing(
        [$activeBoost->id, $permanentBoost->id, $normalListing->id, $expiredBoost->id],
        \App\Models\Listing::available()->pluck('id')->all()
    );

    $this->assertEqualsCanonicalizing(
        [$activeBoost->id, $permanentBoost->id],
        \App\Models\Listing::boosted()->pluck('id')->all()
    );

    $this->assertEqualsCanonicalizing(
        [$normalListing->id, $expiredBoost->id, $soldListing->id],
        \App\Models\Listing::notBoosted()->pluck('id')->all()
    );
}
}