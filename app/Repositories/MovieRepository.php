<?php



namespace App\Repositories;

use App\Interfaces\MovieRepositoryInterface;
use App\Models\Movie;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Cache;

class MovieRepository implements MovieRepositoryInterface
{
    public function all()
    {
        return Movie::all();
    }

    public function create(array $data)
    {
        return Movie::create($data);
    }
    public function associateGenresAndCrew(Movie $movie, array $genreIds, array $crewIds)
    {
        $movie->genres()->sync($genreIds);
        $movie->crew()->sync($crewIds);
    }

    public function find($id)
    {
        return Movie::find($id);
    }

    public function update($id, array $data)
    {
        $movie = Movie::find($id);
        if ($movie) {
            $movie->update($data);
            return $movie;
        }
        return null;
    }

    public function delete($id)
    {
        $movie = Movie::find($id);
        if ($movie) {
            $movie->delete();
            return true;
        }
        return false;
    }
    public function search($query, $genreFilter = null, $crewFilter = null, $sortField = null)
    {
        $cacheKey = $this->generateCacheKey('search:movies:', [$query, $genreFilter, $crewFilter, $sortField]);

        return Cache::remember($cacheKey, 60, function () use ($query, $genreFilter, $crewFilter, $sortField) {
            $params = [
                'index' => 'your_index_name',
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'match' => [
                                        'title' => $query,
                                    ],
                                ],
                                [
                                    'match' => [
                                        'genres' => $genreFilter,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            // Apply sorting
            if ($sortField) {
                $params['body']['sort'] = [
                    $sortField => 'asc',  // or 'desc' based on your sorting preference
                ];
            }

            $client = ClientBuilder::create()
                ->setHosts([env('ELASTICSEARCH_HOST', 'elasticsearch')])
                ->build();

            $response = $client->search($params);

            // Process and return the results
            return $response['hits']['hits'];
        });
    }


    protected function generateCacheKey($prefix, $params)
    {
        return $prefix . md5(implode(':', $params));
    }
}
