<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovieFormRequest;
use App\Interfaces\MovieRepositoryInterface;
use App\Models\Movie;
use App\Repositories\MovieRepository;
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

    public function store(MovieFormRequest $request)
    {
        $data = $request->validated();

        // Create the movie in your database
        $movie = $this->movieRepository->create($data);

        // Index the movie in Elasticsearch
        $this->indexMovieInElasticsearch($movie);

        // Associate genres and crew in your database
        $genreIds = $request->input('genre_id', []);
        $crewIds = $request->input('crew_id', []);
        $this->movieRepository->associateGenresAndCrew($movie, $genreIds, $crewIds);

        return response()->json(['message' => 'Movie created successfully']);
    }



    protected function indexMovieInElasticsearch(Movie $movie)
    {
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'localhost')])
            ->build();

        // Index the movie in Elasticsearch
        $params = [
            'index' => 'movies',  // Replace with your Elasticsearch index name
            'id' => $movie->id,
            'body' => [
                'title' => $movie->title,
                'year' => $movie->year,

            ],
        ];

        $client->index($params);
    }

    // ... (other methods)

    public function index()
    {
        $movies = Movie::with('crew', 'genres')->get();

        return response()->json(['movies' => $movies], 200);
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

        $this->indexMovieInElasticsearch($movie);

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


    public function search(Request $request, MovieRepository $movieRepository)
    {
        $query = $request->input('query');
        $filters = $request->input('filters', []); // Convert query string to array
        $sorting = $request->input('sorting', []); // Convert query string to array

        $results = $movieRepository->search($query, $filters, $sorting);

        return response()->json($results);
    }
}
