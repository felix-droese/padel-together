<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('games.index');
})->name('home');

Route::get('games', [GameController::class, 'index'])
    ->name('games.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');

    Route::get('players', [PlayerController::class, 'index'])->name('players.index');
    Route::get('players/create', [PlayerController::class, 'create'])->name('players.create');
    Route::post('players', [PlayerController::class, 'store'])->name('players.store');
    Route::delete('players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

    Route::post('/games', [GameController::class, 'store'])->name('games.store');
    Route::post('/games/{game}/result', [GameController::class, 'storeResult'])->name('games.result');
    Route::put('/games/{game}/result', [GameController::class, 'updateResult'])->name('games.result.update');
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');

    Route::post('/payments/create', [PaymentController::class, 'create'])
        ->name('payments.create');

    Route::get('/payments/success', [PaymentController::class, 'success'])
        ->name('payments.success');

    Route::get('/payments/cancel', [PaymentController::class, 'cancel'])
        ->name('payments.cancel');

    Route::post('/payments/capture', [PaymentController::class, 'capture'])
        ->name('payments.capture');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
