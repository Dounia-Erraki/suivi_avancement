<?php

use App\Http\Controllers\AffectationModuleController;
use App\Http\Controllers\FormateursRendementController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/FormateursRendementController', [FormateursRendementController::class, 'index']);
Route::get('/AffectationController', [AffectationModuleController::class, 'index']);
Route::Post('/importData',[ImportController::class,'import']);
