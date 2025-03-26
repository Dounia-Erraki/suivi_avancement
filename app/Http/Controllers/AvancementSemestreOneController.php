<?php

namespace App\Http\Controllers;

use App\Exports\AvancementS1Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class AvancementSemestreOneController extends Controller
{
    public function show()
    {
        $filiereSubQuery = DB::table('filieres_modules AS fm')
            ->select('module_id', 'filiere_id')
            ->from(DB::raw("(
                SELECT fm.module_id, fm.filiere_id,
                       ROW_NUMBER() OVER (PARTITION BY fm.module_id ORDER BY f.nom_filiere ASC) AS rn
                FROM filieres_modules fm
                INNER JOIN filieres f ON f.id = fm.filiere_id
            ) AS t"))
            ->where('rn', 1);

        // Requête principale
        $results = DB::table('modules AS m')
            ->select([
                'form.date_maj as date',
                'form.niveau_formation AS Niveau',
                'fil.secteur AS Secteur',
                'fil.code_filiere AS Code_Filière',
                'fil.nom_filiere AS Filière',
                'form.type_formation AS Type_de_Formation',
                'form.creneau AS Creneau',
                'g.nom_groupe AS Groupe',
                'g.effectif_groupe AS Effectif_Groupe',
                'g.annee_formation AS Année_de_Formation',
                'form.mode_formation AS Mode_Formation',
                'm.code_module AS Code_Module',
                'm.nom_module AS Module',
                'm.regional AS Régional',
                'm.MHT_presentiel_s1 AS MHP_S1',
                'm.MHT_sync_s1 AS MHSYN_S1',
                DB::raw('(m.MHT_presentiel_s1 + m.MHT_sync_s1) AS MH_Totale_S1'),
                'gm.MHT_presentiel_realisees AS MHP_Realisee_Présentiel',
                'gm.MHT_sync_realisees AS MH_Réalisee_Sync',
                DB::raw('(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees) AS MH_Réalisée_Globale'),
            ])
            ->distinct()
            ->join('groupes_modules AS gm', 'gm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'g.id', '=', 'gm.groupe_id')
            ->joinSub($filiereSubQuery, 'film', 'film.module_id', '=', 'm.id')
            ->join('filieres AS fil', 'fil.id', '=', 'film.filiere_id')
            ->join('formations AS form', 'form.id', '=', 'fil.formation_id')
            ->orderBy('g.nom_groupe')
            ->orderBy('m.code_module')
            ->get();
            //dd($results);
            return response()->json($results);
        //return view('module-report', ['results' => $results]);
    }

    public function export() 
    {
        return Excel::download(new AvancementS1Export, 'avancement-s1.xlsx');
    }
}
