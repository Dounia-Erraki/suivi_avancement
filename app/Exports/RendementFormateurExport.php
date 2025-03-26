<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RendementFormateurExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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

                SUM(CASE WHEN f.id = fm.formateur_presentiel_id THEN fm.mh_affectee_presentiel ELSE 0 END) +
                SUM(CASE WHEN f.id = fm.formateur_sync_id THEN fm.mh_affectee_sync ELSE 0 END) AS mh_totale,

                SUM(CASE WHEN f.id = fm.formateur_presentiel_id THEN fm.mh_realisee_presentiel ELSE 0 END) +
                SUM(CASE WHEN f.id = fm.formateur_sync_id THEN fm.mh_realisee_sync ELSE 0 END) AS mh_realisee,

                CASE 
                    WHEN 
                        (SUM(CASE WHEN f.id = fm.formateur_presentiel_id THEN fm.mh_affectee_presentiel ELSE 0 END) +
                        SUM(CASE WHEN f.id = fm.formateur_sync_id THEN fm.mh_affectee_sync ELSE 0 END)) = 0 
                    THEN '0.00'
                    ELSE 
                        
                        (SUM(CASE WHEN f.id = fm.formateur_presentiel_id THEN fm.mh_realisee_presentiel ELSE 0 END) +
                        SUM(CASE WHEN f.id = fm.formateur_sync_id THEN fm.mh_realisee_sync ELSE 0 END))/
                        (SUM(CASE WHEN f.id = fm.formateur_presentiel_id THEN fm.mh_affectee_presentiel ELSE 0 END) +
                        SUM(CASE WHEN f.id = fm.formateur_sync_id THEN fm.mh_affectee_sync ELSE 0 END)) 
                        
                END AS rendement

            FROM formateurs f
            JOIN formateurs_modules fm ON f.id = fm.formateur_presentiel_id OR f.id = fm.formateur_sync_id
            JOIN modules m ON fm.module_id = m.id
            JOIN groupes g ON fm.groupe_id = g.id
            GROUP BY f.id, f.mle_formateur, f.nom_formateur
            ORDER BY rendement
        "));
    }

    public function headings(): array
    {
        return [
            'Mle Formateur',
            'Nom & Prénom Formateur',
            'MH Totale',
            'MH Réalisée',
            'Rendement en %',
        ];
    }

    public function title(): string
    {
        return 'Rendement formateur';
    }

    
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->getFont()->setBold(true);
        $headings = [
            'Mle Formateur',
            'Nom & Prénom Formateur',
            'MH Totale',
            'MH Réalisée',
            'Rendement en %',
        ];

        $columnIndex = array_search('Rendement en %', $headings);

        if ($columnIndex !== false) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
            
            $sheet->getStyle($columnLetter)->getNumberFormat()->setFormatCode('0.00%');
        }
            
    }
}
