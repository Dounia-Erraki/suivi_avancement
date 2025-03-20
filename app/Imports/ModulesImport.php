<?php

namespace App\Imports;

use App\Models\Formation;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\FormateurModule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class ModulesImport implements ToCollection, WithHeadingRow
{
   
    public function __construct()
    {
        $this->cleanupOldData();
    }
    
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            foreach ($rows as $row) {
                $formation = Formation::firstOrCreate([
                    'niveau_formation' => $row['niveau'] ?? null,
                    'type_formation' => $row['type_de_formation'] ?? null,
                    'creneau' => $row['creneau'] ?? null,
                    'mode_formation' => $row['mode'] ?? null,
                    'date_maj' => isset($row['date_maj']) ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $row['date_maj'])->format('Y-m-d H:i:s') : null,
                ]);

                $filiere = Filiere::firstOrCreate(
                    ['code_filiere' => $row['code_filiere'] ?? null],
                    [
                        'nom_filiere' => $row['filiere'] ?? null,
                        'secteur' => $row['secteur'] ?? null,
                        'formation_id' => $formation->id,
                    ]
                );

                $groupe = Groupe::firstOrCreate(
                    ['nom_groupe' => $row['groupe'] ?? null,
                    'filiere_id' => $filiere->id,
                    ],
                    [
                        'effectif_groupe' => isset($row['effectif_groupe']) ? (int)$row['effectif_groupe'] : null,
                        'annee_formation' => $row['annee_de_formation'] ?? null,
                    ]
                );
                                        
                $formateurPresentielId = null;
                if (!empty($row['mle_affecte_presentiel_actif']) && !empty($row['formateur_affecte_presentiel_actif'])) {
                    $formateurPresentiel = Formateur::firstOrCreate(
                        ['mle_formateur' => $row['mle_affecte_presentiel_actif']],
                        ['nom_formateur' => $row['formateur_affecte_presentiel_actif']]
                    );
                    $formateurPresentielId = $formateurPresentiel->id;
                }
               
                $formateurSyncId = null;
                if (!empty($row['mle_affecte_syn_actif']) && !empty($row['formateur_affecte_syn_actif'])) {
                    $formateurSync = Formateur::firstOrCreate(
                        ['mle_formateur' => $row['mle_affecte_syn_actif']],
                        ['nom_formateur' => $row['formateur_affecte_syn_actif']]
                    );
                    $formateurSyncId = $formateurSync->id;
                }

                $module = Module::firstOrCreate(
                    [
                        'code_module' => $row['code_module'] ?? null,
                        'nom_module' => $row['module'] ?? null,
                    ],
                    [
                        'nb_cc' => $row['nb_cc'] ?? null,
                        'regional' => $row['regional'] === 'O' ? 'O' : 'N',
                        'EFM_realisation' => isset($row['seance_efm']) && $row['seance_efm'] === 'Oui' ? true : false,
                        'EFM_validation' => isset($row['validation_efm']) && $row['validation_efm'] === 'Oui' ? true : false,
                        'MHT_total' => isset($row['mh_totale_drif']) ? (float)$row['mh_totale_drif'] : 0.00,
                        'MHT_presentiel_s1' => isset($row['mhp_s1_drif']) ? (float)$row['mhp_s1_drif'] : 0.00,
                        'MHT_sync_s1' => isset($row['mhsyn_s1_drif']) ? (float)$row['mhsyn_s1_drif'] : 0.00,
                        'MHT_presentiel_s2' => isset($row['mhp_s2_drif']) ? (float)$row['mhp_s2_drif'] : 0.00,
                        'MHT_sync_s2' => isset($row['mhsyn_s2_drif']) ? (float)$row['mhsyn_s2_drif'] : 0.00,
                    ]
                );

                if (!$filiere->modules()->where('modules.id', $module->id)->exists()) {
                    $filiere->modules()->attach($module->id);
                }
                
                FormateurModule::updateOrCreate(
                    [
                        'groupe_id' => $groupe->id,
                        'module_id' => $module->id,
                    ],
                    [
                        'formateur_presentiel_id' => $formateurPresentielId ? $formateurPresentiel->id : null,
                        'formateur_sync_id' => $formateurSyncId ? $formateurSync->id : null,
                        'mh_realisee_presentiel' => isset($row['mh_realisee_presentiel']) ? (float)$row['mh_realisee_presentiel'] : 0.00,
                        'mh_realisee_sync' => isset($row['mh_realisee_sync']) ? (float)$row['mh_realisee_sync'] : 0.00,
                        'mh_affectee_presentiel' => isset($row['mh_affectee_presentiel']) ? (float)$row['mh_affectee_presentiel'] : 0.00,
                        'mh_affectee_sync' => isset($row['mh_affectee_sync']) ? (float)$row['mh_affectee_sync'] : 0.00,
                    ]
                );

                DB::table('groupes_modules')->updateOrInsert(
                    [
                        'groupe_id' => $groupe->id,
                        'module_id' => $module->id,
                    ],
                    [
                        'MHT_presentiel_realisees' => isset($row['mh_realisee_presentiel']) ? (float)$row['mh_realisee_presentiel'] : 0.00,
                        'MHT_sync_realisees' => isset($row['mh_realisee_sync']) ? (float)$row['mh_realisee_sync'] : 0.00,
                    ]
                );
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function cleanupOldData()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            DB::table('groupes_modules')->delete();
            DB::table('filieres_modules')->delete();
            
            FormateurModule::query()->delete();
            Groupe::query()->delete();
            Filiere::query()->delete();
            Module::query()->delete();
            Formateur::query()->delete();
            Formation::query()->delete();
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            throw $e;
        }
    }
}