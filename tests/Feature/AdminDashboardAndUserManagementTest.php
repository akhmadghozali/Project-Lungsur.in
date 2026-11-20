<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardAndUserManagementTest extends TestCase
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
        $response = $this->actingAs($this->admin)->get('/admin/dashboard?period=30d');

        $response->assertStatus(200);
        $response->assertViewHas('period', '30d');
    }

    public function test_admin_can_view_users_list_and_search(): void
    {
        $searchTarget = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@student.polinema.ac.id',
            'nim' => '244107060999',
            'campus' => 'Politeknik Negeri Malang',
            'role' => 'buyer',
            'status' => 'active',
            'verification_status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/users?q=Budi');

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('244107060999');

        $detailResponse = $this->actingAs($this->admin)->get("/admin/users/{$searchTarget->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Detail Profil Mahasiswa');
        $detailResponse->assertSee('Budi Santoso');
    }

    public function test_admin_can_suspend_user_with_reason(): void
    {
        $targetUser = User::factory()->create([
            'status' => 'active',
            'role' => 'buyer',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/users/{$targetUser->id}/suspend", [
            'reason' => 'Indikasi akun ganda dan spamming',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'status' => 'suspended',
            'rejection_reason' => 'Indikasi akun ganda dan spamming',
        ]);
    }

    public function test_admin_can_ban_user_and_auto_archive_active_listings(): void
    {
        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $seller = User::factory()->create([
            'status' => 'active',
            'role' => 'seller',
        ]);

        $activeListing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Dispenser Miyako Bekas',
            'description' => 'Masih bagus dingin normal',
            'price' => 75000,
            'addresses' => 'Jl. Soekarno Hatta No. 9, Malang',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/users/{$seller->id}/ban", [
            'reason' => 'Melakukan troll bidding dan percobaan penipuan COD',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $seller->id,
            'status' => 'banned',
            'rejection_reason' => 'Melakukan troll bidding dan percobaan penipuan COD',
        ]);

        $this->assertDatabaseHas('listings', [
            'id' => $activeListing->id,
            'status' => 'archived',
        ]);
    }

    public function test_banned_user_cannot_login(): void
    {
        $bannedUser = User::factory()->create([
            'email' => 'banned_student@student.polinema.ac.id',
            'password' => bcrypt('Password123!'),
            'status' => 'banned',
            'rejection_reason' => 'Pelanggaran ketentuan platform.',
        ]);

        $response = $this->post('/login', [
            'email' => 'banned_student@student.polinema.ac.id',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_admin_can_reactivate_user(): void
    {
        $bannedUser = User::factory()->create([
            'status' => 'banned',
            'rejection_reason' => 'Sanksi sementara 7 hari',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/users/{$bannedUser->id}/reactivate");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $bannedUser->id,
            'status' => 'active',
            'rejection_reason' => null,
        ]);
    }

    public function test_admin_can_ban_seller_directly_from_report(): void
    {
        $category = Category::create([
            'name' => 'Perabot Kos',
            'slug' => 'perabot-kos',
        ]);

        $reporter = User::factory()->create(['status' => 'active', 'verification_status' => 'active']);
        $badSeller = User::factory()->create(['status' => 'active', 'verification_status' => 'active']);

        $listing = Listing::create([
            'user_id' => $badSeller->id,
            'category_id' => $category->id,
            'title' => 'Kasur Busa Inoac Palsu',
            'description' => 'Iklan penipuan',
            'price' => 50000,
            'addresses' => 'Lowokwaru, Malang',
            'status' => 'available',
        ]);

        $report = Report::create([
            'reporter_id' => $reporter->id,
            'reported_listing_id' => $listing->id,
            'reported_user_id' => $badSeller->id,
            'reason' => 'Indikasi penipuan',
            'details' => 'Barang tidak sesuai dan minta transfer di awal',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/reports/{$report->id}/ban-seller", [
            'reason' => 'Terbukti modus penipuan transfer awal',
        ]);

        $response->assertRedirect('/admin/reports');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $badSeller->id,
            'status' => 'banned',
            'rejection_reason' => 'Terbukti modus penipuan transfer awal',
        ]);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
        ]);
    }
}
