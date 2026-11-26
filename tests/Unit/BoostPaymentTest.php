<?php

namespace Tests\Unit;

use App\Models\BoostPayment;
use PHPUnit\Framework\TestCase;

class BoostPaymentTest extends TestCase
{
    public function test_payment_status_methods_return_correct_values(): void
    {
        $pending = new BoostPayment(['status' => 'pending']);
        $verified = new BoostPayment(['status' => 'verified']);
        $rejected = new BoostPayment(['status' => 'rejected']);

        $this->assertTrue($pending->isPending());
        $this->assertTrue($verified->isVerified());
        $this->assertTrue($rejected->isRejected());
    }
}