<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth:admin')->as('admin.')->group(function () {
    // User routes
    Route::resource('users', UserController::class);

    // Role routes
    Route::resource('roles', RoleController::class)->except('show');

    // Admin routes
    Route::resource('admins', AdminController::class);
});

require __DIR__.'/auth.php';
