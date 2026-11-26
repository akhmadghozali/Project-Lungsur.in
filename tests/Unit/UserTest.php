<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_active_mode_uses_session_or_role(): void
    {
        $seller = new User(['role' => 'seller']);
        $buyer = new User(['role' => 'buyer']);

        $this->assertEquals('seller', $seller->getActiveMode());
        $this->assertEquals('buyer', $buyer->getActiveMode());

        session(['user_mode' => 'buyer']);

        $this->assertEquals('buyer', $seller->getActiveMode());
    }

    public function test_user_role_mode_methods_work_correctly(): void
    {
        session(['user_mode' => 'seller']);

        $user = new User(['role' => 'buyer']);

        $this->assertTrue($user->isSeller());
        $this->assertFalse($user->isBuyer());

        session(['user_mode' => 'buyer']);

        $this->assertTrue($user->isBuyer());
        $this->assertFalse($user->isSeller());
    }
}