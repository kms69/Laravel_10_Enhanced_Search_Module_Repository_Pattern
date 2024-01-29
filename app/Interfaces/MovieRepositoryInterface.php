<?php


namespace App\Interfaces;

use App\Models\Movie;

interface MovieRepositoryInterface
{
    public function all();

    public function create(array $data);
    public function associateGenresAndCrew(Movie $movie, array $genreIds, array $crewIds);

    public function search($query, $genreFilter = null, $crewFilter = null, $sortField = null);



    public function find($id);

    public function update($id, array $data);

    public function delete($id);
}
