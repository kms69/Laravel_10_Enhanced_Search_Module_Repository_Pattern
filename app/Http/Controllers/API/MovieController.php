<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovieFormRequest;
use App\Interfaces\MovieRepositoryInterface;
use App\Models\Movie;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MovieController extends Controller
{
    protected MovieRepositoryInterface $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function index()
    {
        $movies = Movie::with('crew', 'genres')->get();

        return response()->json(['movies' => $movies], 200);
    }

    public function store(MovieFormRequest $request)
    {
        $data = $request->validated();

        $movie = $this->movieRepository->create($data);


        $genreIds = $request->input('genre_id', []);
        $crewIds = $request->input('crew_id', []);


        $this->movieRepository->associateGenresAndCrew($movie, $genreIds, $crewIds);

        return response()->json(['message' => 'Movie created successfully']);
    }

    public function show($id)
    {
        $movie = Movie::with('crew', 'genres')->findOrFail($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        return response()->json(['movie' => $movie], 200);
    }

    public function update(MovieFormRequest $request, $id)
    {
        $validatedData = $request->validated();

        $movie = $this->movieRepository->update($id, $validatedData);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $movie->crew()->sync($request->input('crew_id'));
        $movie->genres()->sync($request->input('genre_id'));

        return response()->json(['message' => 'Movie updated successfully'], 200);
    }

    public function destroy($id)
    {
        $result = $this->movieRepository->delete($id);

        if ($result) {
            return response()->json(['message' => 'Movie deleted successfully'], 200);
        }

        return response()->json(['message' => 'Movie not found'], 404);
    }

//    public function search(Request $request)
//
//    {
//        $query = $request->input('query');
////        dd($query);
//        $genreFilter = $request->input('genre');
//
//        $crewFilter = $request->input('crew');
//        $sortField = $request->input('sort');
//
//        $cacheKey = 'search:movies:' . md5(implode(':', [$query, $genreFilter, $crewFilter, $sortField]));
//
//        $results = Cache::remember($cacheKey, 60, function () use ($query, $genreFilter, $crewFilter, $sortField) {
//            $params = [
//                'index' => 'your_index_name',
//                'body' => [
//                    'query' => [
//                        'bool' => [
//                            'must' => [
//                                'match' => [
//                                    'title' => $query,
//                                ],
//                            ],
//                        ],
//                    ],
//                ],
//            ];
//
//            // Apply filters
//            if ($genreFilter) {
//                $params['body']['query']['bool']['must'][] = [
//                    'match' => [
//                        'genres' => $genreFilter,
//                    ],
//                ];
//            }
//
//            if ($crewFilter) {
//                $params['body']['query']['bool']['must'][] = [
//                    'match' => [
//                        'crews' => $crewFilter,
//                    ],
//                ];
//            }
//
//            // Apply sorting
//            if ($sortField) {
//                $params['body']['sort'] = [
//                    $sortField => 'asc',  // or 'desc' based on your sorting preference
//                ];
//            }
//
//            $client = ClientBuilder::create()
//                ->setHosts([env('ELASTICSEARCH_HOST', 'localhost')])
//                ->build();
//
//            $response = $client->search($params);
//
//            // Process and return the results
//            return $response['hits']['hits'];
//        });
//
//        return response()->json(['results' => $results], 200);
//    }
    public function search(Request $request)
    {
        $query = $request->input('query');
        $genreFilter = $request->input('genre');
        $crewFilter = $request->input('crew');
        $sortField = $request->input('sort');

        $results = $this->movieRepository->search($query, $genreFilter, $crewFilter, $sortField);

        return response()->json(['results' => $results], 200);
    }

}
