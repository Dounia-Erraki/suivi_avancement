<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AvancementFormateurExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        $firstQuery = DB::table('formateurs AS f')
            ->select([
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
                DB::raw('fm.mh_affectee_presentiel + fm.mh_affectee_sync AS MH_Totale'),
                'fm.mh_realisee_presentiel AS MHP_Réalisée',
                'fm.mh_realisee_sync AS MHSYN_Réalisée',
                DB::raw('fm.mh_realisee_presentiel + fm.mh_realisee_sync AS MH_Réalisée'),
                DB::raw('
                    CASE 
                        WHEN (fm.mh_affectee_presentiel + fm.mh_affectee_sync) = 0 THEN 0
                        ELSE ((fm.mh_realisee_presentiel + fm.mh_realisee_sync) ) / 
                        (fm.mh_affectee_presentiel + fm.mh_affectee_sync)
                    END AS Pourcentage_Realisation
                ')
            ])
            ->join('formateurs_modules AS fm', function ($join) {
                $join->on('f.id', '=', 'fm.formateur_presentiel_id')
                     ->whereColumn('f.id', '=', 'fm.formateur_sync_id');
            })
            ->join('modules AS m', 'fm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'fm.groupe_id', '=', 'g.id')
            ->join('filieres AS fil', 'g.filiere_id', '=', 'fil.id')
            ->join('formations AS form', 'fil.formation_id', '=', 'form.id');

        $secondQuery = DB::table('formateurs AS f')
            ->select([
                'f.mle_formateur',
                'f.nom_formateur',
                'fil.nom_filiere',
                'form.type_formation',
                'g.nom_groupe',
                'g.annee_formation',
                'form.mode_formation',
                'm.code_module',
                'm.nom_module',
                'fm.mh_affectee_presentiel AS MHP_Totale',
                DB::raw('0 AS MHSYN_Totale'),
                DB::raw('fm.mh_affectee_presentiel AS MH_Totale'),
                'fm.mh_realisee_presentiel AS MHP_Réalisée',
                DB::raw('0 AS MHSYN_Réalisée'),
                DB::raw('fm.mh_realisee_presentiel AS MH_Réalisée'),
                DB::raw('
                    CASE 
                        WHEN fm.mh_affectee_presentiel = 0 THEN 0
                        ELSE (fm.mh_realisee_presentiel ) / fm.mh_affectee_presentiel
                    END AS Pourcentage_Realisation
                ')
            ])
            ->join('formateurs_modules AS fm', 'f.id', '=', 'fm.formateur_presentiel_id')
            ->join('modules AS m', 'fm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'fm.groupe_id', '=', 'g.id')
            ->join('filieres AS fil', 'g.filiere_id', '=', 'fil.id')
            ->join('formations AS form', 'fil.formation_id', '=', 'form.id')
            ->where(function ($query) {
                $query->whereNull('fm.formateur_sync_id')
                      ->orWhereColumn('fm.formateur_sync_id', '!=', 'f.id');
            });

        $thirdQuery = DB::table('formateurs AS f')
            ->select([
                'f.mle_formateur',
                'f.nom_formateur',
                'fil.nom_filiere',
                'form.type_formation',
                'g.nom_groupe',
                'g.annee_formation',
                'form.mode_formation',
                'm.code_module',
                'm.nom_module',
                DB::raw('0 AS MHP_Totale'),
                'fm.mh_affectee_sync AS MHSYN_Totale',
                DB::raw('fm.mh_affectee_sync AS MH_Totale'),
                DB::raw('0 AS MHP_Réalisée'),
                'fm.mh_realisee_sync AS MHSYN_Réalisée',
                DB::raw('fm.mh_realisee_sync AS MH_Réalisée'),
                DB::raw('
                    CASE 
                        WHEN fm.mh_affectee_sync = 0 THEN 0
                        ELSE (fm.mh_realisee_sync ) / fm.mh_affectee_sync
                    END AS Pourcentage_Realisation
                ')
            ])
            ->join('formateurs_modules AS fm', 'f.id', '=', 'fm.formateur_sync_id')
            ->join('modules AS m', 'fm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'fm.groupe_id', '=', 'g.id')
            ->join('filieres AS fil', 'g.filiere_id', '=', 'fil.id')
            ->join('formations AS form', 'fil.formation_id', '=', 'form.id')
            ->where(function ($query) {
                $query->whereNull('fm.formateur_presentiel_id')
                      ->orWhereColumn('fm.formateur_presentiel_id', '!=', 'f.id');
            });

        $results = $firstQuery
            ->union($secondQuery)
            ->union($thirdQuery)
            ->orderBy('Nom_Prenom_Formateur')
            ->orderBy('Filière')
            ->orderBy('Groupe')
            ->orderBy('Code_Module')
            ->get();

        return collect($results);
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
            'MHP Totale',
            'MHSYN Totale',
            'MH Totale',
            'MHP Réalisée',
            'MHSYN Réalisée',
            'MH Réalisée',
            '% de Réalisation',
        ];
    }

    public function title(): string
    {
        return 'Avancement par formateur';
    }

    
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $headings = [
            'Mle Formateur',
            'Nom & Prénom Formateur',
            'Filière',
            'Type de Formation',
            'Groupe',
            'Année de Formation',
            'Mode',
            'Code Module',
            'Module',
            'MHP Totale',
            'MHSYN Totale',
            'MH Totale',
            'MHP Réalisée',
            'MHSYN Réalisée',
            'MH Réalisée',
            '% de Réalisation',
        ];

        $columnIndex = array_search('% de Réalisation', $headings);

        if ($columnIndex !== false) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
            
            $sheet->getStyle($columnLetter)->getNumberFormat()->setFormatCode('0.00%');
        }
    }
}
