<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('login',[AuthController::class, 'login']);
Route::post('store', [UserController::class, 'store']);
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});