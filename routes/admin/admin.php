<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth:admin')->as('admin.')->group(function () {
    // User routes
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';