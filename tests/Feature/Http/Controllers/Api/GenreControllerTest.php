<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index()
    {
        $genre = Genre::factory()->create();
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }

    public function test_show()
    {
        $genre = Genre::factory()->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function test_store_invalid_data()
    {
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertNameRequired($response);

        $response = $this->json('POST', route('genres.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'test'
        ]);
        $this->assertNameMaxCharsAndIsActiveBoolean($response);

        $genre = Genre::factory()->create();

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), []);
        $this->assertNameRequired($response);


        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'test'
        ]);
        $this->assertNameMaxCharsAndIsActiveBoolean($response);

    }

    public function test_store()
    {
        $response = $this->json('POST', route('genres.store'), ['name' => 'test']);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test', 'is_active' => false
        ]);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertJsonFragment([
                'is_active' => false
            ]);
    }

    public function test_update()
    {
        $genre = Genre::factory()->create(['is_active' => false]);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]),
            ['name' => 'test', 'is_active' => true]);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertEquals('test', $response->json('name'));
    }

    public function test_destroy()
    {
        $genre = Genre::factory()->create();
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]));
        $response->assertStatus(204);

        $genre = Genre::find($genre->id);
        $this->assertNull($genre);
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
