<?php

use App\Http\Controllers\FormateursRendementController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import', [ImportController::class, 'index'])->name('import.index');
Route::post('/import', [ImportController::class, 'import'])->name('import.process');

Route::get('/formateurs/rendement', [FormateursRendementController::class, 'index'])->name('formateurs.rendement');
