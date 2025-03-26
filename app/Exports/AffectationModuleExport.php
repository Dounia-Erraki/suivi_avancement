<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AffectationModuleExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(DB::select("
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
                m.MHT_presentiel_s1 + m.MHT_sync_s1 AS mh_totale_s1,
                m.MHT_presentiel_s2 AS mhp_S2,
                m.MHT_sync_s2 AS mhsyn_S2,
                m.MHT_presentiel_s2 + m.MHT_sync_s2 AS mh_totale_s2,
                m.MHT_presentiel_s1 + m.MHT_presentiel_s2 AS mhp_totale,
                m.MHT_sync_s1 + m.MHT_sync_s2 AS mhsyn_totale,
                m.MHT_presentiel_s1 + m.MHT_presentiel_s2 + m.MHT_sync_s1 + m.MHT_sync_s2 AS mh_totale
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
                m.MHT_presentiel_s1  AS mh_totale_s1,
                m.MHT_presentiel_s2 AS mhp_S2,
                0 AS mhsyn_S2,
                m.MHT_presentiel_s2 AS mh_totale_s2,
                m.MHT_presentiel_s1 + m.MHT_presentiel_s2 AS mhp_totale,
                0 AS mhsyn_totale,
                m.MHT_presentiel_s1 + m.MHT_presentiel_s2  AS mh_totale
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
                m.MHT_sync_s1 AS mh_totale_s1,
                0 AS mhp_S2,
                m.MHT_sync_s2 AS mhsyn_S2,
                m.MHT_sync_s2 AS mh_totale_s2,
                0 AS mhp_totale,
                m.MHT_sync_s1 + m.MHT_sync_s2 AS mhsyn_totale,
                m.MHT_sync_s1 + m.MHT_sync_s2 AS mh_totale
            FROM formateurs f
            JOIN formateurs_modules fm ON f.id = fm.formateur_sync_id
            JOIN modules m ON fm.module_id = m.id
            JOIN groupes g ON fm.groupe_id = g.id
            JOIN filieres fil ON g.filiere_id = fil.id
            JOIN formations form ON fil.formation_id = form.id
            WHERE (fm.formateur_presentiel_id IS NULL OR fm.formateur_presentiel_id != f.id)
        "));
    }

    public function headings(): array
    {
        return [
            'Mle Formateur',
            'Nom & Prénom Formateur',
            'Filière',
            'Type de Formation',
            'Groupe',
            'Année de Formation',
            'Mode',
            'Code Module',
            'Module',
            'MHP S1',
            'MHSYN S1',
            'MH Totale S1',
            'MHP S2',
            'MHSYN S2',
            'MH Totale S2',
            'MHP Totale',
            'MHSYN Totale',
            'MH Totale',
        ];
    }

    public function title(): string
    {
        return 'Affectation par module';
    }

    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
