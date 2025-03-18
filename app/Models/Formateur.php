<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'mle_formateur',
        'nom_formateur',
    ];

    public function presentielModules()
    {
        return $this->hasMany(FormateurModule::class, 'formateur_presentiel_id');
    }
    
    public function syncModules()
    {
        return $this->hasMany(FormateurModule::class, 'formateur_sync_id');
    }
}