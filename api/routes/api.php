<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\StandingController;
use App\Http\Controllers\SimulationController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


    Route::prefix('teams')->group(function () {
        Route::get('/', [TeamController::class, 'index']);
        Route::post('/', [TeamController::class, 'store']);
    });
    
    Route::prefix('fixtures')->group(function () {
        Route::post('/generate', [FixtureController::class, 'generateFixtures']);
        Route::get('/', [FixtureController::class, 'index']);
        Route::get('/current-week', [FixtureController::class, 'getCurrentWeek']);
    });
    
    Route::prefix('standings')->group(function () {
        Route::get('/', [StandingController::class, 'index']);
        Route::get('/predictions', [StandingController::class, 'getChampionshipPredictions']);
    });
    
    Route::prefix('simulation')->group(function () {
        Route::post('/play-week/{week_id}', [SimulationController::class, 'playWeek']);
        Route::post('/play-all-weeks', [SimulationController::class, 'playAllWeeks']);
        Route::post('/reset', [SimulationController::class, 'resetData']);
    });

    



