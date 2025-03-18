<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_module',
        'nom_module',
        'nb_cc',
        'regional',
        'EFM_realisation',
        'EFM_validation',
        'MHT_total',
        'MHT_presentiel_s1',
        'MHT_sync_s1',
        'MHT_presentiel_s2',
        'MHT_sync_s2'
    ];

    public function formateurModules()
    {
        return $this->hasMany(FormateurModule::class);
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'filieres_modules')
            ->withTimestamps();
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'formateurs_modules')
            ->withPivot('formateur_presentiel_id', 'formateur_sync_id', 
                        'mh_realisee_presentiel', 'mh_realisee_sync',
                        'mh_affectee_presentiel', 'mh_affectee_sync')
            ->withTimestamps();
    }  
   
    public function groupesDirect()
    {
        return $this->belongsToMany(Groupe::class, 'groupes_modules')
        ->withPivot('MHT_presentiel_realisees', 'MHT_sync_realisees')
        ->withTimestamps();
    } 
  
}