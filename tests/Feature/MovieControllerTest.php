<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Movie;

class MovieControllerTest extends TestCase
{
    use RefreshDatabase;



    public function testIndexMethod()
    {
        // Create a test movie in the database
        Movie::factory()->create();

        $response = $this->getJson('/api/movies');

        $response->assertStatus(200)
            ->assertJsonStructure(['movies' => []]);
    }

    public function testShowMethod()
    {
        // Create a test movie in the database
        $movie = Movie::factory()->create();

        $response = $this->getJson("/api/movies/{$movie->id}");

        $response->assertStatus(200)
            ->assertJson(['movie' => []]);
    }

    public function testDestroyMethod()
    {
        // Create a test movie in the database
        $movie = Movie::factory()->create();

        $response = $this->deleteJson("/api/movies/{$movie->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Movie deleted successfully']);
    }
}
