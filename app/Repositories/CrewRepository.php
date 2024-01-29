<?php



namespace App\Repositories;

use App\Interfaces\CrewRepositoryInterface;
use App\Models\Crew;

class CrewRepository implements CrewRepositoryInterface
{
    public function all()
    {
        return Crew::all();
    }

    public function create(array $data)
    {
        return Crew::create($data);
    }

    public function find($id)
    {
        return Crew::find($id);
    }

    public function update($id, array $data)
    {
        $crew = Crew::find($id);
        if ($crew) {
            $crew->update($data);
            return $crew;
        }
        return null;
    }

    public function delete($id)
    {
        $crew = Crew::find($id);
        if ($crew) {
            $crew->delete();
            return true;
        }
        return false;
    }
}
