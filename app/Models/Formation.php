<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'niveau_formation',
        'type_formation',
        'mode_formation',
        'creneau',
        'date_maj'
    ];

    public function filieres()
    {
        return $this->hasMany(Filiere::class);
    }
}