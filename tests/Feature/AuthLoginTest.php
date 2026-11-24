<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        // Menyiapkan akun pengguna aktif
        $user = User::factory()->create([
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
            'verification_status' => 'active',
        ]);

        // Mengirim data login dengan kredensial yang benar
        $response = $this->postJson('/login', [
            'email' => 'mahasiswa@example.com',
            'password' => 'password123',
        ]);

        // Memastikan login berhasil
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Login berhasil.');

        // Memastikan pengguna berhasil diautentikasi
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        // Menyiapkan akun pengguna
        User::factory()->create([
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Mengirim login dengan password yang salah
        $response = $this->postJson('/login', [
            'email' => 'mahasiswa@example.com',
            'password' => 'passwordsalah',
        ]);

        // Memastikan login ditolak
        $response->assertStatus(401)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath(
                'message',
                'Email atau kata sandi yang Anda masukkan tidak sesuai.'
            );

        // Memastikan pengguna tidak berhasil login
        $this->assertGuest();
    }
        public function test_login_rejects_empty_email(): void
    {
        $response = $this->postJson('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }
    public function test_login_rejects_invalid_email_format(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'email-tidak-valid',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }
    public function test_suspended_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => bcrypt('password123'),
            'status' => 'suspended',
            'verification_status' => 'active',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'suspended@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath(
                'message',
                'Akun Anda ditangguhkan karena pelanggaran ketentuan platform. Silakan hubungi admin.'
            );

        $this->assertGuest();
    }
    public function test_banned_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'banned@example.com',
            'password' => bcrypt('password123'),
            'status' => 'banned',
            'verification_status' => 'active',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'banned@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath(
                'message',
                'Akun Anda ditangguhkan karena pelanggaran ketentuan platform. Silakan hubungi admin.'
            );

        $this->assertGuest();
    }
    public function test_pending_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'pending@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
            'verification_status' => 'pending',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('status', 'pending_verification');

        $this->assertGuest();
    }
    public function test_rejected_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'rejected@example.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
            'verification_status' => 'rejected',
            'rejection_reason' => 'Foto KTM tidak jelas.',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'rejected@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('status', 'rejected')
            ->assertJsonPath(
                'rejection_reason',
                'Foto KTM tidak jelas.'
            );

        $this->assertGuest();
    }
    public function test_admin_can_login_successfully(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
            'verification_status' => 'active',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('redirect', route('admin.verifications.index'));

        $this->assertAuthenticatedAs($admin);
    }
}
