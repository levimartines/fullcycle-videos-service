<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function test_fillable()
    {
        $category = new Category();
        $this->assertEquals(
            ['name', 'description', 'is_active'],
            $category->getFillable()
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
        $category = new Category();
        $this->assertEquals($casts, $category->getCasts());
    }

    public function test_increment()
    {
        $category = new Category();
        $this->assertFalse($category->getIncrementing());
    }

    public function test_dates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $category = new Category();
        foreach ($dates as $date) {
            $this->assertContains($date, $category->getDates());
        }
        $this->assertCount(count($dates), $category->getDates());
    }

}
