<?php

use App\Http\Controllers\AlbumMedia\GetPrivacyController;
use App\Http\Controllers\Albums\AddMemberIntoAlbumController;
use App\Http\Controllers\Albums\CreateAlbumController;
use App\Http\Controllers\Albums\DeleteAlbumController;
use App\Http\Controllers\Albums\GetDetailAlbumByIdController;
use App\Http\Controllers\Albums\GetMyAlbumController;
use App\Http\Controllers\Albums\GetMyAlbumRoleMemberController;
use App\Http\Controllers\Albums\UpdateAlbumController;
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
use App\Http\Controllers\Medias\DownloadMediaController;
use App\Http\Controllers\Medias\CreateMediaController;
use App\Http\Controllers\Medias\DeleteMediaByIdController;
use App\Http\Controllers\Medias\GetAllMediaController;
use App\Http\Controllers\Medias\GetDetailMediaByIdController;
use App\Http\Controllers\Medias\GetMyMediaController;
use App\Http\Controllers\Medias\ReportMediaController;
use App\Http\Controllers\Medias\SearchMediaByTagIdController;
use App\Http\Controllers\Medias\UpdateMediaController;
use App\Http\Controllers\Feelings\GetAllFeelingController;
use App\Http\Controllers\Medias\GetCommentsOfMediaDetailByIdController;
use App\Http\Controllers\Medias\GetReplyCommentByIdController;
use App\Http\Controllers\Medias\Reactions\CommentMediaController;
use App\Http\Controllers\Medias\Reactions\ReplyCommentController;
use App\Http\Controllers\Medias\Reactions\ToggleReactionCommentController;
use App\Http\Controllers\Medias\Reactions\ToggleReactionMediaController;
use App\Http\Controllers\Medias\Reactions\ToggleReactionReplyController;
use App\Http\Controllers\Tags\GetAllTagController;
use App\Http\Controllers\Users\Reports\GetListReportReasonController;
use App\Http\Controllers\Users\Profiles\GetMyFollowerOrFolloweeController;
use App\Http\Controllers\Users\Profiles\GetMyProfileController;
use App\Http\Controllers\Users\Profiles\GetProfileUserByIdController;
use App\Http\Controllers\Users\Profiles\UpdateMyProfileController;
use App\Http\Controllers\Users\Relationships\FollowOrBlockController;
use App\Http\Controllers\Users\Relationships\GetFollowerOrFollweeByIdController;
use App\Http\Controllers\Users\Relationships\UnFollowOrUnBlockController;
use App\Http\Controllers\Users\Reports\ReportUserController;
use App\Http\Controllers\Users\SearchUserOrTagNameController;
use App\Http\Controllers\Users\SearchUsersController;
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

        Route::prefix("report")->group(function () {
            Route::post("/", ReportUserController::class);
        });
    });

    Route::prefix("/medias")->group(function () {
        Route::get("/my-media", GetMyMediaController::class);
        Route::post("/", CreateMediaController::class);
        Route::put("/{mediaId}", UpdateMediaController::class);
        Route::delete("/", DeleteMediaByIdController::class);

        Route::prefix("report")->group(function () {
            Route::post("/", ReportMediaController::class);
        });

        Route::post("/reactions", ToggleReactionMediaController::class);

        Route::prefix("comment")->group(function () {
            Route::post("/", CommentMediaController::class);
            Route::post("/reactions", ToggleReactionCommentController::class);

            Route::prefix("reply")->group(function () {
                Route::post("/", ReplyCommentController::class);
                Route::post("/reactions", ToggleReactionReplyController::class);
            });
        });
    });

    Route::prefix("/albums")->group(function () {
        Route::get("/medias/privacy", GetPrivacyController::class);
        Route::get("/my-album", GetMyAlbumController::class);
        Route::get("/my-album-member", GetMyAlbumRoleMemberController::class);
        Route::post("/", CreateAlbumController::class);
        Route::put("/{albumId}", UpdateAlbumController::class);
        Route::post("/{albumId}/invite/{userId}", AddMemberIntoAlbumController::class);
        Route::delete("/{albumId}", DeleteAlbumController::class);
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

        Route::prefix("report-reasons")->group(function () {
            Route::get("/", GetListReportReasonController::class);
        });

        Route::get("/search", SearchUserOrTagNameController::class);
        Route::get('/find', SearchUsersController::class);
    });

    Route::prefix("/medias")->group(function () {
        Route::get("/all", GetAllMediaController::class);
        Route::get("/search/{tagId}", SearchMediaByTagIdController::class);
        Route::get("{mediaId}", GetDetailMediaByIdController::class);
        Route::get("/{mediaId}/comments", GetCommentsOfMediaDetailByIdController::class);
        Route::get("/comments/{commentId}/replies", GetReplyCommentByIdController::class);
        Route::post("downloads", DownloadMediaController::class);
    });

    Route::prefix("/albums")->group(function () {
        Route::get("{albumId}", GetDetailAlbumByIdController::class);
    });

    Route::get("/feelings", GetAllFeelingController::class);

    Route::prefix("/tags")->group(function () {
        Route::get("/", GetAllTagController::class);
    });
});
