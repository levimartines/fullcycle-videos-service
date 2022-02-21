<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;
    private $uuid_regex = '/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/';

    public function test_list()
    {
        Genre::factory()->create();
        $result = Genre::all();

        $this->assertCount(1, $result);
        $genreKey = array_keys($result->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id', 'name', 'created_at', 'updated_at', 'deleted_at', 'is_active'
        ], $genreKey);
    }

    public function test_create()
    {
        $genre = Genre::create(['name' => 'test']);
        $genre->refresh();

        $this->assertEquals(36, strlen($genre->id));
        $this->assertMatchesRegularExpression($this->uuid_regex, $genre->id);
        $this->assertEquals('test', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create(['name' => 'test', 'is_active' => false]);
        $this->assertFalse($genre->is_active);

        $genre = Genre::create(['name' => 'test', 'is_active' => true]);
        $this->assertTrue($genre->is_active);
    }

    public function test_update()
    {
        $genre = Genre::factory()->create([
            'is_active' => false
        ]);
        $data = [
            'name' => 'updated name',
            'is_active' => true
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function test_delete()
    {
        $genre = Genre::factory()->create();
        $this->assertNull($genre->deleted_at);

        $genre->delete();
        $this->assertNotNull($genre->deleted_at);
    }
}
