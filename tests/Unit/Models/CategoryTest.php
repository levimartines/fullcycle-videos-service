<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    public function test_fillable()
    {
        $this->assertEquals(
            ['name', 'description', 'is_active'],
            $this->category->getFillable()
        );
    }

    public function test_traits()
    {
        $traits = [HasFactory::class, SoftDeletes::class, Uuid::class];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEqualsCanonicalizing($traits, $categoryTraits);
    }

    public function test_casts()
    {
        $casts = ['id' => 'string', 'deleted_at' => 'datetime', 'is_active' => 'boolean'];
        $this->category = new Category();
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function test_increment()
    {
        $this->category = new Category();
        $this->assertFalse($this->category->getIncrementing());
    }

    public function test_dates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $this->category = new Category();
        $this->assertEqualsCanonicalizing($dates, $this->category->getDates());
    }

}
