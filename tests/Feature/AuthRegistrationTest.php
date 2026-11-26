<?php

namespace Tests\Feature;

use App\Mail\OtpVerificationMail;
use App\Models\CampusDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_campus_email_can_register_and_receive_otp(): void
    {
        // Menyiapkan email palsu agar tidak mengirim email sungguhan
        Mail::fake();

        // Menyiapkan domain kampus resmi
        CampusDomain::create([
            'domain_name' => 'polinema.ac.id',
            'campus_name' => 'Politeknik Negeri Malang',
            'is_active' => true,
        ]);

        // Mengirim data registrasi melalui POST /register
        $response = $this->postJson('/register', [
            'name' => 'Mahasiswa Polinema',
            'email' => 'mahasiswa@polinema.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
        ]);

        // Memastikan respons registrasi berhasil
        $response->assertStatus(200)
            ->assertJsonPath('status', 'otp_required')
            ->assertJsonPath('email_type', 'campus');

        // Memastikan data registrasi tersimpan sementara di Cache
        $cacheKey = 'reg_otp_' . md5('mahasiswa@polinema.ac.id');
        $cachedData = Cache::get($cacheKey);

        $this->assertNotNull($cachedData);
        $this->assertSame('active', $cachedData['verification_status']);
        $this->assertSame('campus', $cachedData['email_type']);
        $this->assertMatchesRegularExpression('/^\d{6}$/', $cachedData['otp']);

        // Memastikan OTP dikirim melalui email
        Mail::assertSent(OtpVerificationMail::class);

        // Memastikan akun belum dibuat sebelum OTP diverifikasi
        $this->assertDatabaseMissing('users', [
            'email' => 'mahasiswa@polinema.ac.id',
        ]);
    }
    public function test_public_email_without_ktm_is_rejected(): void
    {
        Mail::fake();

        $email = 'mahasiswa@gmail.com';
        $cacheKey = 'reg_otp_' . md5($email);

        Cache::forget($cacheKey);

        $response = $this->postJson('/register', [
            'name' => 'Mahasiswa Umum',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error');

        $this->assertNull(Cache::get($cacheKey));

        Mail::assertNothingSent();

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }
    public function test_public_email_with_valid_ktm_can_register(): void
    {
        Mail::fake();
        Storage::fake('public');

        $email = 'mahasiswa@gmail.com';

        $response = $this->postJson('/register', [
            'name' => 'Mahasiswa Umum',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'ktm_file' => UploadedFile::fake()->image('ktm.jpg'),
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'otp_required')
            ->assertJsonPath('email_type', 'public');

        $cacheKey = 'reg_otp_' . md5($email);
        $cachedData = Cache::get($cacheKey);

        $this->assertNotNull($cachedData);
        $this->assertSame('pending', $cachedData['verification_status']);
        $this->assertSame('public', $cachedData['email_type']);

        $this->assertNotNull($cachedData['ktm_image_path']);
        Storage::disk('local')->assertExists($cachedData['ktm_image_path']);

        Mail::assertSent(OtpVerificationMail::class);

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    public function test_invalid_ktm_format_and_size_are_rejected(): void
    {
        Mail::fake();

        // Percobaan 1: Mengunggah file PDF
        $pdfResponse = $this->postJson('/register', [
            'name' => 'Mahasiswa PDF',
            'email' => 'mahasiswa.pdf@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'ktm_file' => UploadedFile::fake()->create(
                'ktm.pdf',
                100,
                'application/pdf'
            ),
        ]);

        $pdfResponse->assertStatus(422)
            ->assertJsonValidationErrors('ktm_file');

        // Percobaan 2: Mengunggah gambar lebih dari 2 MB
        $largeFileResponse = $this->postJson('/register', [
            'name' => 'Mahasiswa File Besar',
            'email' => 'mahasiswa.besar@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Politeknik Negeri Malang',
            'ktm_file' => UploadedFile::fake()->image('ktm-besar.jpg')->size(2049),
        ]);

        $largeFileResponse->assertStatus(422)
            ->assertJsonValidationErrors('ktm_file');

        // Memastikan tidak ada email OTP yang dikirim
        Mail::assertNothingSent();

        // Memastikan akun tidak dibuat
        $this->assertDatabaseMissing('users', [
            'email' => 'mahasiswa.pdf@gmail.com',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'mahasiswa.besar@gmail.com',
        ]);
    }

    public function test_registration_is_rejected_when_email_domain_does_not_match_campus(): void
    {
        Mail::fake();

        // Menyiapkan domain resmi Polinema
        CampusDomain::create([
            'domain_name' => 'polinema.ac.id',
            'campus_name' => 'Politeknik Negeri Malang',
            'is_active' => true,
        ]);

        $email = 'mahasiswa.mismatch@polinema.ac.id';
        $cacheKey = 'reg_otp_' . md5($email);

        // Pengguna memilih kampus yang berbeda dari pemilik domain email
        $response = $this->postJson('/register', [
            'name' => 'Mahasiswa Mismatch',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'campus' => 'Universitas Brawijaya',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonFragment([
                'message' => 'Domain email @polinema.ac.id adalah milik Politeknik Negeri Malang, tidak sesuai dengan kampus Universitas Brawijaya yang Anda pilih.',
            ]);

        // Memastikan data registrasi tidak diproses
        $this->assertNull(Cache::get($cacheKey));

        Mail::assertNothingSent();

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }
}
