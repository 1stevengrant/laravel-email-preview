<?php

use Ghijk\EmailPreview\Http\Controllers\CapturedEmailsController;
use Illuminate\Support\Facades\Route;

$config = config('email-preview.routes');

if (! $config['enabled']) {
    return;
}

Route::prefix($config['prefix'])
    ->middleware($config['middleware'])
    ->name($config['name'].'.')
    ->group(function () {
        Route::get('/', [CapturedEmailsController::class, 'index'])->name('index');
        Route::get('/{uuid}', [CapturedEmailsController::class, 'show'])->name('show');
        Route::delete('/{uuid}', [CapturedEmailsController::class, 'destroy'])->name('destroy');
        Route::delete('/', [CapturedEmailsController::class, 'clear'])->name('clear');
    });
