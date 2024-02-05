<?php

// database/seeders/ElasticsearchMovieSeeder.php

namespace Database\Seeders;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Seeder;

class ElasticsearchMovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = ClientBuilder::create()->setHosts(['http://elasticsearch:9200'])->build();

        \App\Models\Movie::factory(10)->create()->each(function ($movie) use ($client) {
            $this->indexMovie($client, $movie);
        });
    }

    private function indexMovie($client, $movie): void
    {
        $params = [
            'index' => 'movies',
               'body' => [
                'title' => $movie->title,
                'year' => $movie->year,
                'rank' => $movie->rank,
                'description' => $movie->description,

            ],
        ];

        $client->index($params);
    }
}
