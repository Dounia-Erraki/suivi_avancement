<?php

namespace App\Http\Controllers;
use App\Models\GroupesModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;
use App\Exports\NombreEfmExport;
use Maatwebsite\Excel\Facades\Excel;

class GroupeController extends Controller
{
    public function show()
    {
        $results = FacadesDB::table('groupes as g')
            ->leftJoin('filieres as f', 'g.filiere_id', '=', 'f.id')
            ->leftJoin('modules as m', 'f.id', '=', 'm.filiere_id')
            ->leftJoin('avancement_presentiels as ap', function($join) {
                $join->on('g.id', '=', 'ap.groupe_id')
                    ->on('m.id', '=', 'ap.module_id');
            })
            ->leftJoin('fusions as fus', 'g.fusion_id', '=', 'fus.id')
            ->leftJoin('avancement_syncs as asunc', function($join) {
                $join->on('m.id', '=', 'asunc.module_id')
                    ->on('fus.id', '=', 'asunc.fusion_id');
            })
            ->join('formations as fo', 'fo.id', '=', 'f.foramtion_id')
            ->select(
                'fo.formation_niveau as Niveau',
                'f.secteur as Secteur',
                'f.code_filiere as CodeFiliere',
                'f.nom_filiere as Filiere',
                'fo.formation_type as TypeDeFormation',
                'fo.creneau as Creneau',
                'g.nom as Groupe',
                'g.effectif as EffectifGroupe',
                'g.annee_formation as AnneeDeFormation',
                'fo.mode as Mode',
                FacadesDB::raw('SUM(m.MHT_presentiel + m.MHT_sync) as MHTotale1'),
                FacadesDB::raw('COALESCE(SUM(ap.volume_realise), 0) + COALESCE(SUM(asunc.volume_realise), 0) as MHTotaleRealisee2')
            )
            ->groupBy(
                'fo.formation_niveau', 'f.secteur', 'f.code_filiere', 'f.nom_filiere',
                'fo.formation_type', 'fo.creneau', 'g.nom', 'g.effectif', 'g.annee_formation', 'fo.mode'
            )
            ->get();

        return view('AvancementParGroupe',['formation'=>$results]);
    }
    public function showEfmDetails()
    {
        $groupes = GroupesModule::with([
            'groupe.filiere',
            'groupe.filiere.formation',
            'module'
        ])
        ->select(
            'groupe_id',
            'module_id')
        ->groupBy('groupe_id', 'module_id')
        ->get();

    return response()->json($groupes);
    }

    public function export() 
    {
        return Excel::download(new NombreEfmExport, 'nombre-efm.xlsx');
    }
}
