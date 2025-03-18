<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_groupe',
        'effectif_groupe',
        'annee_formation',
        'filiere_id',
    ];

    public function formateurModules()
    {
        return $this->hasMany(FormateurModule::class);
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'formateurs_modules')
            ->withPivot('formateur_presentiel_id', 'formateur_sync_id', 
                        'mh_realisee_presentiel', 'mh_realisee_sync',
                        'mh_affectee_presentiel', 'mh_affectee_sync')
            ->withTimestamps();
    }

    public function modulesDirect()
    {
        return $this->belongsToMany(Module::class, 'groupes_modules')
        ->withPivot('MHT_presentiel_realisees', 'MHT_sync_realisees')
        ->withTimestamps();
    }
      
}