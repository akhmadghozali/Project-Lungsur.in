<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'verification_status' => 'active',
        ]);

        $this->student = User::factory()->create([
            'role' => 'seller',
            'status' => 'active',
            'verification_status' => 'active',
        ]);
    }

    public function test_guest_is_redirected_when_accessing_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard_and_see_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dasbor Analitik Bisnis');
        $response->assertSee('Kategori Barang Paling Banyak Diiklankan');
        $response->assertSee('Tren Penerimaan Biaya Mikro');
        $response->assertSee('Tren Rata-rata Harga Pasar Barang Bekas');
    }

    public function test_admin_can_filter_dashboard_by_period(): void
    {
        // Seed users with various statuses to ensure table rendering doesn't throw RouteNotFoundException
        User::factory()->create(['status' => 'banned', 'role' => 'seller']);
        User::factory()->create(['verification_status' => 'pending', 'role' => 'buyer']);
        User::factory()->create(['status' => 'active', 'verification_status' => 'active', 'role' => 'seller']);

        foreach (['7d', '30d', 'month', 'all'] as $period) {
            $response = $this->actingAs($this->admin)->get("/admin/dashboard?period={$period}");
            $response->assertStatus(200);
            $response->assertViewHas('period', $period);
        }
    }

    public function test_admin_route_redirects_to_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_admin_dashboard_shows_correct_wib_print_timestamp(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $expectedTimestamp = now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';
        $response->assertSee($expectedTimestamp);
    }
}

