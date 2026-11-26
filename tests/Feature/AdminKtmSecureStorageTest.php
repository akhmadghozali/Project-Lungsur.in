<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminKtmSecureStorageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'role' => 'admin',
            'status' => 'active',
            'verification_status' => 'active',
        ]);

        $this->student = User::factory()->create([
            'name' => 'Mahasiswa Test',
            'role' => 'buyer',
            'status' => 'active',
            'verification_status' => 'pending',
            'ktm_image_path' => 'ktm/test_ktm.jpg',
        ]);
    }

    public function test_guest_is_redirected_when_accessing_ktm_image(): void
    {
        $response = $this->get(route('admin.verifications.ktm', $this->student));
        $response->assertRedirect('/login');
    }

    public function test_student_cannot_access_ktm_image(): void
    {
        $response = $this->actingAs($this->student)->get(route('admin.verifications.ktm', $this->student));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_ktm_from_private_local_disk(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('ktm_private.jpg');
        $path = $file->store('ktm', 'local');

        $this->student->update(['ktm_image_path' => $path]);

        $response = $this->actingAs($this->admin)->get(route('admin.verifications.ktm', $this->student));

        $response->assertStatus(200);
        $this->assertStringContainsString('private', (string) $response->headers->get('Cache-Control'));
    }

    public function test_admin_can_view_ktm_from_public_disk_backward_compatibility(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('ktm_public.jpg');
        $path = $file->store('ktm', 'public');

        $this->student->update(['ktm_image_path' => $path]);

        $response = $this->actingAs($this->admin)->get(route('admin.verifications.ktm', $this->student));

        $response->assertStatus(200);
    }

    public function test_admin_receives_fallback_svg_if_file_missing_on_new_deployment(): void
    {
        // Path exists in DB, but physical file does not exist on disk
        $this->student->update(['ktm_image_path' => 'ktm/non_existent_file.jpg']);

        $response = $this->actingAs($this->admin)->get(route('admin.verifications.ktm', $this->student));

        $response->assertStatus(200);
        $this->assertStringContainsString('image/svg+xml', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('KARTU TANDA MAHASISWA', $response->getContent());
        $this->assertStringContainsString($this->student->name, $response->getContent());
    }

    public function test_verification_index_page_renders_secure_ktm_route(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.verifications.index'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.verifications.ktm', $this->student));
    }
}
