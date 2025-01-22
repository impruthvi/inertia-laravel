<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AuthAuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('admin')->middleware('guest:admin')->as('admin.')->group(function () {
    Route::get('login', [AuthAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthAuthenticatedSessionController::class, 'store'])->name('login');
});

Route::prefix('admin')->middleware('auth:admin')->as('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    Route::post('logout', [AuthAuthenticatedSessionController::class, 'destroy'])->name('logout');

});
