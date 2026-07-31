<?php

use App\Http\Controllers\SigningController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/sign/{recipient:access_token}', [SigningController::class, 'show'])
    ->name('signing.show');

Route::post('/sign/{recipient:access_token}', [SigningController::class, 'sign'])
    ->name('signing.sign');
