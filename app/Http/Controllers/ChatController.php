<?php

namespace App\Http\Controllers;

use App\Events\ChatSent;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Mendapatkan user yang sedang aktif (mendukung session auth & header X-User-Id di testing/local).
     */
    protected function resolveUser(Request $request): ?User
    {
        $user = $request->user();
        if (!$user && (app()->environment('local', 'testing')) && $request->hasHeader('X-User-Id')) {
            $user = User::find($request->header('X-User-Id'));
        }

        return $user;
    }

    /**
     * Mendapatkan daftar percakapan aktif yang melibatkan pengguna saat ini.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan masuk (login) terlebih dahulu.',
            ], 401);
        }

        $conversations = Conversation::with([
            'listing:id,title,price,status',
            'buyer:id,name,campus',
            'seller:id,name,campus',
            'latestMessage.sender:id,name',
        ])
        ->where(function ($query) use ($user) {
            $query->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
        })
        ->orderByDesc('last_message_at')
        ->orderByDesc('created_at')
        ->get();

        $data = $conversations->map(function ($conversation) use ($user) {
            $otherParticipant = $conversation->getOtherParticipant($user->id);
            $unreadCount = Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $conversation->id,
                'listing' => [
                    'id' => $conversation->listing->id,
                    'title' => $conversation->listing->title,
                    'price' => (float) $conversation->listing->price,
                    'status' => $conversation->listing->status,
                ],
                'interlocutor' => $otherParticipant ? [
                    'id' => $otherParticipant->id,
                    'name' => $otherParticipant->name,
                    'campus' => $otherParticipant->campus,
                ] : null,
                'latest_message' => $conversation->latestMessage ? [
                    'id' => $conversation->latestMessage->id,
                    'sender_id' => $conversation->latestMessage->sender_id,
                    'sender_name' => $conversation->latestMessage->sender?->name,
                    'message_text' => $conversation->latestMessage->message_text,
                    'is_read' => $conversation->latestMessage->is_read,
                    'created_at' => $conversation->latestMessage->created_at?->toISOString(),
                ] : null,
                'unread_count' => $unreadCount,
                'last_message_at' => $conversation->last_message_at?->toISOString() ?? $conversation->created_at?->toISOString(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Memulai percakapan baru atau membuka percakapan yang sudah ada untuk suatu listing.
     */
    public function startConversation(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan masuk (login) terlebih dahulu.',
            ], 401);
        }

        if ($user->verification_status !== 'active') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Akun Anda harus terverifikasi aktif untuk dapat mengirim pesan.',
            ], 403);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Akun administrator tidak diperbolehkan menggunakan fitur obrolan transaksi.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'listing_id' => ['required', 'integer', 'exists:listings,id'],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [
            'listing_id.required' => 'ID barang listing wajib disertakan.',
            'listing_id.exists' => 'Barang listing tidak ditemukan.',
            'message.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $listing = Listing::with('user:id,name,campus')->findOrFail($request->listing_id);

        // Penjual tidak dapat chat dengan dirinya sendiri
        if ((int) $listing->user_id === (int) $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak dapat memulai percakapan pada barang jualan Anda sendiri.',
            ], 422);
        }

        // Cari atau buat percakapan
        $conversation = Conversation::firstOrCreate(
            [
                'listing_id' => $listing->id,
                'buyer_id' => $user->id,
                'seller_id' => $listing->user_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        $initialMessage = null;
        if ($request->filled('message')) {
            $msg = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message_text' => $request->message,
            ]);

            $conversation->update(['last_message_at' => now()]);
            $msg->load('sender:id,name,campus');

            try {
                broadcast(new ChatSent($msg))->toOthers();
            } catch (\Throwable $e) {
                report($e);
            }

            $initialMessage = [
                'id' => $msg->id,
                'message_text' => $msg->message_text,
                'created_at' => $msg->created_at->toISOString(),
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Percakapan berhasil dimulai.',
            'data' => [
                'conversation_id' => $conversation->id,
                'listing' => [
                    'id' => $listing->id,
                    'title' => $listing->title,
                    'price' => (float) $listing->price,
                ],
                'seller' => [
                    'id' => $listing->user->id,
                    'name' => $listing->user->name,
                    'campus' => $listing->user->campus,
                ],
                'initial_message' => $initialMessage,
            ],
        ], 201);
    }

    /**
     * Memuat riwayat pesan dalam satu percakapan.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan masuk (login) terlebih dahulu.',
            ], 401);
        }

        $conversation = Conversation::with([
            'listing:id,title,price,status',
            'buyer:id,name,campus',
            'seller:id,name,campus',
        ])->find($id);

        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Percakapan tidak ditemukan.',
            ], 404);
        }

        // Pastikan hanya peserta percakapan yang dapat membaca pesan
        if (!$conversation->isParticipant($user->id)) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Anda tidak memiliki akses ke percakapan ini.',
            ], 403);
        }

        // Tandai pesan dari lawan bicara sebagai terbaca
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()
            ->with('sender:id,name,campus')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender' => [
                        'id' => $msg->sender->id,
                        'name' => $msg->sender->name,
                        'campus' => $msg->sender->campus,
                    ],
                    'message_text' => $msg->message_text,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at?->toISOString(),
                ];
            });

        $otherParticipant = $conversation->getOtherParticipant($user->id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'conversation_id' => $conversation->id,
                'listing' => [
                    'id' => $conversation->listing->id,
                    'title' => $conversation->listing->title,
                    'price' => (float) $conversation->listing->price,
                    'status' => $conversation->listing->status,
                ],
                'interlocutor' => $otherParticipant ? [
                    'id' => $otherParticipant->id,
                    'name' => $otherParticipant->name,
                    'campus' => $otherParticipant->campus,
                ] : null,
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * Mengirim pesan baru ke percakapan dan menyiarkan event Pusher ChatSent.
     */
    public function sendMessage(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan masuk (login) terlebih dahulu.',
            ], 401);
        }

        if ($user->verification_status !== 'active') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Akun Anda harus terverifikasi aktif untuk dapat mengirim pesan.',
            ], 403);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Akun administrator tidak diperbolehkan menggunakan fitur obrolan transaksi.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message_text' => ['required', 'string', 'max:2000'],
        ], [
            'message_text.required' => 'Pesan tidak boleh kosong.',
            'message_text.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $conversation = Conversation::find($id);
        if (!$conversation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Percakapan tidak ditemukan.',
            ], 404);
        }

        // Otorisasi peserta percakapan
        if (!$conversation->isParticipant($user->id)) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Anda tidak memiliki hak akses untuk mengirim pesan di percakapan ini.',
            ], 403);
        }

        // Simpan pesan
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message_text' => $request->message_text,
            'is_read' => false,
        ]);

        // Perbarui waktu percakapan
        $conversation->update(['last_message_at' => now()]);

        $message->load('sender:id,name,campus');

        // Siarkan event Pusher realtime
        try {
            broadcast(new ChatSent($message))->toOthers();
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan berhasil terkirim.',
            'data' => [
                'id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'campus' => $user->campus,
                ],
                'message_text' => $message->message_text,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at?->toISOString(),
            ],
        ], 201);
    }

    /**
     * Menandai semua pesan dalam percakapan sebagai telah dibaca.
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan masuk (login) terlebih dahulu.',
            ], 401);
        }

        $conversation = Conversation::find($id);
        if (!$conversation || !$conversation->isParticipant($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Percakapan tidak ditemukan atau Anda bukan peserta.',
            ], 404);
        }

        $updatedCount = Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => "{$updatedCount} pesan ditandai telah dibaca.",
            'marked_count' => $updatedCount,
        ]);
    }
}
