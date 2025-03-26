<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NombreEfmExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(DB::select("
            SELECT 
                form.niveau_formation,
                fl.secteur,
                fl.code_filiere,
                fl.nom_filiere AS Filière,
                form.type_formation,
                form.creneau,
                g.nom_groupe,
                g.effectif_groupe,
                g.annee_formation,
                form.mode_formation,
                m.code_module AS code_module,
                m.nom_module,
                CASE WHEN m.regional = 'N' THEN 'Oui' ELSE 'Non' END,
                CASE WHEN m.regional = 'O' THEN 'Oui' ELSE 'Non' END
            FROM formations form
            JOIN filieres fl ON fl.formation_id = form.id
            JOIN groupes g ON g.filiere_id = fl.id
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
            'EFM Local',
            'EFM Régional',
        ];
    }

    public function title(): string
    {
        return 'Nombre Efm';
    }

    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            
        ];
    }
}
