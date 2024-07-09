<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', function () {
    return 'health-check OK';
});


Route::middleware(["auth:api"])->group(function () {
    Route::prefix("/auth")->group(function () {
        Route::post("/logout", LogoutController::class);
    });
});

Route::group([], function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/login', LoginController::class);
        Route::post('/register', RegisterController::class);
    });
});
