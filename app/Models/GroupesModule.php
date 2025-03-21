<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupesModule extends Model
{
    protected $table = 'groupes_modules';
    public $timestamps = true;

    protected $fillable = [
        'groupe_id',
        'module_id',
        'MHT_presentiel_realisées',
        'MHT_sync_realisées',
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
