<?php

namespace App\Models;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Movie extends Model
{
    use HasFactory;



    protected $guarded;

    /**
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function addToElasticsearchIndex(): void
    {
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'elasticsearch')])
            ->build();

        $params = [
            'index' => 'movies',
            'id' => $this->id,
            'body' => $this->toElasticsearchArray(),
        ];

        $client->index($params);
    }

    /**
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function removeFromElasticsearchIndex(): void
    {
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'elasticsearch')])
            ->build();

        $params = [
            'index' => 'movies',
            'id' => $this->id,
        ];

        $client->delete($params);
    }

    public function toElasticsearchArray(): array
    {
        $genres = $this->genres->pluck('name')->toArray();
        $crew = $this->crew->pluck('name')->toArray();

        // You might need to adjust the structure based on your pivot tables
        return [
            'title' => $this->title,
            'year' => $this->year,
            'description' => $this->description,
            'genres' => $genres,
            'crew' => $crew,
        ];
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_movie', 'movie_id', 'genre_id');
    }

    public function crew()
    {
        return $this->belongsToMany(Crew::class, 'crew_movie', 'movie_id', 'crew_id');
    }

}
