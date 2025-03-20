<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormateursRendementController extends Controller
{
    /**
     * Affiche le rapport de rendement des formateurs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        $formateurs = DB::select("
            SELECT 
                f.mle_formateur,
                f.nom_formateur,

                SUM(CASE WHEN f.id = fm.formateur_presentiel_id THEN fm.mh_affectee_presentiel ELSE 0 END) AS mhp_totale,
                SUM(CASE WHEN f.id = fm.formateur_sync_id THEN fm.mh_affectee_sync ELSE 0 END) AS mhsyn_totale,
                
                SUM(CASE WHEN f.id = fm.formateur_presentiel_id THEN fm.mh_realisee_presentiel ELSE 0 END) AS mhp_realisee,
                SUM(CASE WHEN f.id = fm.formateur_sync_id THEN fm.mh_realisee_sync ELSE 0 END) AS mhsyn_realisee
                
            FROM formateurs f
            JOIN formateurs_modules fm ON f.id = fm.formateur_presentiel_id OR f.id = fm.formateur_sync_id
            JOIN modules m ON fm.module_id = m.id
            JOIN groupes g ON fm.groupe_id = g.id
            GROUP BY f.id, f.mle_formateur, f.nom_formateur
            ORDER BY f.nom_formateur
        ");

        return response()->json($formateurs);
    }
}