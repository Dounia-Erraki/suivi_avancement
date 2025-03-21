<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvancementFormateurController extends Controller
{
    public function show(){
        $firstQuery = DB::table('formateurs AS f')
            ->select([
                'form.date_maj as date',
                'f.mle_formateur AS Mle_Formateur',
                'f.nom_formateur AS Nom_Prenom_Formateur',
                'fil.nom_filiere AS Filière',
                'form.type_formation AS Type_de_Formation',
                'g.nom_groupe AS Groupe',
                'g.annee_formation AS Année_de_Formation',
                'form.mode_formation AS Mode',
                'm.code_module AS Code_Module',
                'm.nom_module AS Module',
                'fm.mh_affectee_presentiel AS MHP_Totale',
                'fm.mh_affectee_sync AS MHSYN_Totale',
                'fm.mh_realisee_presentiel AS MHP_Réalisée',
                'fm.mh_realisee_sync AS MHSYN_Réalisée',
            ])
            ->join('formateurs_modules AS fm', function ($join) {
                $join->on('f.id', '=', 'fm.formateur_presentiel_id')
                     ->where('f.id', '=', DB::raw('fm.formateur_sync_id'));
            })
            ->join('modules AS m', 'fm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'fm.groupe_id', '=', 'g.id')
            ->join('filieres AS fil', 'g.filiere_id', '=', 'fil.id')
            ->join('formations AS form', 'fil.formation_id', '=', 'form.id');

        // Second group: Formateurs who are only presentiel
        $secondQuery = DB::table('formateurs AS f')
            ->select([
                'form.date_maj as date',
                'f.mle_formateur AS Mle_Formateur',
                'f.nom_formateur AS Nom_Prenom_Formateur',
                'fil.nom_filiere AS Filière',
                'form.type_formation AS Type_de_Formation',
                'g.nom_groupe AS Groupe',
                'g.annee_formation AS Année_de_Formation',
                'form.mode_formation AS Mode',
                'm.code_module AS Code_Module',
                'm.nom_module AS Module',
                'fm.mh_affectee_presentiel AS MHP_Totale',
                DB::raw('0 AS MHSYN_Totale'),
                'fm.mh_realisee_presentiel AS MHP_Réalisée',
                DB::raw('0 AS MHSYN_Réalisée'),
            ])
            ->join('formateurs_modules AS fm', 'f.id', '=', 'fm.formateur_presentiel_id')
            ->join('modules AS m', 'fm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'fm.groupe_id', '=', 'g.id')
            ->join('filieres AS fil', 'g.filiere_id', '=', 'fil.id')
            ->join('formations AS form', 'fil.formation_id', '=', 'form.id')
            ->where(function ($query) {
                $query->whereNull('fm.formateur_sync_id')
                      ->orWhere('fm.formateur_sync_id', '!=', DB::raw('f.id'));
            });

        // Third group: Formateurs who are only sync
        $thirdQuery = DB::table('formateurs AS f')
            ->select([
                'form.date_maj as date',
                'f.mle_formateur AS Mle_Formateur',
                'f.nom_formateur AS Nom_Prenom_Formateur',
                'fil.nom_filiere AS Filière',
                'form.type_formation AS Type_de_Formation',
                'g.nom_groupe AS Groupe',
                'g.annee_formation AS Année_de_Formation',
                'form.mode_formation AS Mode',
                'm.code_module AS Code_Module',
                'm.nom_module AS Module',
                DB::raw('0 AS MHP_Totale'),
                'fm.mh_affectee_sync AS MHSYN_Totale',
                DB::raw('0 AS MHP_Réalisée'),
                'fm.mh_realisee_sync AS MHSYN_Réalisée',
            ])
            ->join('formateurs_modules AS fm', 'f.id', '=', 'fm.formateur_sync_id')
            ->join('modules AS m', 'fm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'fm.groupe_id', '=', 'g.id')
            ->join('filieres AS fil', 'g.filiere_id', '=', 'fil.id')
            ->join('formations AS form', 'fil.formation_id', '=', 'form.id')
            ->where(function ($query) {
                $query->whereNull('fm.formateur_presentiel_id')
                      ->orWhere('fm.formateur_presentiel_id', '!=', DB::raw('f.id'));
            });

        // Combine the queries with UNION and apply ordering
        $results = $firstQuery
            ->union($secondQuery)
            ->union($thirdQuery)
            ->orderBy('Nom_Prenom_Formateur')
            ->orderBy('Filière')
            ->orderBy('Groupe')
            ->orderBy('Code_Module')
            ->get();

        //dd($results);
        return response()->json($results);
        //return view('formateur-report', ['results' => $results]);
    }
}
