<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function test_category_relationships_return_has_many_relations(): void
    {
        $category = new Category();

        $this->assertInstanceOf(HasMany::class, $category->listings());
        $this->assertInstanceOf(HasMany::class, $category->wantedAds());
    }
}