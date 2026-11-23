<?php

namespace Tests\Unit;

use App\Http\Requests\StoreListingRequest;
use App\Models\User;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class StoreListingRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_create_listing(): void
    {
        // Membuat pengguna dengan role seller
        $seller = User::factory()->create([
            'role' => 'seller',
        ]);

        // Login sebagai seller
        $this->actingAs($seller);

        // Mengatur mode aktif sebagai seller
        $this->withSession([
            'user_mode' => 'seller',
        ]);

        // Membuat Form Request untuk proses pembuatan iklan
        $request = StoreListingRequest::create(
            '/iklan',
            'POST'
        );

        // Memastikan seller memiliki hak akses
        $this->assertTrue($request->authorize());
    }
    public function test_buyer_cannot_create_listing(): void
{
    // Membuat pengguna dengan role buyer
    $buyer = User::factory()->create([
        'role' => 'buyer',
    ]);

    // Login sebagai buyer
    $this->actingAs($buyer);

    // Mengatur mode aktif sebagai buyer
    $this->withSession([
        'user_mode' => 'buyer',
    ]);

    // Membuat Form Request
    $request = StoreListingRequest::create(
        '/iklan',
        'POST'
    );

    // Memastikan buyer tidak memiliki hak akses
    $this->assertFalse($request->authorize());
}
public function test_guest_cannot_create_listing(): void
{
    // Memastikan tidak ada pengguna yang login
    auth()->logout();

    // Membuat Form Request
    $request = StoreListingRequest::create(
        '/iklan',
        'POST'
    );

    // Memastikan guest tidak memiliki hak akses
    $this->assertFalse($request->authorize());
}
    public function test_seller_can_edit_own_listing(): void
{
    // Membuat seller
    $seller = User::factory()->create([
        'role' => 'seller',
    ]);

    $this->actingAs($seller);

    $this->withSession([
        'user_mode' => 'seller',
    ]);

    // Membuat kategori
    $category = Category::create([
        'name' => 'Elektronik',
        'slug' => 'elektronik',
    ]);

    // Membuat iklan milik seller
    $listing = Listing::create([
        'user_id' => $seller->id,
        'category_id' => $category->id,
        'title' => 'Laptop Bekas',
        'description' => 'Laptop masih berfungsi dengan baik',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    // Membuat Form Request untuk proses edit iklan
    $request = StoreListingRequest::create(
        '/iklan/' . $listing->id,
        'PUT'
    );

    // Mengatur route parameter listing
    $route = new \Illuminate\Routing\Route(
        ['PUT'],
        '/iklan/{listing}',
        []
    );

   // Mengikat route ke request terlebih dahulu
    $route->bind($request);

    // Mengatur parameter listing
    $route->setParameter('listing', $listing);

    $request->setRouteResolver(function () use ($route) {
        return $route;
    });

    // Memastikan seller dapat mengakses iklannya sendiri
    $this->assertTrue($request->authorize());
}
public function test_valid_price_is_accepted(): void
{
    // Membuat Form Request untuk pembuatan iklan
    $request = StoreListingRequest::create(
        '/iklan',
        'POST'
    );

    // Mengambil aturan validasi harga
    $rules = $request->rules();

    // Menguji harga Rp35.000
    $validator = Validator::make(
        ['price' => 35000],
        ['price' => $rules['price']]
    );

    // Memastikan harga dinyatakan valid
    $this->assertFalse($validator->fails());
}
public function test_price_below_minimum_is_rejected(): void
{
    // Membuat Form Request
    $request = StoreListingRequest::create('/iklan', 'POST');

    // Mengambil aturan validasi harga
    $rules = $request->rules();

    // Menguji harga di bawah minimum
    $validator = Validator::make(
        ['price' => 500],
        ['price' => $rules['price']]
    );

    // Memastikan harga ditolak
    $this->assertTrue($validator->fails());
}
public function test_price_not_multiple_of_1000_is_rejected(): void
{
    // Membuat Form Request
    $request = StoreListingRequest::create('/iklan', 'POST');

    // Mengambil aturan validasi harga
    $rules = $request->rules();

    // Menguji harga yang bukan kelipatan Rp1.000
    $validator = Validator::make(
        ['price' => 27350],
        ['price' => $rules['price']]
    );

    // Memastikan harga ditolak
    $this->assertTrue($validator->fails());
}
public function test_seller_cannot_edit_other_seller_listing(): void
{
    // Membuat dua seller
    $sellerA = User::factory()->create([
        'role' => 'seller',
    ]);

    $sellerB = User::factory()->create([
        'role' => 'seller',
    ]);

    // Login sebagai Seller A
    $this->actingAs($sellerA);

    $this->withSession([
        'user_mode' => 'seller',
    ]);

    // Membuat kategori
    $category = Category::create([
        'name' => 'Elektronik',
        'slug' => 'elektronik',
    ]);

    // Membuat iklan milik Seller B
    $listing = Listing::create([
        'user_id' => $sellerB->id,
        'category_id' => $category->id,
        'title' => 'Laptop Bekas',
        'description' => 'Laptop masih berfungsi',
        'price' => 1000000,
        'addresses' => 'Malang',
        'status' => 'available',
    ]);

    // Membuat Form Request untuk edit iklan
    $request = StoreListingRequest::create(
        '/iklan/' . $listing->id,
        'PUT'
    );

    // Mengatur route parameter
    $route = new \Illuminate\Routing\Route(
        ['PUT'],
        '/iklan/{listing}',
        []
    );

    $route->bind($request);
    $route->setParameter('listing', $listing);

    $request->setRouteResolver(function () use ($route) {
        return $route;
    });

    // Memastikan Seller A tidak dapat mengakses iklan Seller B
    $this->assertFalse($request->authorize());
}
public function test_zero_price_is_rejected(): void
{
    $request = StoreListingRequest::create('/iklan', 'POST');
    $rules = $request->rules();

    $validator = Validator::make(
        ['price' => 0],
        ['price' => $rules['price']]
    );

    $this->assertTrue($validator->fails());
}
public function test_negative_price_is_rejected(): void
{
    $request = StoreListingRequest::create('/iklan', 'POST');
    $rules = $request->rules();

    $validator = Validator::make(
        ['price' => -10000],
        ['price' => $rules['price']]
    );

    $this->assertTrue($validator->fails());
}
}
