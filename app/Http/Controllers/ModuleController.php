<?php

namespace App\Http\Controllers;

use App\Exports\AvancementGroupeExport;
use App\Exports\AvancementModuleExport;
use App\Models\GroupesModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class ModuleController extends Controller
{
    public function showdetails(Request $request)
    {
        $modules = GroupesModule::with([
                'groupe.filiere',
                'groupe.filiere.formation',
                'module'
                ])
            ->select(
                'groupe_id',
                'module_id',
                DB::raw("SUM(MHT_presentiel_realisees + MHT_sync_realisees) as total_MHT_realisées")
            )
            ->groupBy('groupe_id', 'module_id')
            ->get();

         return response()->json($modules);
         // return view('AvancementParModule',['modules'=>$modules]);
    }
    public function getAvancementData()
    {
        $results =  DB::select("
        SELECT
            f.niveau_formation AS Niveau,
            fi.secteur AS Secteur,
            fi.code_filiere AS Code_Filiere,
            fi.nom_filiere AS Filiere,
            f.type_formation AS Type_Formation,
            f.date_maj as date_maj,
            f.creneau AS Creneau,
            g.nom_groupe AS Groupe,
            g.effectif_groupe AS Effectif_Groupe,
            g.annee_formation AS Annee_Formation,
            f.mode_formation AS Mode,
            SUM(m.MHT_total) AS MH_Totale,
            SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees) AS MH_Totale_Realisee,
            ROUND((SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees) / SUM(m.MHT_total)) * 100, 2) AS Pourcentage_Realisation,
            SUM(m.MHT_total) - SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees) AS Ecart,
            ROUND((SUM(m.MHT_total) - SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees)) / SUM(m.MHT_total) * 100, 2) AS Ecart_Pourcentage
        FROM
            groupes g
        JOIN
            filieres fi ON g.filiere_id = fi.id
        JOIN
            formations f ON fi.formation_id = f.id
        JOIN
            groupes_modules gm ON g.id = gm.groupe_id
        JOIN
            modules m ON gm.module_id = m.id
        GROUP BY
            g.nom_groupe, f.niveau_formation, fi.secteur, fi.code_filiere, fi.nom_filiere,
            f.type_formation,f.date_maj, f.creneau, g.effectif_groupe, g.annee_formation, f.mode_formation
        ORDER BY
            g.nom_groupe;
    ");
        return response()->json($results);
        //return view('AvencementParGroup', ['data' => $results]);
    }
    public function exportModule() 
    {
        return Excel::download(new AvancementModuleExport, 'avancement-modules.xlsx');
    }
    public function exportGroupe() 
    {
        return Excel::download(new AvancementGroupeExport, 'avancement-groupes.xlsx');
    }

}
