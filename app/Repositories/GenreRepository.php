<?php



namespace App\Repositories;

use App\Interfaces\GenreRepositoryInterface;
use App\Models\Genre;

class GenreRepository implements GenreRepositoryInterface
{
    public function all()
    {
        return Genre::all();
    }

    public function create(array $data)
    {
        return Genre::create($data);
    }

    public function find($id)
    {
        return Genre::find($id);
    }

    public function update($id, array $data)
    {
        $genre = Genre::find($id);
        if ($genre) {
            $genre->update($data);
            return $genre;
        }
        return null;
    }

    public function delete($id)
    {
        $genre = Genre::find($id);
        if ($genre) {
            $genre->delete();
            return true;
        }
        return false;
    }
}
