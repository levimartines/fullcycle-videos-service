<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{
    private Genre $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function test_fillable()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals($fillable, $this->genre->getFillable());
    }

    public function test_traits()
    {
        $genreTraits = array_keys(class_uses(Genre::class));
        $traits = [HasFactory::class, SoftDeletes::class, Uuid::class];
        $this->assertEqualsCanonicalizing($traits, $genreTraits);
    }

    public function test_casts()
    {
        $casts = ['id' => 'string', 'deleted_at' => 'datetime', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->genre->getCasts());
    }

    public function test_increment()
    {
        $this->assertFalse($this->genre->getIncrementing());
    }

    public function test_dates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $this->assertEqualsCanonicalizing($dates, $this->genre->getDates());
    }

}
