<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes pour la gestion des salles de réunion
    Route::resource('salles', App\Http\Controllers\SalleController::class);
    
    // Routes pour la gestion des réservations
    Route::get('/reservations/mes-reservations', [App\Http\Controllers\ReservationController::class, 'mesReservations'])->name('reservations.mes-reservations');
    Route::resource('reservations', App\Http\Controllers\ReservationController::class);
    
    // Routes pour le tableau de bord administrateur
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        // La route /admin/stats n'est plus nécessaire car les stats sont maintenant directement chargées dans le dashboard
    });
});

require __DIR__.'/auth.php';
