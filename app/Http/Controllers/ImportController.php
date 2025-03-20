<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ModulesImport;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // try {
        //     Excel::import(new ModulesImport, $request->file('file'));
        //     return redirect()->back()->with('success', 'Données importées avec succès!');
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        // }
        try {
            Excel::import(new ModulesImport, $request->file('file'));
            return response()->json(['message' => 'Données importées avec succès!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Erreur lors de l'importation: " . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
}