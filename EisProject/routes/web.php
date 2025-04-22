<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerifyController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/verify-code', [EmailVerifyController::class, 'showVerificationForm'])->name('auth.verify-code');