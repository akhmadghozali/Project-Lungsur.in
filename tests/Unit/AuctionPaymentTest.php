<?php

namespace Tests\Unit;

use App\Models\AuctionPayment;
use PHPUnit\Framework\TestCase;

class AuctionPaymentTest extends TestCase
{
    public function test_payment_status_methods_return_correct_values(): void
    {
        $pending = new AuctionPayment(['status' => 'pending']);
        $verified = new AuctionPayment(['status' => 'verified']);
        $rejected = new AuctionPayment(['status' => 'rejected']);

        $this->assertTrue($pending->isPending());
        $this->assertTrue($verified->isVerified());
        $this->assertTrue($rejected->isRejected());
    }
}