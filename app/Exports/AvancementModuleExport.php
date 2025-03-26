<?php

namespace App\Exports;

use App\Models\GroupesModule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AvancementModuleExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(DB::select("
           SELECT 
                f_formations.niveau_formation,
                f_filieres.secteur,
                f_filieres.code_filiere,
                f_filieres.nom_filiere,
                f_formations.type_formation,
                f_formations.creneau,
                g.nom_groupe,
                g.effectif_groupe,
                g.annee_formation,
                f_formations.mode_formation,
                m.code_module,
                m.nom_module,
                CASE WHEN m.regional = 1 THEN 'Oui' ELSE 'Non' END,
                m.MHT_total,
                gm.MHT_presentiel_realisees + gm.MHT_sync_realisees,
                CASE 
                    WHEN m.MHT_total = 0 THEN 0 
                    ELSE ((gm.MHT_presentiel_realisees + gm.MHT_sync_realisees) / m.MHT_total)
                END,
                m.MHT_total - (gm.MHT_presentiel_realisees + gm.MHT_sync_realisees),
                CASE 
                    WHEN m.MHT_total = 0 THEN 0  
                    ELSE ((gm.MHT_presentiel_realisees + gm.MHT_sync_realisees) / m.MHT_total)
                END

            FROM formations f_formations
            JOIN filieres f_filieres ON f_filieres.formation_id = f_formations.id
            JOIN groupes g ON g.filiere_id = f_filieres.id
            JOIN groupes_modules gm ON gm.groupe_id = g.id
            JOIN modules m ON m.id = gm.module_id
        "));
    }

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
           'Mode',
           'Code Module',
           'Module',
           'Régional',
           'MH Totale',
           'MH Totale Réalisée',
           '% de Réalisation',
           'Ecart',
           'Ecart en %'
        ];
    }

    public function title(): string
    {
        return 'Avancement par module';
    }

    
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $headings = [
            'Niveau',
           'Secteur',
           'Code Filière',
           'Filière',
           'Type de Formation',
           'Créneau',
           'Groupe',
           'Effectif Groupe',
           'Année de Formation',
           'Mode',
           'Code Module',
           'Module',
           'Régional',
           'MH Totale',
           'MH Totale Réalisée',
           '% de Réalisation',
           'Ecart',
           'Ecart en %'
        ];

        $columns = ['% de Réalisation', 'Ecart en %'];

        foreach ($columns as $column) {
            $columnIndex = array_search($column, $headings);

            if ($columnIndex !== false) {
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
                $sheet->getStyle($columnLetter)->getNumberFormat()->setFormatCode('0.00%');
            }
        }
    }
}
