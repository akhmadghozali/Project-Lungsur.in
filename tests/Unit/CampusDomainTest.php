<?php

namespace Tests\Unit;

use App\Models\CampusDomain;
use PHPUnit\Framework\TestCase;

class CampusDomainTest extends TestCase
{
    public function test_is_active_is_cast_to_boolean(): void
    {
        $active = new CampusDomain(['is_active' => 1]);
        $inactive = new CampusDomain(['is_active' => 0]);

        $this->assertTrue($active->is_active);
        $this->assertFalse($inactive->is_active);
    }
}