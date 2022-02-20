<?php

namespace Tests\Unit;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{
    public function test_fillable()
    {
        $genre = new Genre();
        $fillable = ['name', 'is_active'];
        $this->assertEquals($fillable, $genre->getFillable());
    }

    public function test_traits()
    {
        $genreTraits = array_keys(class_uses(Genre::class));
        $traits = [HasFactory::class, SoftDeletes::class, Uuid::class];
        foreach ($traits as $trait) {
            $this->assertContains($trait, $genreTraits);
        }
    }

    public function test_casts()
    {
        $casts = ['id' => "string", 'deleted_at' => "datetime"];
        $genre = new Genre();
        $this->assertEquals($casts, $genre->getCasts());
    }

    public function test_increment()
    {
        $genre = new Genre();
        $this->assertFalse($genre->getIncrementing());
    }

    public function test_dates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $genre = new Genre();
        foreach ($dates as $date) {
            $this->assertContains($date, $genre->getDates());
        }
        $this->assertCount(count($dates), $genre->getDates());
    }

}
