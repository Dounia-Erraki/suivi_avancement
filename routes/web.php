<?php

use App\Exports\AvancementModuleExport;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\AffectationModuleController;
use App\Http\Controllers\AvancementFormateurController;
use App\Http\Controllers\AvancementSemestreOneController;
use App\Http\Controllers\FormateursRendementController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import', [ImportController::class, 'index'])->name('import.index');
Route::post('/import', [ImportController::class, 'import'])->name('import.process');


Route::get('/export/affectation-modules', [AffectationModuleController::class, 'export']);
Route::get('/export/avancement-formateurs', [AvancementFormateurController::class, 'export']);
Route::get('/export/rendement-formateurs', [FormateursRendementController::class, 'export']);

Route::get('/export/avancement-groupes', [ModuleController::class, 'exportGroupe']);
Route::get('/export/avancement-modules',[ModuleController::class,'exportModule']);

Route::get('/export/avancement-s1', [AvancementSemestreOneController::class, 'export']);
Route::get('/export/nombre-efm',[GroupeController::class,'export']);
