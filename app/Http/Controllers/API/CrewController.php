<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CrewFormRequest;
use App\Interfaces\CrewRepositoryInterface;

class CrewController extends Controller
{
    protected CrewRepositoryInterface $crewRepository;

    public function __construct(CrewRepositoryInterface $crewRepository)
    {
        $this->crewRepository = $crewRepository;
    }

    public function index()
    {
        $crews = $this->crewRepository->all();
        return response()->json(['crews' => $crews], 200);
    }

    public function store(CrewFormRequest $request)
    {
        $validatedData = $request->validated();

        $crew = $this->crewRepository->create($validatedData);

        return response()->json(['message' => 'Crew created successfully'], 201);
    }

    public function show($id)
    {
        $crew = $this->crewRepository->find($id);
        if (!$crew) {
            return response()->json(['message' => 'Crew not found'], 404);
        }

        return response()->json(['crew' => $crew], 200);
    }

    public function update(CrewFormRequest $request, $id)
    {
        $validatedData = $request->validated();

        $crew = $this->crewRepository->update($id, $validatedData);
        if (!$crew) {
            return response()->json(['message' => 'Crew not found'], 404);
        }

        return response()->json(['message' => 'Crew updated successfully'], 200);
    }

    public function destroy($id)
    {
        $result = $this->crewRepository->delete($id);

        if ($result) {
            return response()->json(['message' => 'Crew deleted successfully'], 200);
        }

        return response()->json(['message' => 'Crew not found'], 404);
    }
}

