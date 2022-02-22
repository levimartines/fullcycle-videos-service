<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function test_show()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function test_store_invalid_data()
    {
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertNameRequired($response);

        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'test'
        ]);
        $this->assertNameMaxCharsAndIsActiveBoolean($response);

        $category = Category::factory()->create();

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);
        $this->assertNameRequired($response);


        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'test'
        ]);
        $this->assertNameMaxCharsAndIsActiveBoolean($response);

    }

    public function test_store()
    {
        $response = $this->json('POST', route('categories.store'), ['name' => 'test']);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test', 'is_active' => false, 'description' => 'test'
        ]);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertJsonFragment([
                'description' => 'test',
                'is_active' => false
            ]);
    }

    public function test_update()
    {
        $category = Category::factory()->create(['description' => 'test', 'is_active' => false]);
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]),
            ['name' => 'test', 'is_active' => true, 'description' => 'updated']);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertEquals('test', $response->json('name'));
        $this->assertEquals('updated', $response->json('description'));
    }

    public function test_destroy()
    {
        $category = Category::factory()->create();
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $category->id]));
        $response->assertStatus(204);

        $category = Category::find($category->id);
        $this->assertNull($category);
    }

    private function assertNameRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }

    private function assertNameMaxCharsAndIsActiveBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ])
            ->assertJsonFragment([
                \Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

}
