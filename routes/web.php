<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminLoginController;

/*
|--------------------------------------------------------------------------
| Admin Auth (Login)
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/admin/login', [AdminLoginController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Panel (Protected)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware('admin.auth')->group(function () {

    // Redirect base /admin
    Route::get('/', fn () => redirect()->route('admin.participants'));

    // PARTICIPANTES (quem é a pessoa)
    Route::get('/participants', [AdminController::class, 'participants'])
        ->name('admin.participants');

    Route::post('/participants', [AdminController::class, 'storeParticipant'])
        ->name('admin.participants.store');

    // PARTICIPAÇÕES (dinheiro da pessoa)
    Route::post('/participants/{participantId}/participation', [AdminController::class, 'addParticipation'])
        ->name('admin.participations.store');

    // SIMULAÇÃO
    Route::get('/simulate', [AdminController::class, 'simulateGames'])
        ->name('admin.simulate');

    // FECHAR BOLÃO
    Route::post('/close', [AdminController::class, 'closePool'])
        ->name('admin.close');

    // JOGOS
    Route::get('/games', [AdminController::class, 'games'])
        ->name('admin.games');

    Route::post('/games/{gameId}/confirm', [AdminController::class, 'confirmGame'])
        ->name('admin.games.confirm');

    // COMPROVANTES
    Route::post('/games/{gameId}/receipt', [AdminController::class, 'uploadReceipt'])
        ->name('admin.games.receipt');

    Route::post('/games/generate', [AdminController::class, 'generateGames'])
    ->name('admin.games.generate');

});

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'));
