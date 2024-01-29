<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crew extends Model
{
    use HasFactory;
    protected $guarded;
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'crew_movie');
    }
}
