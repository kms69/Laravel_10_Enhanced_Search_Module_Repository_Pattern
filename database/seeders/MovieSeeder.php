<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Movie::factory(10)->create()->each(function ($movie) {

            $genres = \App\Models\Genre::inRandomOrder()->limit(rand(1, 3))->pluck('id')->toArray();
            $crews = \App\Models\Crew::inRandomOrder()->limit(rand(1, 5))->pluck('id')->toArray();

            $movie->genres()->attach($genres);
            $movie->crew()->attach($crews);
        });
    }

}
