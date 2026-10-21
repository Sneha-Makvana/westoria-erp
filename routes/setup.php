<?php

use App\Http\Controllers\SetupController;
use App\Http\Middleware\PreventSetupWhenInstalled;
use Illuminate\Support\Facades\Route;

Route::middleware([PreventSetupWhenInstalled::class])
    ->prefix('setup')
    ->name('setup.')
    ->group(function () {
        Route::get('/', [SetupController::class, 'index'])->name('index');
        Route::post('/test-database', [SetupController::class, 'testDatabase'])->name('test-database');
        Route::post('/install', [SetupController::class, 'install'])->name('install');
    });
