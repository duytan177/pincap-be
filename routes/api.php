<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', function () {
    return 'health-check OK';
});


Route::middleware(["auth:sanctum", "check.status"])->group(function () {
});

Route::group([], function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/login', LoginController::class);
    });
});
