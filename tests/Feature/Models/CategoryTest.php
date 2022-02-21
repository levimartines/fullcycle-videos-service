<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    private $uuid_regex = '/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/';

    public function test_list()
    {
        Category::factory()->create();
        $result = Category::all();

        $this->assertCount(1, $result);
        $categoryKey = array_keys($result->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id', 'name', 'description', 'created_at', 'updated_at', 'deleted_at', 'is_active'
        ], $categoryKey);
    }

    public function test_create()
    {
        $category = Category::create(['name' => 'test']);
        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertMatchesRegularExpression($this->uuid_regex, $category->id);
        $this->assertEquals('test', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create(['name' => 'test', 'description' => null]);
        $this->assertNull($category->description);

        $category = Category::create(['name' => 'test', 'description' => 'test']);
        $this->assertEquals('test', $category->description);

        $category = Category::create(['name' => 'test', 'is_active' => false]);
        $this->assertFalse($category->is_active);

        $category = Category::create(['name' => 'test', 'is_active' => true]);
        $this->assertTrue($category->is_active);
    }

    public function test_update()
    {
        $category = Category::factory()->create([
            'description' => 'test',
            'is_active' => false
        ]);
        $data = [
            'name' => 'updated name',
            'description' => 'updated description',
            'is_active' => true
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function test_delete()
    {
        $category = Category::factory()->create();
        $this->assertNull($category->deleted_at);

        $category->delete();
        $this->assertNotNull($category->deleted_at);
    }
}
