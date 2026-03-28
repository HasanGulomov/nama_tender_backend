<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
Route::get('/tenders/search', [TenderController::class, 'search'])->name('tenders.search');
Route::get('/tenders/filter', [TenderController::class, 'filter'])->name('tenders.filter');
Route::get('/tenders/{id}', [TenderController::class, 'show'])->name('tenders.show');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    
    Route::post('/tenders', [TenderController::class, 'store'])->name('tenders.store');
    Route::put('/tenders/{id}', [TenderController::class, 'update'])->name('tenders.update');
    Route::delete('/tenders/{id}', [TenderController::class, 'destroy'])->name('tenders.destroy');
    
    
    Route::post('/tender/{id}/favorite', [TenderController::class, 'toggleFavorite'])->name('tenders.favorite.toggle');
    Route::get('/favorites', [TenderController::class, 'getFavorite'])->name('tenders.favorite.list');
});