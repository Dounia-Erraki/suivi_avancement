<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffectationModuleController extends Controller
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
                fil.nom_filiere ,
                form.type_formation ,
                g.nom_groupe ,
                g.annee_formation,
                form.mode_formation,
                m.code_module,
                m.nom_module,
                m.MHT_presentiel_s1 AS mhp_S1,
                m.MHT_sync_s1 AS mhsyn_S1,
                m.MHT_presentiel_s2 AS mhp_S2,
                m.MHT_sync_s2 AS mhsyn_S2
            FROM formateurs f
            JOIN formateurs_modules fm ON (f.id = fm.formateur_presentiel_id AND f.id = fm.formateur_sync_id)
            JOIN modules m ON fm.module_id = m.id
            JOIN groupes g ON fm.groupe_id = g.id
            JOIN filieres fil ON g.filiere_id = fil.id
            JOIN formations form ON fil.formation_id = form.id

            UNION

            SELECT
                f.mle_formateur,
                f.nom_formateur ,
                fil.nom_filiere ,
                form.type_formation ,
                g.nom_groupe ,
                g.annee_formation,
                form.mode_formation ,
                m.code_module ,
                m.nom_module,
                m.MHT_presentiel_s1 AS mhp_S1,
                0 AS mhsyn_S1,
                m.MHT_presentiel_s2 AS mhp_S2,
                0 AS mhsyn_S2
            FROM formateurs f
            JOIN formateurs_modules fm ON f.id = fm.formateur_presentiel_id
            JOIN modules m ON fm.module_id = m.id
            JOIN groupes g ON fm.groupe_id = g.id
            JOIN filieres fil ON g.filiere_id = fil.id
            JOIN formations form ON fil.formation_id = form.id
            WHERE (fm.formateur_sync_id IS NULL OR fm.formateur_sync_id != f.id)

            UNION

            SELECT
                f.mle_formateur,
                f.nom_formateur,
                fil.nom_filiere,
                form.type_formation,
                g.nom_groupe,
                g.annee_formation,
                form.mode_formation,
                m.code_module,
                m.nom_module,
                0 AS mhp_S1,
                m.MHT_sync_s1 AS mhsyn_S1,
                0 AS mhp_S2,
                m.MHT_sync_s2 AS mhsyn_S2
            FROM formateurs f
            JOIN formateurs_modules fm ON f.id = fm.formateur_sync_id
            JOIN modules m ON fm.module_id = m.id
            JOIN groupes g ON fm.groupe_id = g.id
            JOIN filieres fil ON g.filiere_id = fil.id
            JOIN formations form ON fil.formation_id = form.id
            WHERE (fm.formateur_presentiel_id IS NULL OR fm.formateur_presentiel_id != f.id)
        ");

        return response()->json($formateurs);
    }
}
