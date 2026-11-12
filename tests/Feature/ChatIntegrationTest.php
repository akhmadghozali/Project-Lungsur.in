<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/chat');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_chat_page(): void
    {
        $user = User::factory()->create([
            'verification_status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/chat');
        $response->assertStatus(200);
        $response->assertSee('Ruang Obrolan');
    }

    public function test_user_can_start_conversation_and_send_message(): void
    {
        $seller = User::factory()->create([
            'name' => 'Dimas Penjual',
            'campus' => 'Polinema',
            'verification_status' => 'active',
        ]);

        $buyer = User::factory()->create([
            'name' => 'Rina Pembeli',
            'campus' => 'Universitas Brawijaya',
            'verification_status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
            'icon' => 'laptop',
        ]);

        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Rice Cooker Mini Kos',
            'description' => 'Kondisi mulus dan normal siap pakai.',
            'price' => 150000,
            'addresses' => 'Gerbang Suhat Polinema',
            'status' => 'available',
        ]);

        // 1. Buyer starts conversation
        $startResponse = $this->actingAs($buyer)->postJson('/chat/api/conversations', [
            'listing_id' => $listing->id,
            'message' => 'Halo kak, rice cookernya masih ada?',
        ]);

        $startResponse->assertStatus(201);
        $startResponse->assertJsonPath('status', 'success');
        $conversationId = $startResponse->json('data.conversation_id');

        $this->assertDatabaseHas('conversations', [
            'id' => $conversationId,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'listing_id' => $listing->id,
        ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversationId,
            'sender_id' => $buyer->id,
            'message_text' => 'Halo kak, rice cookernya masih ada?',
        ]);

        // 2. Seller sends reply message
        $replyResponse = $this->actingAs($seller)->postJson("/chat/api/conversations/{$conversationId}/messages", [
            'message_text' => 'Masih ada kak, mau COD di kampus mana?',
        ]);

        $replyResponse->assertStatus(201);
        $replyResponse->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversationId,
            'sender_id' => $seller->id,
            'message_text' => 'Masih ada kak, mau COD di kampus mana?',
        ]);

        // 3. Buyer fetches conversation history (should include both messages)
        $historyResponse = $this->actingAs($buyer)->getJson("/chat/api/conversations/{$conversationId}");
        $historyResponse->assertStatus(200);
        $historyResponse->assertJsonPath('status', 'success');
        $historyResponse->assertJsonCount(2, 'data.messages');

        // 4. Listing all conversations
        $listResponse = $this->actingAs($buyer)->getJson('/chat/api/conversations');
        $listResponse->assertStatus(200);
        $listResponse->assertJsonCount(1, 'data');
        $listResponse->assertJsonPath('data.0.interlocutor.name', 'Dimas Penjual');
    }

    public function test_user_cannot_chat_on_own_listing(): void
    {
        $seller = User::factory()->create([
            'verification_status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Buku',
            'slug' => 'buku',
        ]);

        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Buku Kalkulus',
            'description' => 'Edisi 8',
            'price' => 50000,
            'addresses' => 'Kantin UB',
            'status' => 'available',
        ]);

        $response = $this->actingAs($seller)->postJson('/chat/api/conversations', [
            'listing_id' => $listing->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');
    }

    public function test_non_participant_cannot_view_conversation(): void
    {
        $seller = User::factory()->create(['verification_status' => 'active']);
        $buyer = User::factory()->create(['verification_status' => 'active']);
        $stranger = User::factory()->create(['verification_status' => 'active']);

        $category = Category::create(['name' => 'Pakaian', 'slug' => 'pakaian']);
        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'title' => 'Jaket Polinema',
            'description' => 'Size L',
            'price' => 75000,
            'addresses' => 'Gedung AH Polinema',
            'status' => 'available',
        ]);

        $conversation = Conversation::create([
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($stranger)->getJson("/chat/api/conversations/{$conversation->id}");
        $response->assertStatus(403);
    }
}
