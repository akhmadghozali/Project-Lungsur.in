<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureUserIsAdminTest extends TestCase
{
    use RefreshDatabase;

    
public function test_admin_can_pass_admin_middleware(): void
{
    // Membuat pengguna dengan role admin
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    // Membuat request
    $request = Request::create('/admin/verifications', 'GET');

    // Menghubungkan pengguna admin dengan request
    $request->setUserResolver(function () use ($admin) {
        return $admin;
    });

    // Menjalankan middleware
    $middleware = new EnsureUserIsAdmin();

    $response = $middleware->handle(
        $request,
        function ($request) {
            return response('Access granted', 200);
        }
    );

    // Memastikan request diteruskan
    $this->assertEquals(200, $response->getStatusCode());
}
    public function test_seller_cannot_pass_admin_middleware(): void
{
    // Membuat pengguna dengan role seller
    $seller = User::factory()->create([
        'role' => 'seller',
    ]);

    // Membuat request
    $request = Request::create('/admin/verifications', 'GET');

    // Menghubungkan pengguna seller dengan request
    $request->setUserResolver(function () use ($seller) {
        return $seller;
    });

    // Menjalankan middleware
    $middleware = new EnsureUserIsAdmin();

    // Memastikan middleware menolak akses
    try {
        $middleware->handle($request, function ($request) {
            return response('Access granted', 200);
        });

        $this->fail('Seller seharusnya tidak dapat mengakses halaman admin.');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        $this->assertEquals(403, $e->getStatusCode());
    }
}
public function test_guest_cannot_pass_admin_middleware(): void
{
    // Membuat request tanpa pengguna login
    $request = Request::create('/admin/verifications', 'GET');

    // Menjalankan middleware
    $middleware = new EnsureUserIsAdmin();

    // Memastikan middleware menolak akses
    try {
        $middleware->handle($request, function ($request) {
            return response('Access granted', 200);
        });

        $this->fail('Guest seharusnya tidak dapat mengakses halaman admin.');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        $this->assertEquals(403, $e->getStatusCode());
    }
}
}
