<?php

use App\Http\Controllers\Auth\ForgotPassword\ForgotPasswordController;
use App\Http\Controllers\Auth\ForgotPassword\RedirectController;
use App\Http\Controllers\Auth\ForgotPassword\ResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OAuth2\Google\GetUrlRedirectController;
use App\Http\Controllers\Auth\OAuth2\Google\HandleCallbackController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendVerifyUserController;
use App\Http\Controllers\Auth\VerifyUserController;
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
        Route::get('/verify-email/{token}', VerifyUserController::class);
        Route::get('/resend-verify-email', ResendVerifyUserController::class);
        Route::get("/redirect-forgot-password/{token}", RedirectController::class);
        Route::post("/forgot-password", ForgotPasswordController::class);
        Route::post("/reset-password", ResetPasswordController::class);

        Route::get("/google/url", GetUrlRedirectController::class);
        Route::get("/google/callback", HandleCallbackController::class);
    });
});
