<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AvancementGroupeExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(DB::select("
        SELECT
            f.niveau_formation,
            fi.secteur,
            fi.code_filiere,
            fi.nom_filiere,
            f.type_formation,
            f.creneau,
            g.nom_groupe,
            g.effectif_groupe,
            g.annee_formation,
            f.mode_formation,
            SUM(m.MHT_total),
            SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees),

            CASE 
                WHEN SUM(m.MHT_total) = 0 THEN 0 
                ELSE SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees) / SUM(m.MHT_total) 
            END, 

            SUM(m.MHT_total) - SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees),

            CASE 
                WHEN SUM(m.MHT_total) = 0 THEN 0 
                ELSE (SUM(m.MHT_total) - SUM(gm.MHT_presentiel_realisees + gm.MHT_sync_realisees)) / SUM(m.MHT_total) 
            END

        FROM groupes g
        JOIN filieres fi ON g.filiere_id = fi.id
        JOIN formations f ON fi.formation_id = f.id
        JOIN groupes_modules gm ON g.id = gm.groupe_id
        JOIN modules m ON gm.module_id = m.id
        GROUP BY g.nom_groupe, f.niveau_formation, fi.secteur, fi.code_filiere, fi.nom_filiere,
            f.type_formation, f.date_maj, f.creneau, g.effectif_groupe, g.annee_formation, f.mode_formation
        ORDER BY g.nom_groupe;
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
            'MH Totale',
            'MH Totale Réalisée',
            '% de Réalisation',
            'Ecart',
            'Ecart en %',
        ];
    }

    public function title(): string
    {
        return 'Avancement par groupe';
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
            'MH Totale',
            'MH Totale Réalisée',
            '% de Réalisation',
            'Ecart',
            'Ecart en %',
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