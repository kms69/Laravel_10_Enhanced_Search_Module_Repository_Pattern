<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreFormRequest;
use App\Interfaces\GenreRepositoryInterface;

class GenreController extends Controller
{
    protected GenreRepositoryInterface $genreRepository;

    public function __construct(GenreRepositoryInterface $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    public function index()
    {
        $genres = $this->genreRepository->all();
        return response()->json(['genres' => $genres], 200);
    }

    public function store(GenreFormRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();

        $genre = $this->genreRepository->create($validatedData);

        return response()->json(['message' => 'Genre created successfully'], 201);
    }

    public function show($id)
    {
        $genre = $this->genreRepository->find($id);
        if (!$genre) {
            return response()->json(['message' => 'Genre not found'], 404);
        }

        return response()->json(['genre' => $genre], 200);
    }

    public function update(GenreFormRequest $request, $id)
    {
        $validatedData = $request->validated();

        $genre = $this->genreRepository->update($id, $validatedData);
        if (!$genre) {
            return response()->json(['message' => 'Genre not found'], 404);
        }

        return response()->json(['message' => 'Genre updated successfully'], 200);
    }

    public function destroy($id)
    {
        $result = $this->genreRepository->delete($id);

        if ($result) {
            return response()->json(['message' => 'Genre deleted successfully'], 200);
        }

        return response()->json(['message' => 'Genre not found'], 404);
    }
}

