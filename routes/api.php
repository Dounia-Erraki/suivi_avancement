<?php

use App\Http\Controllers\AffectationModuleController;
use App\Http\Controllers\AvancementFormateurController;
use App\Http\Controllers\AvancementSemestreOneController;
use App\Http\Controllers\FormateursRendementController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;


Route::get('avancementSone', [AvancementSemestreOneController::class, 'show']);
Route::get('avancementFormateur', [AvancementFormateurController::class, 'show']);
Route::get('/FormateursRendement', [FormateursRendementController::class, 'index']);
Route::get('/AffectationController', [AffectationModuleController::class, 'index']);
Route::Post('/importData',[ImportController::class,'import']);
Route::get('/AvencementParGroup',[ModuleController::class,'getAvancementData']);
Route::get('/AvancementParModule', [ModuleController::class, 'showdetails']);
Route::get('/NombreEfmParGroup',[GroupeController::class,'showEfmDetails']);

