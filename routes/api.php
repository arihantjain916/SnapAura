<?php

use App\Http\Controllers\FollowRequestController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\RequestCheck;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PollController;

Route::get("/", function () {
    return response()->json([
        "status" => "success",
        "message" => "Welcome to SnapAura API",
        "date" => now()
    ]);
});


Route::get("verify/email/{userId}/{token}", [AuthController::class, "verifyEmail"]);
Route::get("resend-email/{email}", [AuthController::class, "resendEmail"]);

Route::group(["prefix" => "auth"], function () {
    Route::post("register", [AuthController::class, "store"]);
    Route::post("login", [AuthController::class, "login"]);
});

Route::group(["prefix" => "user"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::get("profile", [AuthController::class, "profile"]);
        Route::put("update/profile", [AuthController::class, "updateProfile"]);
        Route::get("logout", [AuthController::class, "logout"]);
        Route::get("/post", [PostController::class, "fetchPostofUser"]);
    });
    Route::put("reset/password", [AuthController::class, "passwordReset"]);
});

Route::group(["prefix" => "post"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [PostController::class, "store"]);
        Route::post("/like/{post_id}", [LikeController::class, 'like']);
    });
    Route::get("/", [PostController::class, "display"]);
    Route::get("/{id}", [PostController::class, "specificPost"]);
});

Route::group(["prefix" => "comment"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [CommentController::class, "store"]);
        Route::post("reply", [CommentController::class, "storeReply"]);

    });
    Route::get("/", [CommentController::class, "display"]);
});

Route::group(["prefix" => "poll"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [PollController::class, "store"]);
        Route::post("/vote/{id}/{option}", [PollController::class, "storeUserVote"]);

    });
    Route::get("/", [PollController::class, "display"]);
});


Route::group(["prefix" => "tag"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::get('{tagName}', [TagController::class, 'getPostsByTag']);
        Route::get('/', [TagController::class, 'getAllTags']);
    });
});

Route::group(["prefix" => "follow/request"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::get("/send/{id}", [FollowRequestController::class, "send"]);
        Route::get("/unfollow/{id}", [FollowRequestController::class, "unfollow"])->name("follow.unfollow");
        Route::get("/accept/{id}", [FollowRequestController::class, "accept"])->name("follow.accept");
        Route::get("/reject/{id}", [FollowRequestController::class, "reject"]);
    });
});

Route::group(["prefix" => "oauth"], function () {
    Route::get("/google", [AuthController::class, "handleGoogleLogin"]);
    Route::get("/google/callback", [AuthController::class, "handleGoogleCallback"]);
    Route::get("/github", [AuthController::class, "handleGitHubLogin"]);
    Route::get("/github/callback", [AuthController::class, "handleGitHubCallback"]);
});

Route::group(["middleware" => RequestCheck::class], function () {
    Route::get("/user/info/{id}", [AuthController::class, 'sendUserInfo']);
});

Route::group(["prefix" => "search"], function () {
    Route::get("/{name}", [SearchController::class, 'search']);
    Route::get("/profile/{name}", [SearchController::class, 'searchProfile']);
});

Route::get("/temp", [NotificationController::class, "temp"]);

Route::group(["prefix" => "notification"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::get("/fetch", [NotificationController::class, "fetch"]);
    });
});