<?php

namespace Tests\Unit;

use App\Models\Conversation;
use App\Models\User;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    public function test_conversation_participant_methods_work_correctly(): void
    {
        $buyer = new User();
        $buyer->id = 1;

        $seller = new User();
        $seller->id = 2;

        $conversation = new Conversation([
            'buyer_id' => 1,
            'seller_id' => 2,
        ]);

        $conversation->setRelation('buyer', $buyer);
        $conversation->setRelation('seller', $seller);

        // Menguji isParticipant()
        $this->assertTrue($conversation->isParticipant(1));
        $this->assertTrue($conversation->isParticipant(2));
        $this->assertFalse($conversation->isParticipant(3));

        // Menguji getOtherParticipant()
        $this->assertSame($seller, $conversation->getOtherParticipant(1));
        $this->assertSame($buyer, $conversation->getOtherParticipant(2));
        $this->assertSame($buyer, $conversation->getOtherParticipant(3));
    }
}