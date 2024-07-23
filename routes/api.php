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
use App\Http\Controllers\Medias\CreateMediaController;
use App\Http\Controllers\Medias\GetAllMediaController;
use App\Http\Controllers\Medias\GetDetailMediaByIdController;
use App\Http\Controllers\Medias\GetMyMediaController;
use App\Http\Controllers\Users\Profiles\GetMyFollowerOrFolloweeController;
use App\Http\Controllers\Users\Profiles\GetMyProfileController;
use App\Http\Controllers\Users\Profiles\GetProfileUserByIdController;
use App\Http\Controllers\Users\Profiles\UpdateMyProfileController;
use App\Http\Controllers\Users\Relationships\FollowOrBlockController;
use App\Http\Controllers\Users\Relationships\GetFollowerOrFollweeByIdController;
use App\Http\Controllers\Users\Relationships\UnFollowOrUnBlockController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', function () {
    return 'health-check OK';
});


Route::middleware(["auth:api"])->group(function () {
    Route::prefix("/auth")->group(function () {
        Route::post("/logout", LogoutController::class);
    });

    Route::prefix("/users")->group(function () {
        Route::prefix("/relationships")->group(function () {
            Route::get("/", GetMyFollowerOrFolloweeController::class);
            Route::post("/", FollowOrBlockController::class);
            Route::delete("/", UnFollowOrUnBlockController::class);
        });
        Route::prefix("/my-profile")->group(function () {
            Route::get("/", GetMyProfileController::class);
            Route::post("/", UpdateMyProfileController::class);
        });
    });

    Route::prefix("/medias")->group(function () {
        Route::get("/my-media", GetMyMediaController::class);
        Route::post("/", CreateMediaController::class);
        Route::get("{mediaId}/auth", GetDetailMediaByIdController::class);
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

    Route::prefix("/users")->group(function () {
        Route::prefix("/profiles")->group(function () {
            Route::get("/{userId}", GetProfileUserByIdController::class);
        });
        Route::prefix("/relationships")->group(function () {
            Route::get("/{userId}", GetFollowerOrFollweeByIdController::class);
        });
    });

    Route::prefix("/medias")->group(function () {
        Route::get("/all", GetAllMediaController::class);
        Route::get("{mediaId}", GetDetailMediaByIdController::class);
    });
});
