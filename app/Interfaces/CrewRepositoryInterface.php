<?php



namespace App\Interfaces;

interface CrewRepositoryInterface
{
    public function all();

    public function create(array $data);

    public function find($id);

    public function update($id, array $data);

    public function delete($id);
}
