<?php



namespace App\Repositories;

use App\Interfaces\MovieRepositoryInterface;
use App\Models\Movie;

use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;


use Elastic\Elasticsearch\ClientBuilder;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Redis;


class MovieRepository implements MovieRepositoryInterface
{
//    protected $redis;
//
//    public function __construct(Redis $redis)
//    {
//        $this->redis = $redis;
//    }
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


    /**
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */



    public function search(string $query, array $filters = [], array $sorting = []): array
    {
        // Cache logic (you can uncomment this if caching is needed)
        // $cacheKey = md5("movie_search_{$query}_" . serialize(compact('filters', 'sorting')));
        // $results = Cache::get($cacheKey);
        // if ($results) {
        //     return unserialize($results);
        // }

        // Initialize Elasticsearch client
        $client = ClientBuilder::create()
            ->setHosts(['http://elasticsearch:9200'])
            ->build();

        // Build Elasticsearch query (use multi_match query for multiple fields)
        $params = [
            'index' => 'movies',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['your_searchable_field', 'genre', 'crew', 'role'], // Add other fields as needed
                    ],
                ],
            ],
        ];

        // Apply filters
        foreach ($filters as $field => $value) {
            $params['body']['query']['bool']['must'][] = [
                'term' => [
                    $field => $value,
                ],
            ];
        }

        // Apply sorting
        foreach ($sorting as $field => $direction) {
            $params['body']['sort'][] = [
                $field => [
                    'order' => $direction,
                ],
            ];
        }

        // Log the Elasticsearch query
        Log::info('Elasticsearch Query:', ['params' => $params]);

        // Execute Elasticsearch query
        try {
            $response = $client->search($params);
        } catch (\Exception $e) {
            // Log any exception that occurs during the Elasticsearch query
            Log::error('Elasticsearch Exception:', ['exception' => $e->getMessage()]);
            return [];
        }

        // Log the Elasticsearch response
//        Log::info('Elasticsearch Response:', $response);

        // Process and return the results
        $hits = $response['hits']['hits'];
        $results = array_map(function ($hit) {
            return $hit['_source'];
        }, $hits);

        // Cache results with appropriate TTL and error handling
        // try {
        //     Cache::put($cacheKey, serialize($results), 60 * 60); // Cache for 1 hour
        // } catch (\Exception $e) {
        //     Log::error('Error caching search results:', ['error' => $e->getMessage()]);
        //     // Optionally report the error to the user or take other actions as needed
        // }

        return $results;
    }



}
