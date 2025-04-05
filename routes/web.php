<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PlayersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');

    Route::get('players', [PlayersController::class, 'index'])->name('players.index');
    Route::get('players/create', [PlayersController::class, 'create'])->name('players.create');
    Route::post('players', [PlayersController::class, 'store'])->name('players.store');

    Route::post('/games', [GameController::class, 'store'])->name('games.store');
    Route::post('/games/{game}/result', [GameController::class, 'storeResult'])->name('games.result');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
