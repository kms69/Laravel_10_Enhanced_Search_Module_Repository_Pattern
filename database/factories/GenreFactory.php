<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Genre::class;

       protected $conventionalGenres = ['Action', 'Drama', 'Comedy', 'Science Fiction', 'Horror'];

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement($this->conventionalGenres),
        ];
    }
}
