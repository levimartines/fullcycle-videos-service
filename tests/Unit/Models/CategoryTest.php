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
        foreach ($traits as $trait) {
            $this->assertContains($trait, $categoryTraits);
        }
    }

    public function test_casts()
    {
        $casts = ['id' => "string", 'deleted_at' => "datetime"];
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
        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }

}
