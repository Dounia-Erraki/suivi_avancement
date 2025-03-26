<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AvancementS1Export implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $filiereSubQuery = DB::table('filieres_modules AS fm')
            ->select('fm.module_id', 'fm.filiere_id')
            ->join('filieres AS f', 'f.id', '=', 'fm.filiere_id')
            ->orderBy('f.nom_filiere')
            ->groupBy('fm.module_id', 'fm.filiere_id');

        $results = DB::table('modules AS m')
            ->select([
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
                DB::raw('m.MHT_presentiel_s1 + m.MHT_sync_s1 AS MH_Totale_S1'), 
                'gm.MHT_presentiel_realisees AS MHP_Realisee_Présentiel',
                'gm.MHT_sync_realisees AS MH_Réalisee_Sync',
                DB::raw('gm.MHT_presentiel_realisees + gm.MHT_sync_realisees AS MH_Réalisée_Globale'), 
            ])
            ->distinct()
            ->join('groupes_modules AS gm', 'gm.module_id', '=', 'm.id')
            ->join('groupes AS g', 'g.id', '=', 'gm.groupe_id')
            ->joinSub($filiereSubQuery, 'film', function ($join) {
                $join->on('film.module_id', '=', 'm.id');
            })
            ->join('filieres AS fil', 'fil.id', '=', 'film.filiere_id')
            ->join('formations AS form', 'form.id', '=', 'fil.formation_id')
            ->orderBy('g.nom_groupe')
            ->orderBy('m.code_module')
            ->get();

        return collect($results);
    }

    /**
     * Retourne les en-têtes du fichier Excel.
     */
    public function headings(): array
    {
        return [
            'Niveau',
            'Secteur',
            'Code Filière',
            'Filière',
            'Type de Formation',
            'Créneau',
            'Groupe',
            'Effectif Groupe',
            'Année de Formation',
            'Mode Formation',
            'Code Module',
            'Module',
            'Régional',
            'MHP S1',
            'MHSYN S1',
            'MH Totale S1',
            'MH Réalisée Présentiel',
            'MH Réalisée Sync',
            'MH Réalisée Globale',
        ];
    }

    public function title(): string
    {
        return 'Avancement S1';
    }

    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
