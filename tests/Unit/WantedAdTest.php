<?php

namespace Tests\Unit;

use App\Models\WantedAd;
use Tests\TestCase;

class WantedAdTest extends TestCase
{
    public function test_active_scope_filters_active_wanted_ads(): void
    {
        $query = WantedAd::query()->active();

        $this->assertStringContainsString(
            'status',
            $query->toSql()
        );

        $this->assertEquals(['active'], $query->getBindings());
    }

    public function test_formatted_budget_returns_rupiah_format(): void
    {
        $wantedAd = new WantedAd([
            'budget_max' => 250000,
        ]);

        $this->assertEquals(
            'Rp 250.000',
            $wantedAd->formatted_budget
        );
    }
}