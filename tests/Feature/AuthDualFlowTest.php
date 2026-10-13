<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CampusDomain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthDualFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan domain kampus Polinema tersedia untuk pengujian
        CampusDomain::updateOrCreate(
            ['domain_name' => 'polinema.ac.id'],
            ['campus_name' => 'Politeknik Negeri Malang', 'is_active' => true]
        );
    }

    /**
     * Halaman Login dan Register dapat diakses dengan sukses.
     */
    public function test_auth_pages_can_be_rendered(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Login');
        $loginResponse->assertSee('Login dengan Google');

        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Buat akun');
        $registerResponse->assertSee('Daftar dengan Google');
        $registerResponse->assertSee('Unggah Foto Kartu Tanda Mahasiswa (KTM)');
    }

    /**
     * Pendaftaran dengan email kampus langsung aktif (status = active) dan diarahkan ke dashboard.
     */
    public function test_registration_with_campus_email_is_automatically_activated(): void
    {
        $uniqueEmail = 'campus_' . time() . '@student.polinema.ac.id';

        $response = $this->post('/register', [
            'name' => 'Mahasiswa Polinema',
            'email' => $uniqueEmail,
            'password' => 'Rahasia123!',
            'password_confirmation' => 'Rahasia123!',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '81234567890',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'email_type' => 'campus',
            'verification_status' => 'active',
        ]);

        $this->assertAuthenticated();

        // Pengguna aktif dapat melihat halaman dashboard
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Mahasiswa Polinema');
        $dashboardResponse->assertSee('Mahasiswa Terverifikasi');
    }

    /**
     * Pendaftaran dengan email umum wajib melampirkan foto KTM.
     */
    public function test_registration_with_public_email_requires_ktm_file(): void
    {
        $uniqueEmail = 'public_no_ktm_' . time() . '@gmail.com';

        $response = $this->post('/register', [
            'name' => 'Mahasiswa Umum',
            'email' => $uniqueEmail,
            'password' => 'Rahasia123!',
            'password_confirmation' => 'Rahasia123!',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '81234567891',
        ]);

        $response->assertSessionHasErrors('ktm_file');
        $this->assertGuest();
    }

    /**
     * Pendaftaran dengan email umum dan KTM valid berstatus pending dan file tersimpan di storage.
     */
    public function test_registration_with_public_email_and_ktm_is_pending(): void
    {
        Storage::fake('public');

        $uniqueEmail = 'public_with_ktm_' . time() . '@gmail.com';
        $file = UploadedFile::fake()->image('ktm_mahasiswa.jpg', 600, 400);

        $response = $this->post('/register', [
            'name' => 'Mahasiswa Umum Valid',
            'email' => $uniqueEmail,
            'password' => 'Rahasia123!',
            'password_confirmation' => 'Rahasia123!',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '81234567892',
            'ktm_file' => $file,
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('info');

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('public', $user->email_type);
        $this->assertEquals('pending', $user->verification_status);
        $this->assertNotNull($user->ktm_image_path);

        Storage::disk('public')->assertExists($user->ktm_image_path);
        $this->assertGuest();
    }

    /**
     * Pengguna dengan status verifikasi pending dilarang login.
     */
    public function test_pending_user_is_blocked_from_login(): void
    {
        $uniqueEmail = 'pending_user_' . time() . '@gmail.com';
        $user = User::create([
            'name' => 'Pending User',
            'email' => $uniqueEmail,
            'email_type' => 'public',
            'ktm_image_path' => 'ktm/fake.jpg',
            'verification_status' => 'pending',
            'password' => bcrypt('Rahasia123!'),
            'role' => 'buyer',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '81234567893',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $uniqueEmail,
            'password' => 'Rahasia123!',
        ]);

        $response->assertSessionHas('warning');
        $this->assertGuest();
    }

    /**
     * Pengguna dengan status pending tidak dapat mengakses /dashboard secara langsung.
     */
    public function test_pending_user_cannot_access_dashboard_directly(): void
    {
        $uniqueEmail = 'pending_direct_' . time() . '@gmail.com';
        $user = User::create([
            'name' => 'Pending Direct User',
            'email' => $uniqueEmail,
            'email_type' => 'public',
            'ktm_image_path' => 'ktm/fake.jpg',
            'verification_status' => 'pending',
            'password' => bcrypt('Rahasia123!'),
            'role' => 'buyer',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '81234567899',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHas('warning');
        $this->assertGuest();
    }

    /**
     * Pengguna aktif dapat login masuk ke dashboard dan kemudian logout dengan benar.
     */
    public function test_active_user_can_login_and_logout(): void
    {
        $uniqueEmail = 'active_user_' . time() . '@student.polinema.ac.id';
        $user = User::create([
            'name' => 'Active User',
            'email' => $uniqueEmail,
            'email_type' => 'campus',
            'verification_status' => 'active',
            'password' => bcrypt('Rahasia123!'),
            'role' => 'buyer',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '81234567894',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $uniqueEmail,
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Akses dashboard sukses 200
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Active User');

        // Uji Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        // Setelah logout, akses /dashboard dilarang (redirect ke login)
        $afterLogoutDashboard = $this->get('/dashboard');
        $afterLogoutDashboard->assertRedirect('/login');
    }
}
