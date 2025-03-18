<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormateurModule extends Model
{
    use HasFactory;
    
    protected $table = 'formateurs_modules';
    
    protected $fillable = [
        'formateur_presentiel_id', 
        'formateur_sync_id', 
        'groupe_id', 
        'module_id',
        'mh_realisee_presentiel',
        'mh_realisee_sync',
        'mh_affectee_presentiel',
        'mh_affectee_sync'
    ];
    
    public function formateurPresentiel()
    {
        return $this->belongsTo(Formateur::class, 'formateur_presentiel_id');
    }
    
    public function formateurSync()
    {
        return $this->belongsTo(Formateur::class, 'formateur_sync_id');
    }
    
    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
    
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}