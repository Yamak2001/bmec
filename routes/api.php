<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BmedleController;

Route::prefix('bmedle')->middleware('auth:sanctum')->group(function () {
    Route::get('/attempts', [BmedleController::class, 'listAttempts']);
    Route::post('/start', [BmedleController::class, 'startAttempt']);
    Route::get('/attempts/{bmedleAttempt}', [BmedleController::class, 'getAttempt']);
    Route::post('/attempts/{bmedleAttempt}/guess', [BmedleController::class, 'submitGuess']);
    Route::patch('/attempts/{bmedleAttempt}/hint', [BmedleController::class, 'applyHint']);
    Route::delete('/attempts/{bmedleAttempt}', [BmedleController::class, 'deleteAttempt']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
