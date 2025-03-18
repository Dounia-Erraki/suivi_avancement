<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_filiere',
        'nom_filiere',
        'secteur',
        'formation_id',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }

    
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'filieres_modules')
            ->withTimestamps();
    }
}