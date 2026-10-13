<?php

namespace Tests\Feature;

use App\Models\CampusDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthValidationEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        CampusDomain::create([
            'domain_name' => 'polinema.ac.id',
            'campus_name' => 'Politeknik Negeri Malang',
            'is_active' => true,
        ]);
        CampusDomain::create([
            'domain_name' => 'ub.ac.id',
            'campus_name' => 'Universitas Brawijaya',
            'is_active' => true,
        ]);
    }

    /**
     * Skenario 1: Nama yang sama DIPERBOLEHKAN register selama email & no HP berbeda.
     */
    public function test_same_name_can_register_with_different_email_and_phone()
    {
        User::create([
            'name' => 'Ahmad Rizky',
            'email' => 'rizky1@student.polinema.ac.id',
            'email_type' => 'campus',
            'verification_status' => 'active',
            'password' => Hash::make('password123'),
            'role' => 'buyer',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $response = $this->post('/register', [
            'name' => 'Ahmad Rizky', // Nama persis sama
            'email' => 'rizky2@student.polinema.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567891',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseCount('users', 2);
    }

    /**
     * Skenario 2: Email yang sudah pernah terdaftar DITOLAK.
     */
    public function test_duplicate_email_is_rejected()
    {
        User::create([
            'name' => 'Mahasiswa Lama',
            'email' => 'existing@student.polinema.ac.id',
            'email_type' => 'campus',
            'verification_status' => 'active',
            'password' => Hash::make('password123'),
            'role' => 'buyer',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $response = $this->post('/register', [
            'name' => 'Mahasiswa Baru',
            'email' => 'existing@student.polinema.ac.id', // Email duplikat
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567891',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Skenario 3: Nomor HP yang sudah pernah terdaftar DITOLAK.
     */
    public function test_duplicate_phone_is_rejected()
    {
        User::create([
            'name' => 'Akun Pertama',
            'email' => 'user1@student.polinema.ac.id',
            'email_type' => 'campus',
            'verification_status' => 'active',
            'password' => Hash::make('password123'),
            'role' => 'buyer',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $response = $this->post('/register', [
            'name' => 'Akun Kedua',
            'email' => 'user2@student.polinema.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567890', // Nomor HP sama persis
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Skenario 5a: Email tanpa domain valid (misal @student) ditolak validasi format email.
     */
    public function test_invalid_email_format_is_rejected()
    {
        $response = $this->post('/register', [
            'name' => 'User Invalid',
            'email' => 'budi@student', // Tanpa TLD
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567899',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Skenario 5b: Email dengan domain .student ngasal (tanpa .ac.id & tidak di whitelist)
     * diperlakukan sebagai email umum dan WAJIB KTM.
     */
    public function test_random_student_email_treated_as_public_and_requires_ktm()
    {
        // 1. Tanpa KTM -> Ditolak
        $responseNoKtm = $this->post('/register', [
            'name' => 'User Ngasal',
            'email' => 'budi@student.randomfakedomain.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567899',
        ]);

        $responseNoKtm->assertSessionHasErrors(['ktm_file']);

        // 2. Dengan KTM -> Berhasil sebagai pending
        $file = UploadedFile::fake()->image('ktm_ngasal.jpg', 600, 400);
        $responseWithKtm = $this->post('/register', [
            'name' => 'User Ngasal',
            'email' => 'budi@student.randomfakedomain.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567899',
            'ktm_file' => $file,
        ]);

        $responseWithKtm->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'budi@student.randomfakedomain.com',
            'email_type' => 'public',
            'verification_status' => 'pending',
        ]);
    }

    /**
     * Skenario 6: Login dengan email atau password salah memberikan pesan aman.
     */
    public function test_login_with_wrong_credentials_fails_with_generic_message()
    {
        User::create([
            'name' => 'User Nyata',
            'email' => 'real@student.polinema.ac.id',
            'email_type' => 'campus',
            'verification_status' => 'active',
            'password' => Hash::make('password_benar123'),
            'role' => 'buyer',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        // Email salah
        $responseWrongEmail = $this->post('/login', [
            'email' => 'salah@student.polinema.ac.id',
            'password' => 'password_benar123',
        ]);
        $responseWrongEmail->assertSessionHasErrors(['email']);

        // Password salah
        $responseWrongPassword = $this->post('/login', [
            'email' => 'real@student.polinema.ac.id',
            'password' => 'password_salah456',
        ]);
        $responseWrongPassword->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Skenario 9: Password kurang dari 8 karakter atau konfirmasi tidak cocok ditolak.
     */
    public function test_password_length_and_confirmation_rules()
    {
        // Kurang dari 8 karakter
        $responseShort = $this->post('/register', [
            'name' => 'User Short PW',
            'email' => 'short@student.polinema.ac.id',
            'password' => 'pass1', // 5 char
            'password_confirmation' => 'pass1',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567890',
        ]);
        $responseShort->assertSessionHasErrors(['password']);

        // Konfirmasi tidak cocok
        $responseMismatch = $this->post('/register', [
            'name' => 'User Mismatch PW',
            'email' => 'mismatch@student.polinema.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'passwordBEDA',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567890',
        ]);
        $responseMismatch->assertSessionHasErrors(['password']);
    }

    /**
     * Skenario 4: Kampus dan domain email berbeda DITOLAK sistem.
     */
    public function test_campus_and_email_domain_mismatch_is_rejected()
    {
        // 1. Pilih Universitas Brawijaya tapi email @student.polinema.ac.id -> DITOLAK
        $response = $this->post('/register', [
            'name' => 'Budi Mismatch',
            'email' => 'budi@student.polinema.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Universitas Brawijaya', // Kampus beda dengan domain!
            'phone' => '081234567890',
        ]);

        $response->assertSessionHasErrors(['campus']);
        $this->assertDatabaseCount('users', 0);

        // 2. Pilih Politeknik Negeri Malang tapi email @student.ub.ac.id -> DITOLAK
        $response2 = $this->post('/register', [
            'name' => 'Siti Mismatch',
            'email' => 'siti@student.ub.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang', // Kampus beda dengan domain!
            'phone' => '081234567891',
        ]);

        $response2->assertSessionHasErrors(['campus']);
        $this->assertDatabaseCount('users', 0);

        // 3. Kampus dan email cocok (Politeknik Negeri Malang & @student.polinema.ac.id) -> DITERIMA
        $responseMatch = $this->post('/register', [
            'name' => 'Ahmad Match',
            'email' => 'ahmad@student.polinema.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'phone' => '081234567892',
        ]);

        $responseMatch->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@student.polinema.ac.id',
            'campus' => 'Politeknik Negeri Malang',
            'verification_status' => 'active',
        ]);
    }
}
