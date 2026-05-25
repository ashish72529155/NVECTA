<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NoteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Notes API Routes
Route::prefix('notes')->group(function () {
    Route::get('/', [NoteController::class, 'index']);
    Route::post('/', [NoteController::class, 'store']);
    Route::get('/{id}', [NoteController::class, 'show']);
    Route::put('/{id}', [NoteController::class, 'update']);
    Route::delete('/{id}', [NoteController::class, 'destroy']);
    Route::post('/{id}/summary', [NoteController::class, 'summary']);
});
